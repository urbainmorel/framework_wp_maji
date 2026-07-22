<?php
/**
 * Import des contenus d'un dossier seed (STI §6, `wp maji import-content`).
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Dna;

/**
 * Importe content.json + médias : chambres, plats, remplacement des
 * marqueurs `data-maji-media` dans les pages. `alt` requis sur les médias.
 */
final class ContentImporter {

	/**
	 * Importe un dossier seed complet.
	 *
	 * @param string $dir Dossier contenant content.json et media/.
	 * @return array{errors: string[], imported: array<string, int>} Résultat.
	 */
	public function import( string $dir ): array {
		$errors   = [];
		$imported = [
			'rooms'  => 0,
			'dishes' => 0,
			'media'  => 0,
		];

		$file = rtrim( $dir, '/' ) . '/content.json';
		if ( ! file_exists( $file ) ) {
			return [
				'errors'   => [ sprintf( 'content.json introuvable dans %s.', $dir ) ],
				'imported' => $imported,
			];
		}
		$json = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fichier seed local.
		$data = json_decode( (string) $json, true );
		if ( ! is_array( $data ) ) {
			return [
				'errors'   => [ 'content.json : JSON invalide.' ],
				'imported' => $imported,
			];
		}

		// 1. Médias : clé → pièce jointe.
		$media_map = [];
		$media     = is_array( $data['media'] ?? null ) ? $data['media'] : [];
		foreach ( $media as $key => $item ) {
			if ( ! is_array( $item ) || ! isset( $item['file'] ) ) {
				continue;
			}
			$alt = (string) ( $item['alt'] ?? '' );
			if ( '' === $alt ) {
				$errors[] = sprintf( 'media.%s : attribut alt requis.', (string) $key );
				continue;
			}
			$path = rtrim( $dir, '/' ) . '/media/' . (string) $item['file'];
			if ( ! file_exists( $path ) ) {
				$errors[] = sprintf( 'media.%s : fichier %s introuvable.', (string) $key, (string) $item['file'] );
				continue;
			}
			$attachment_id = $this->sideload( $path, $alt );
			if ( $attachment_id > 0 ) {
				$media_map[ (string) $key ] = $attachment_id;
				++$imported['media'];
			} else {
				$errors[] = sprintf( 'media.%s : import impossible.', (string) $key );
			}
		}

		// 2. Chambres.
		$rooms = is_array( $data['rooms'] ?? null ) ? $data['rooms'] : [];
		foreach ( $rooms as $room ) {
			if ( ! is_array( $room ) ) {
				continue;
			}
			$room_id = $this->import_room( $room, $media_map );
			if ( $room_id > 0 ) {
				++$imported['rooms'];
			} else {
				$errors[] = sprintf( 'Chambre « %s » : import impossible.', (string) ( $room['title'] ?? '?' ) );
			}
		}

		// 3. Plats (WooCommerce requis).
		$dishes = is_array( $data['dishes'] ?? null ) ? $data['dishes'] : [];
		if ( [] !== $dishes && ! class_exists( 'WooCommerce' ) ) {
			$errors[] = 'Des plats sont définis mais WooCommerce n\'est pas actif.';
		} else {
			foreach ( $dishes as $dish ) {
				if ( ! is_array( $dish ) ) {
					continue;
				}
				$dish_id = $this->import_dish( $dish, $media_map );
				if ( $dish_id > 0 ) {
					++$imported['dishes'];
				} else {
					$errors[] = sprintf( 'Plat « %s » : import impossible.', (string) ( $dish['name'] ?? '?' ) );
				}
			}
		}

		// 4. Remplacement des médias marqués dans les pages publiées.
		$this->replace_page_media( $media_map );

		return [
			'errors'   => $errors,
			'imported' => $imported,
		];
	}

	/**
	 * Vérifie qu'aucun jeton {{maji:*}} ne reste dans les pages publiées.
	 *
	 * @return string[] Erreurs listant les occurrences.
	 */
	public function check_residual_tokens(): array {
		$errors = [];
		$pages  = get_posts(
			[
				'post_type'      => 'page',
				'posts_per_page' => 100,
				'post_status'    => 'publish',
			]
		);
		foreach ( $pages as $page ) {
			$residual = Tokens::find( (string) $page->post_content );
			if ( [] !== $residual ) {
				$errors[] = sprintf(
					'Page « %s » : jetons résiduels %s.',
					$page->post_name,
					implode( ', ', $residual )
				);
			}
		}
		return $errors;
	}

	/**
	 * Importe un fichier image dans la médiathèque.
	 *
	 * @param string $path Chemin local.
	 * @param string $alt  Texte alternatif (requis).
	 * @return int ID de pièce jointe (0 en échec).
	 */
	private function sideload( string $path, string $alt ): int {
		$allowed = [ 'jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'avif' ];
		$ext     = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
		if ( ! in_array( $ext, $allowed, true ) ) {
			return 0;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Réutilise une pièce jointe déjà importée du même nom (idempotence).
		$existing = get_posts(
			[
				'post_type'      => 'attachment',
				'posts_per_page' => 1,
				'title'          => sanitize_file_name( basename( $path ) ),
				'fields'         => 'ids',
			]
		);
		if ( isset( $existing[0] ) ) {
			return (int) $existing[0];
		}

		$tmp = wp_tempnam( basename( $path ) );
		copy( $path, $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_copy -- copie locale temporaire.

		$file_array = [
			'name'     => sanitize_file_name( basename( $path ) ),
			'tmp_name' => $tmp,
		];

		$attachment_id = media_handle_sideload( $file_array, 0, null );
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink -- nettoyage temporaire.
			return 0;
		}
		update_post_meta( (int) $attachment_id, '_wp_attachment_image_alt', $alt );
		return (int) $attachment_id;
	}

	/**
	 * Crée/actualise une chambre.
	 *
	 * @param array<string, mixed> $room      Données.
	 * @param array<string, int>   $media_map Clés média → IDs.
	 * @return int ID (0 en échec).
	 */
	private function import_room( array $room, array $media_map ): int {
		$title = (string) ( $room['title'] ?? '' );
		if ( '' === $title ) {
			return 0;
		}
		$slug     = sanitize_title( $title );
		$existing = get_page_by_path( $slug, OBJECT, 'maji_room' );

		$args = [
			'post_type'    => 'maji_room',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => (string) ( $room['content'] ?? '' ),
			'post_excerpt' => (string) ( $room['excerpt'] ?? '' ),
		];
		if ( $existing instanceof \WP_Post ) {
			$args['ID'] = $existing->ID;
		}
		$post_id = wp_insert_post( $args, true );
		if ( is_wp_error( $post_id ) ) {
			return 0;
		}
		$post_id = (int) $post_id;

		foreach ( [ 'price_from', 'capacity', 'size_sqm' ] as $meta ) {
			if ( isset( $room[ $meta ] ) ) {
				update_post_meta( $post_id, $meta, absint( $room[ $meta ] ) );
			}
		}
		if ( isset( $room['featured'] ) ) {
			update_post_meta( $post_id, 'featured', (bool) $room['featured'] );
		}
		if ( isset( $room['amenities'] ) && is_array( $room['amenities'] ) ) {
			wp_set_object_terms( $post_id, array_map( 'strval', $room['amenities'] ), 'maji_amenity' );
		}
		$image_key = (string) ( $room['image'] ?? '' );
		if ( '' !== $image_key && isset( $media_map[ $image_key ] ) ) {
			set_post_thumbnail( $post_id, $media_map[ $image_key ] );
		}
		return $post_id;
	}

	/**
	 * Crée/actualise un plat (produit WooCommerce simple).
	 *
	 * @param array<string, mixed> $dish      Données.
	 * @param array<string, int>   $media_map Clés média → IDs.
	 * @return int ID (0 en échec).
	 */
	private function import_dish( array $dish, array $media_map ): int {
		$name = (string) ( $dish['name'] ?? '' );
		if ( '' === $name ) {
			return 0;
		}
		$slug     = sanitize_title( $name );
		$existing = get_page_by_path( $slug, OBJECT, 'product' );

		$product = $existing instanceof \WP_Post ? wc_get_product( $existing->ID ) : null;
		if ( ! $product instanceof \WC_Product ) {
			$product = new \WC_Product_Simple();
		}
		$product->set_name( $name );
		$product->set_slug( $slug );
		$product->set_status( 'publish' );
		$product->set_short_description( (string) ( $dish['description'] ?? '' ) );
		$product->set_regular_price( (string) ( $dish['price'] ?? '0' ) );

		$image_key = (string) ( $dish['image'] ?? '' );
		if ( '' !== $image_key && isset( $media_map[ $image_key ] ) ) {
			$product->set_image_id( $media_map[ $image_key ] );
		}
		$product_id = $product->save();
		if ( $product_id <= 0 ) {
			return 0;
		}

		$category = (string) ( $dish['category'] ?? '' );
		if ( '' !== $category ) {
			wp_set_object_terms( $product_id, [ $category ], 'product_cat' );
		}
		update_post_meta( $product_id, 'maji_available', empty( $dish['available'] ) && isset( $dish['available'] ) ? '0' : '1' );
		$badges = is_array( $dish['badges'] ?? null ) ? array_map( 'strval', $dish['badges'] ) : [];
		update_post_meta( $product_id, 'maji_badges', array_values( array_intersect( $badges, [ 'populaire', 'epice', 'nouveau' ] ) ) );

		return $product_id;
	}

	/**
	 * Remplace les images marquées `data-maji-media` dans les pages publiées.
	 *
	 * @param array<string, int> $media_map Clés média → IDs de pièces jointes.
	 */
	private function replace_page_media( array $media_map ): void {
		if ( [] === $media_map ) {
			return;
		}
		$pages = get_posts(
			[
				'post_type'      => 'page',
				'posts_per_page' => 100,
				'post_status'    => 'publish',
			]
		);
		foreach ( $pages as $page ) {
			$content  = (string) $page->post_content;
			$original = $content;
			foreach ( $media_map as $key => $attachment_id ) {
				$url = wp_get_attachment_image_url( $attachment_id, 'full' );
				if ( ! is_string( $url ) ) {
					continue;
				}
				// <img src="…" … data-maji-media="key">.
				$content = (string) preg_replace(
					'/(<img[^>]*?)src="[^"]*"([^>]*?data-maji-media="' . preg_quote( (string) $key, '/' ) . '")/',
					'$1src="' . esc_url( $url ) . '"$2',
					$content
				);
				// Variante attribut avant src.
				$content = (string) preg_replace(
					'/(<img[^>]*?data-maji-media="' . preg_quote( (string) $key, '/' ) . '"[^>]*?)src="[^"]*"/',
					'$1src="' . esc_url( $url ) . '"',
					$content
				);
				// Fond de cover parallaxe : <div … data-maji-media="key" … background-image:url(…)>.
				$content = (string) preg_replace(
					'/(background-image:url\()[^)]*(\)[^>]*data-maji-media="' . preg_quote( (string) $key, '/' ) . '")/',
					'$1' . esc_url( $url ) . '$2',
					$content
				);
			}
			if ( $content !== $original ) {
				wp_update_post(
					[
						'ID'           => $page->ID,
						'post_content' => $content,
					]
				);
			}
		}
	}
}
