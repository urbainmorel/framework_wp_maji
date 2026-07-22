<?php
/**
 * Application d'un ADN au site (STI §5.2) — idempotent.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Dna;

use MAJI\Core\Settings\Settings;

/**
 * Applique identity → réglages, design → global styles utilisateur,
 * structure → pages/menu. Interdiction absolue d'écrire dans les
 * fichiers du thème.
 */
final class DnaApplier {

	/**
	 * Constructeur.
	 *
	 * @param Settings $settings Réglages.
	 */
	public function __construct( private readonly Settings $settings ) {}

	/**
	 * Applique l'ADN complet. Retourne la liste des erreurs (vide = succès).
	 *
	 * @param array<string, mixed> $dna ADN validé.
	 * @return string[] Erreurs.
	 */
	public function apply( array $dna ): array {
		$errors = [];

		$this->apply_identity( $dna );
		$this->apply_design( is_array( $dna['design'] ?? null ) ? $dna['design'] : [] );
		$errors = array_merge( $errors, $this->apply_structure( $dna ) );

		update_option( 'maji_dna', $dna, false );

		return $errors;
	}

	/**
	 * Applique identity + features + delivery_zones + integrations aux réglages.
	 * Génère le webhook_secret s'il n'existe pas.
	 *
	 * @param array<string, mixed> $dna ADN.
	 */
	public function apply_identity( array $dna ): void {
		$identity = is_array( $dna['identity'] ?? null ) ? $dna['identity'] : [];

		$features       = is_array( $identity['features'] ?? null ) ? $identity['features'] : [];
		$delivery_zones = is_array( $identity['delivery_zones'] ?? null ) ? $identity['delivery_zones'] : [];
		unset( $identity['features'], $identity['delivery_zones'] );

		$integrations = is_array( $dna['integrations'] ?? null ) ? $dna['integrations'] : [];

		$settings = [
			'establishment'  => $identity,
			'features'       => $features,
			'delivery_zones' => $delivery_zones,
			'integrations'   => [
				'n8n_order_url'       => (string) ( $integrations['n8n_order_url'] ?? '' ),
				'n8n_reservation_url' => (string) ( $integrations['n8n_reservation_url'] ?? '' ),
			],
		];

		$this->settings->replace( $settings );
		$this->settings->webhook_secret(); // Génère le secret si absent (jamais dans l'ADN).

		// Nom et slogan du site.
		if ( isset( $identity['name'] ) && is_string( $identity['name'] ) ) {
			update_option( 'blogname', $identity['name'] );
		}
	}

	/**
	 * Applique design → style variation + global styles utilisateur.
	 *
	 * @param array<string, mixed> $design Bloc design.
	 */
	public function apply_design( array $design ): void {
		$pair_id   = (string) ( $design['font_pair'] ?? '' );
		$font_pair = function_exists( 'maji_framework_get_font_pair' )
			? maji_framework_get_font_pair( $pair_id )
			: null;

		$fragment = GlobalStyles::build( $design, $font_pair );

		$post_id = \WP_Theme_JSON_Resolver::get_user_global_styles_post_id();

		wp_update_post(
			[
				'ID'           => $post_id,
				'post_content' => (string) wp_json_encode( $fragment ),
			]
		);

		// Nettoyage des caches de global styles.
		if ( function_exists( 'wp_clean_theme_json_cache' ) ) {
			wp_clean_theme_json_cache();
		}
	}

	/**
	 * Applique structure → pages avec patterns (jetons résolus), menu, header/footer.
	 *
	 * @param array<string, mixed> $dna ADN.
	 * @return string[] Erreurs (jetons non résolus, patterns introuvables).
	 */
	public function apply_structure( array $dna ): array {
		$errors    = [];
		$structure = is_array( $dna['structure'] ?? null ) ? $dna['structure'] : [];
		$pages     = is_array( $structure['pages'] ?? null ) ? $structure['pages'] : [];
		$vocab     = (string) ( $structure['nav_vocabulary'] ?? '' );

		$token_data = [
			'identity' => is_array( $dna['identity'] ?? null ) ? $dna['identity'] : [],
			'content'  => is_array( $dna['content'] ?? null ) ? $dna['content'] : [],
			'texts'    => is_array( $dna['content']['texts'] ?? null ) ? $dna['content']['texts'] : [],
		];

		$registry   = \WP_Block_Patterns_Registry::get_instance();
		$menu_items = [];

		foreach ( $pages as $page_def ) {
			if ( ! is_array( $page_def ) ) {
				continue;
			}
			$slug     = (string) ( $page_def['slug'] ?? '' );
			$title    = (string) ( $page_def['title'] ?? '' );
			$sections = is_array( $page_def['sections'] ?? null ) ? $page_def['sections'] : [];

			$content = '';
			foreach ( $sections as $pattern_slug ) {
				$pattern_slug = (string) $pattern_slug;
				$pattern      = $registry->get_registered( $pattern_slug );
				if ( null === $pattern ) {
					$errors[] = sprintf( 'Pattern introuvable : %s (page %s).', $pattern_slug, $slug );
					continue;
				}
				$content .= "\n" . (string) $pattern['content'];
			}

			$content = Tokens::resolve( $content, $token_data );

			$residual = Tokens::find( $content );
			if ( [] !== $residual ) {
				$errors[] = sprintf(
					'Jetons non résolus sur la page « %s » : %s.',
					$slug,
					implode( ', ', array_map( static fn( string $t ): string => '{{maji:' . $t . '}}', $residual ) )
				);
			}

			$page_id = $this->upsert_page( $slug, $title, $content );
			if ( 0 === $page_id ) {
				$errors[] = sprintf( 'Impossible de créer la page « %s ».', $slug );
				continue;
			}

			$menu_items[] = [
				'page_id' => $page_id,
				'label'   => NavVocabulary::label( $vocab, $slug, $title ),
			];

			if ( 'accueil' === $slug ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $page_id );
			}
		}

		$this->apply_navigation( $menu_items );
		$this->apply_template_parts( $structure );

		return $errors;
	}

	/**
	 * Crée ou met à jour une page par slug.
	 *
	 * @param string $slug    Slug.
	 * @param string $title   Titre.
	 * @param string $content Contenu (blocs).
	 * @return int ID de page (0 en échec).
	 */
	private function upsert_page( string $slug, string $title, string $content ): int {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		$args     = [
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
		];
		if ( $existing instanceof \WP_Post ) {
			$args['ID'] = $existing->ID;
		}
		$result = wp_insert_post( $args, true );
		return is_wp_error( $result ) ? 0 : (int) $result;
	}

	/**
	 * Reconstruit le menu de navigation (bloc wp_navigation).
	 *
	 * @param array<int, array{page_id: int, label: string}> $items Éléments.
	 */
	private function apply_navigation( array $items ): void {
		if ( [] === $items ) {
			return;
		}
		$content = '';
		foreach ( $items as $item ) {
			$content .= sprintf(
				'<!-- wp:navigation-link {"label":"%s","type":"page","id":%d,"url":"%s","kind":"post-type"} /-->' . "\n",
				esc_attr( $item['label'] ),
				$item['page_id'],
				esc_url( (string) get_permalink( $item['page_id'] ) )
			);
		}

		$existing = get_posts(
			[
				'post_type'      => 'wp_navigation',
				'posts_per_page' => 1,
				'post_status'    => 'publish',
			]
		);

		$args = [
			'post_type'    => 'wp_navigation',
			'post_status'  => 'publish',
			'post_title'   => __( 'Navigation principale', 'maji-core' ),
			'post_content' => $content,
		];
		if ( isset( $existing[0] ) ) {
			$args['ID'] = $existing[0]->ID;
		}
		wp_insert_post( $args );
	}

	/**
	 * Applique les variantes header/footer aux templates du site.
	 *
	 * Les templates du thème référencent `header-02` / `footer-01` par
	 * défaut ; on crée des templates personnalisés (wp_template) qui
	 * pointent vers les variantes choisies — sans toucher aux fichiers.
	 *
	 * @param array<string, mixed> $structure Structure de l'ADN.
	 */
	private function apply_template_parts( array $structure ): void {
		$header = (string) ( $structure['header'] ?? 'header-02' );
		$footer = (string) ( $structure['footer'] ?? 'footer-01' );
		if ( 'header-02' === $header && 'footer-01' === $footer ) {
			return; // Défauts du thème : rien à surcharger.
		}

		$theme = get_stylesheet();

		// Parcourt TOUS les templates du thème (fichiers + base) et surcharge
		// ceux qui référencent le header/footer par défaut, pour une cohérence
		// visuelle complète (accueil, pages, panier, checkout, 404, chambres…).
		$templates = function_exists( 'get_block_templates' )
			? get_block_templates( [], 'wp_template' )
			: [];

		foreach ( $templates as $template ) {
			// Ne traite que les templates du thème actif.
			if ( $theme !== $template->theme ) {
				continue;
			}
			$template_slug = $template->slug;
			$source        = $template->content;
			if ( '' === $template_slug || '' === $source ) {
				continue;
			}

			$content = str_replace(
				[ '"slug":"header-02"', '"slug":"footer-01"' ],
				[ '"slug":"' . $header . '"', '"slug":"' . $footer . '"' ],
				$source
			);
			if ( $content === $source ) {
				continue; // Ce template n'utilise pas les valeurs par défaut.
			}

			// Crée/met à jour le template personnalisé en base (sans toucher aux fichiers).
			$existing = get_posts(
				[
					'post_type'      => 'wp_template',
					'name'           => $template_slug,
					'posts_per_page' => 1,
					'post_status'    => 'publish',
					'tax_query'      => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- provision ponctuelle.
						[
							'taxonomy' => 'wp_theme',
							'field'    => 'name',
							'terms'    => $theme,
						],
					],
				]
			);

			$args = [
				'post_type'    => 'wp_template',
				'post_status'  => 'publish',
				'post_name'    => $template_slug,
				'post_title'   => $template_slug,
				'post_content' => $content,
				'tax_input'    => [ 'wp_theme' => [ $theme ] ],
			];
			if ( isset( $existing[0] ) ) {
				$args['ID'] = $existing[0]->ID;
			}
			wp_insert_post( $args );
		}
	}
}
