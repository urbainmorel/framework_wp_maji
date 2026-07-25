<?php
/**
 * Fabrique de fragments JSON-LD (V2-H1) — logique pure et testable.
 *
 * Le balisage ne doit refléter QUE le contenu visible : les questions/réponses
 * sont extraites du contenu réel de la page (blocs `core/details`), jamais
 * inventées. Aucune dépendance WordPress ici pour rester testable.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Seo;

/**
 * Transformations pures produisant des tableaux JSON-LD.
 */
final class SeoSchema {

	/**
	 * Extrait les couples question/réponse des blocs `<details>` d'un contenu.
	 *
	 * @param string $html Contenu HTML de la page.
	 * @return array<int, array{q: string, a: string}> Paires FAQ (dans l'ordre).
	 */
	public static function faq_from_html( string $html ): array {
		$faqs = [];
		if ( ! preg_match_all( '#<details\b[^>]*>(.*?)</details>#is', $html, $blocks ) ) {
			return $faqs;
		}
		foreach ( $blocks[1] as $inner ) {
			if ( ! preg_match( '#<summary\b[^>]*>(.*?)</summary>#is', $inner, $qm ) ) {
				continue;
			}
			if ( ! preg_match( '#<p\b[^>]*>(.*?)</p>#is', $inner, $am ) ) {
				continue;
			}
			$question = self::clean( $qm[1] );
			$answer   = self::clean( $am[1] );
			if ( '' !== $question && '' !== $answer ) {
				$faqs[] = [
					'q' => $question,
					'a' => $answer,
				];
			}
		}
		return $faqs;
	}

	/**
	 * Construit le schéma `FAQPage` (vide si aucune FAQ).
	 *
	 * @param array<int, array{q: string, a: string}> $faqs Paires FAQ.
	 * @return array<string, mixed> Schéma JSON-LD ou tableau vide.
	 */
	public static function faq_page( array $faqs ): array {
		if ( [] === $faqs ) {
			return [];
		}
		$entities = [];
		foreach ( $faqs as $qa ) {
			$entities[] = [
				'@type'          => 'Question',
				'name'           => $qa['q'],
				'acceptedAnswer' => [
					'@type' => 'Answer',
					'text'  => $qa['a'],
				],
			];
		}
		return [
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $entities,
		];
	}

	/**
	 * Fragment `Offer` pour une chambre (prix optionnel).
	 *
	 * @param string $name     Nom de la chambre.
	 * @param int    $price    Prix « à partir de » (0 = non communiqué).
	 * @param string $currency Devise ISO 4217.
	 * @return array<string, mixed> Offre JSON-LD.
	 */
	public static function room_offer( string $name, int $price, string $currency ): array {
		$offer = [
			'@type' => 'Offer',
			'name'  => $name,
		];
		if ( $price > 0 ) {
			$offer['priceSpecification'] = [
				'@type'         => 'PriceSpecification',
				'price'         => $price,
				'priceCurrency' => $currency,
			];
		}
		return $offer;
	}

	/**
	 * Fragment `LocationFeatureSpecification` pour un équipement.
	 *
	 * @param string $name Libellé de l'équipement.
	 * @return array<string, mixed> Équipement JSON-LD.
	 */
	public static function amenity_feature( string $name ): array {
		return [
			'@type' => 'LocationFeatureSpecification',
			'name'  => $name,
			'value' => true,
		];
	}

	/**
	 * Nettoie un extrait HTML en texte simple.
	 *
	 * @param string $html Fragment HTML.
	 */
	private static function clean( string $html ): string {
		$text = html_entity_decode( strip_tags( $html ), ENT_QUOTES, 'UTF-8' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- classe pure sans WordPress ; texte issu de summary/p.
		// Normalise les espaces (y compris insécables U+00A0) en espace simple.
		$text = (string) preg_replace( '#\s+#u', ' ', str_replace( "\xc2\xa0", ' ', $text ) );
		return trim( $text );
	}
}
