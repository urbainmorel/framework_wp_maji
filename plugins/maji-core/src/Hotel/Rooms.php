<?php
/**
 * CPT `maji_room` : chambres d'hôtel.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Hotel;

/**
 * Type de contenu Chambre + taxonomie Équipements + méta.
 */
final class Rooms {

	public const POST_TYPE = 'maji_room';

	public const TAXONOMY = 'maji_amenity';

	/**
	 * Branche les hooks du module.
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'init', [ $this, 'register_taxonomy' ] );
		add_action( 'init', [ $this, 'register_meta' ] );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', [ $this, 'admin_columns' ] );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ $this, 'admin_column_content' ], 10, 2 );
	}

	/**
	 * Déclare le type de contenu Chambre.
	 */
	public function register_post_type(): void {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'       => [
					'name'               => __( 'Chambres', 'maji-core' ),
					'singular_name'      => __( 'Chambre', 'maji-core' ),
					'add_new'            => __( 'Ajouter une chambre', 'maji-core' ),
					'add_new_item'       => __( 'Ajouter une chambre', 'maji-core' ),
					'edit_item'          => __( 'Modifier la chambre', 'maji-core' ),
					'new_item'           => __( 'Nouvelle chambre', 'maji-core' ),
					'view_item'          => __( 'Voir la chambre', 'maji-core' ),
					'search_items'       => __( 'Rechercher une chambre', 'maji-core' ),
					'not_found'          => __( 'Aucune chambre trouvée', 'maji-core' ),
					'not_found_in_trash' => __( 'Aucune chambre dans la corbeille', 'maji-core' ),
					'all_items'          => __( 'Toutes les chambres', 'maji-core' ),
				],
				'public'       => true,
				'has_archive'  => true,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-bed',
				'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
				'rewrite'      => [ 'slug' => 'chambres' ],
			]
		);
	}

	/**
	 * Déclare la taxonomie Équipements.
	 */
	public function register_taxonomy(): void {
		register_taxonomy(
			self::TAXONOMY,
			self::POST_TYPE,
			[
				'labels'       => [
					'name'          => __( 'Équipements', 'maji-core' ),
					'singular_name' => __( 'Équipement', 'maji-core' ),
					'add_new_item'  => __( 'Ajouter un équipement', 'maji-core' ),
					'edit_item'     => __( 'Modifier l\'équipement', 'maji-core' ),
					'search_items'  => __( 'Rechercher un équipement', 'maji-core' ),
				],
				'public'       => true,
				'hierarchical' => false,
				'show_in_rest' => true,
				'rewrite'      => [ 'slug' => 'equipements' ],
			]
		);
	}

	/**
	 * Déclare les métadonnées de chambre (REST + sanitisation stricte).
	 */
	public function register_meta(): void {
		register_post_meta(
			self::POST_TYPE,
			'price_from',
			[
				'type'              => 'integer',
				'single'            => true,
				'default'           => 0,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
			]
		);
		register_post_meta(
			self::POST_TYPE,
			'capacity',
			[
				'type'              => 'integer',
				'single'            => true,
				'default'           => 2,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
			]
		);
		register_post_meta(
			self::POST_TYPE,
			'size_sqm',
			[
				'type'              => 'integer',
				'single'            => true,
				'default'           => 0,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
			]
		);
		register_post_meta(
			self::POST_TYPE,
			'gallery',
			[
				'type'              => 'array',
				'single'            => true,
				'default'           => [],
				'show_in_rest'      => [
					'schema' => [
						'type'  => 'array',
						'items' => [ 'type' => 'integer' ],
					],
				],
				'sanitize_callback' => static fn( $value ): array => array_map( 'absint', is_array( $value ) ? $value : [] ),
				'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
			]
		);
		register_post_meta(
			self::POST_TYPE,
			'featured',
			[
				'type'              => 'boolean',
				'single'            => true,
				'default'           => false,
				'show_in_rest'      => true,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
			]
		);
	}

	/**
	 * Colonnes personnalisées de la liste admin.
	 *
	 * @param array<string, string> $columns Colonnes existantes.
	 * @return array<string, string> Colonnes modifiées.
	 */
	public function admin_columns( array $columns ): array {
		$date = $columns['date'] ?? '';
		unset( $columns['date'] );
		$columns['maji_price']    = __( 'Prix (à partir de)', 'maji-core' );
		$columns['maji_capacity'] = __( 'Capacité', 'maji-core' );
		if ( '' !== $date ) {
			$columns['date'] = $date;
		}
		return $columns;
	}

	/**
	 * Contenu des colonnes personnalisées.
	 *
	 * @param string $column  Identifiant de colonne.
	 * @param int    $post_id ID du contenu.
	 */
	public function admin_column_content( string $column, int $post_id ): void {
		if ( 'maji_price' === $column ) {
			$price = (int) get_post_meta( $post_id, 'price_from', true );
			echo $price > 0
				? esc_html( number_format_i18n( $price ) . ' FCFA' )
				: esc_html__( '—', 'maji-core' );
		}
		if ( 'maji_capacity' === $column ) {
			$capacity = (int) get_post_meta( $post_id, 'capacity', true );
			/* translators: %d : nombre de personnes. */
			echo esc_html( sprintf( _n( '%d personne', '%d personnes', $capacity, 'maji-core' ), $capacity ) );
		}
	}
}
