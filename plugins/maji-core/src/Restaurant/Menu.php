<?php
/**
 * Requêtes de menu (plats = produits WooCommerce).
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Restaurant;

/**
 * Accès aux plats pour les blocs d'affichage du menu.
 */
final class Menu {

	/**
	 * Récupère les plats disponibles.
	 *
	 * @param int    $count    Nombre maximal.
	 * @param string $category Slug de catégorie produit (vide = toutes).
	 * @param string $badge    Badge MAJI requis (populaire, epice, nouveau ; vide = tous).
	 * @return array<int, array<string, mixed>> Plats normalisés pour l'affichage.
	 */
	public static function dishes( int $count = 12, string $category = '', string $badge = '' ): array {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return [];
		}

		$args = [
			'status'  => 'publish',
			'limit'   => max( 1, min( 48, $count ) ),
			'orderby' => 'menu_order title',
			'order'   => 'ASC',
		];
		if ( '' !== $category ) {
			$args['category'] = [ $category ];
		}

		$products = wc_get_products( $args );
		$dishes   = [];
		foreach ( $products as $product ) {
			if ( ! $product instanceof \WC_Product ) {
				continue;
			}
			$available = get_post_meta( $product->get_id(), 'maji_available', true );
			if ( '0' === $available ) {
				continue;
			}
			$badges = get_post_meta( $product->get_id(), 'maji_badges', true );
			$badges = is_array( $badges ) ? $badges : [];
			if ( '' !== $badge && ! in_array( $badge, $badges, true ) ) {
				continue;
			}
			$dishes[] = [
				'id'          => $product->get_id(),
				'name'        => $product->get_name(),
				'description' => $product->get_short_description(),
				'price'       => $product->get_price(),
				'price_html'  => $product->get_price_html(),
				'image_id'    => (int) $product->get_image_id(),
				'permalink'   => $product->get_permalink(),
				'badges'      => $badges,
				'categories'  => wc_get_product_category_list( $product->get_id() ),
			];
		}
		return $dishes;
	}

	/**
	 * Libellés français des badges.
	 *
	 * @return array<string, string> Libellés par badge.
	 */
	public static function badge_labels(): array {
		return [
			'populaire' => __( 'Populaire', 'maji-core' ),
			'epice'     => __( 'Épicé', 'maji-core' ),
			'nouveau'   => __( 'Nouveau', 'maji-core' ),
		];
	}

	/**
	 * Catégories de menu (product_cat) avec leurs plats.
	 *
	 * @param int $per_category Plats par catégorie.
	 * @return array<int, array{term: \WP_Term, dishes: array<int, array<string, mixed>>}> Catégories.
	 */
	public static function by_categories( int $per_category = 12 ): array {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return [];
		}
		$terms = get_terms(
			[
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
			]
		);
		if ( is_wp_error( $terms ) ) {
			return [];
		}
		$result = [];
		foreach ( $terms as $term ) {
			if ( ! $term instanceof \WP_Term || 'uncategorized' === $term->slug ) {
				continue;
			}
			$dishes = self::dishes( $per_category, $term->slug );
			if ( [] === $dishes ) {
				continue;
			}
			$result[] = [
				'term'   => $term,
				'dishes' => $dishes,
			];
		}
		return $result;
	}
}
