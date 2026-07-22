<?php
/**
 * SEO technique de base (F-C6, STI §4.7).
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Seo;

use MAJI\Core\Modes\ModeManager;
use MAJI\Core\Settings\Settings;

/**
 * Titres/meta, Open Graph et JSON-LD depuis les réglages.
 * Se désactive si un plugin SEO majeur est actif.
 */
final class Seo {

	/**
	 * Constructeur.
	 *
	 * @param Settings    $settings Réglages.
	 * @param ModeManager $modes    Modes.
	 */
	public function __construct(
		private readonly Settings $settings,
		private readonly ModeManager $modes
	) {}

	/**
	 * Branche les hooks si aucun plugin SEO majeur n'est détecté.
	 */
	public function register(): void {
		if ( $this->has_seo_plugin() ) {
			return;
		}
		add_action( 'wp_head', [ $this, 'meta_tags' ], 1 );
		add_action( 'wp_head', [ $this, 'json_ld' ], 2 );
		add_filter( 'document_title_parts', [ $this, 'title_parts' ] );
	}

	/**
	 * Un plugin SEO majeur est-il actif ?
	 */
	public function has_seo_plugin(): bool {
		return defined( 'WPSEO_VERSION' )
			|| class_exists( 'RankMath' )
			|| defined( 'SEOPRESS_VERSION' );
	}

	/**
	 * Titre : « Page — Établissement ».
	 *
	 * @param array<string, string> $parts Segments du titre.
	 * @return array<string, string> Segments modifiés.
	 */
	public function title_parts( array $parts ): array {
		$name = (string) $this->settings->get( 'establishment.name', '' );
		if ( '' !== $name ) {
			$parts['site'] = $name;
		}
		return $parts;
	}

	/**
	 * Meta description + Open Graph + Twitter Card.
	 */
	public function meta_tags(): void {
		$name        = (string) $this->settings->get( 'establishment.name', '' );
		$description = $this->description();

		if ( '' !== $description ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
			printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
		}
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $name ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( wp_get_document_title() ) );
		printf( '<meta property="og:type" content="website">' . "\n" );
		printf( '<meta property="og:locale" content="fr_FR">' . "\n" );

		$url = is_singular() ? get_permalink() : home_url( add_query_arg( [], '' ) );
		if ( is_string( $url ) ) {
			printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		}

		$image = $this->og_image();
		if ( '' !== $image ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		}
		printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
	}

	/**
	 * JSON-LD Hotel / Restaurant / LocalBusiness sur la page d'accueil.
	 */
	public function json_ld(): void {
		if ( ! is_front_page() ) {
			return;
		}
		$type = 'LocalBusiness';
		if ( $this->modes->is_hotel() && ! $this->modes->is_restaurant() ) {
			$type = 'Hotel';
		} elseif ( $this->modes->is_restaurant() && ! $this->modes->is_hotel() ) {
			$type = 'Restaurant';
		}

		$address = $this->settings->get( 'establishment.address', [] );
		$hours   = $this->settings->get( 'establishment.hours', [] );
		$socials = $this->settings->get( 'establishment.socials', [] );

		$schema = [
			'@context'  => 'https://schema.org',
			'@type'     => $type,
			'name'      => (string) $this->settings->get( 'establishment.name', '' ),
			'telephone' => (string) $this->settings->get( 'establishment.phone', '' ),
			'email'     => (string) $this->settings->get( 'establishment.email', '' ),
			'url'       => home_url( '/' ),
		];

		if ( is_array( $address ) ) {
			$schema['address'] = [
				'@type'           => 'PostalAddress',
				'streetAddress'   => trim( (string) ( $address['street'] ?? '' ) . ', ' . (string) ( $address['district'] ?? '' ), ', ' ),
				'addressLocality' => (string) ( $address['city'] ?? '' ),
				'addressCountry'  => (string) ( $address['country'] ?? '' ),
			];
		}

		if ( is_array( $hours ) ) {
			$day_map = [
				'mon' => 'Monday',
				'tue' => 'Tuesday',
				'wed' => 'Wednesday',
				'thu' => 'Thursday',
				'fri' => 'Friday',
				'sat' => 'Saturday',
				'sun' => 'Sunday',
			];
			$specs   = [];
			foreach ( $day_map as $key => $day_name ) {
				$ranges = isset( $hours[ $key ] ) && is_array( $hours[ $key ] ) ? $hours[ $key ] : [];
				foreach ( $ranges as $range ) {
					if ( is_array( $range ) && count( $range ) >= 2 ) {
						$specs[] = [
							'@type'     => 'OpeningHoursSpecification',
							'dayOfWeek' => $day_name,
							'opens'     => (string) $range[0],
							'closes'    => (string) $range[1],
						];
					}
				}
			}
			if ( [] !== $specs ) {
				$schema['openingHoursSpecification'] = $specs;
			}
		}

		if ( is_array( $socials ) ) {
			$same_as = array_values( array_filter( array_map( 'strval', $socials ) ) );
			if ( [] !== $same_as ) {
				$schema['sameAs'] = $same_as;
			}
		}

		if ( 'Restaurant' === $type ) {
			$schema['servesCuisine']      = __( 'Cuisine ouest-africaine', 'maji-core' );
			$schema['currenciesAccepted'] = (string) $this->settings->get( 'establishment.currency', 'XOF' );
		}

		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encodé.
		);
	}

	/**
	 * Description : extrait de la page ou description par défaut.
	 */
	private function description(): string {
		if ( is_singular() ) {
			$post = get_post();
			if ( null !== $post && '' !== $post->post_excerpt ) {
				return wp_strip_all_tags( $post->post_excerpt );
			}
		}
		$name = (string) $this->settings->get( 'establishment.name', '' );
		$city = (string) $this->settings->get( 'establishment.address.city', '' );
		if ( '' === $name ) {
			return '';
		}
		if ( $this->modes->is_restaurant() && ! $this->modes->is_hotel() ) {
			/* translators: 1 : nom, 2 : ville. */
			return sprintf( __( '%1$s — restaurant à %2$s. Consultez la carte et commandez en ligne, paiement à la livraison.', 'maji-core' ), $name, $city );
		}
		/* translators: 1 : nom, 2 : ville. */
		return sprintf( __( '%1$s — à %2$s. Découvrez nos chambres et réservez votre séjour.', 'maji-core' ), $name, $city );
	}

	/**
	 * Image Open Graph : image mise en avant ou logo.
	 */
	private function og_image(): string {
		if ( is_singular() && has_post_thumbnail() ) {
			$url = get_the_post_thumbnail_url( null, 'large' );
			if ( is_string( $url ) ) {
				return $url;
			}
		}
		$logo_id = (int) get_theme_mod( 'custom_logo' );
		if ( $logo_id > 0 ) {
			$url = wp_get_attachment_image_url( $logo_id, 'large' );
			if ( is_string( $url ) ) {
				return $url;
			}
		}
		return '';
	}
}
