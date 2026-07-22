<?php
/**
 * CPT `maji_reservation` : demandes de réservation (chambres et tables).
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Hotel;

/**
 * Type de contenu Réservation + méta + admin (colonnes, filtres, actions).
 */
final class Reservations {

	public const POST_TYPE = 'maji_reservation';

	public const STATUSES = [ 'new', 'confirmed', 'declined', 'cancelled' ];

	/**
	 * Branche les hooks du module.
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'init', [ $this, 'register_meta' ] );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', [ $this, 'admin_columns' ] );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ $this, 'admin_column_content' ], 10, 2 );
		add_action( 'restrict_manage_posts', [ $this, 'status_filter_dropdown' ] );
		add_action( 'pre_get_posts', [ $this, 'apply_status_filter' ] );
		add_action( 'admin_post_maji_reservation_status', [ $this, 'handle_status_action' ] );
		add_filter( 'post_row_actions', [ $this, 'row_actions' ], 10, 2 );
	}

	/**
	 * Déclare le type de contenu Réservation (non public).
	 */
	public function register_post_type(): void {
		register_post_type(
			self::POST_TYPE,
			[
				'labels'          => [
					'name'          => __( 'Réservations', 'maji-core' ),
					'singular_name' => __( 'Réservation', 'maji-core' ),
					'edit_item'     => __( 'Détail de la demande', 'maji-core' ),
					'search_items'  => __( 'Rechercher une demande', 'maji-core' ),
					'not_found'     => __( 'Aucune demande', 'maji-core' ),
					'all_items'     => __( 'Toutes les demandes', 'maji-core' ),
				],
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'show_in_rest'    => false,
				'menu_icon'       => 'dashicons-calendar-alt',
				'supports'        => [ 'title' ],
				'capability_type' => 'post',
				'capabilities'    => [ 'create_posts' => 'do_not_allow' ],
				'map_meta_cap'    => true,
			]
		);
	}

	/**
	 * Déclare les métadonnées de réservation.
	 */
	public function register_meta(): void {
		$string_meta = [ 'type', 'checkin', 'checkout', 'date_time', 'name', 'phone', 'email', 'message', 'status' ];
		foreach ( $string_meta as $key ) {
			register_post_meta(
				self::POST_TYPE,
				$key,
				[
					'type'              => 'string',
					'single'            => true,
					'default'           => '',
					'show_in_rest'      => false,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => static fn(): bool => current_user_can( 'manage_maji' ),
				]
			);
		}
		register_post_meta(
			self::POST_TYPE,
			'room_id',
			[
				'type'              => 'integer',
				'single'            => true,
				'default'           => 0,
				'show_in_rest'      => false,
				'sanitize_callback' => 'absint',
				'auth_callback'     => static fn(): bool => current_user_can( 'manage_maji' ),
			]
		);
		register_post_meta(
			self::POST_TYPE,
			'guests',
			[
				'type'              => 'integer',
				'single'            => true,
				'default'           => 1,
				'show_in_rest'      => false,
				'sanitize_callback' => 'absint',
				'auth_callback'     => static fn(): bool => current_user_can( 'manage_maji' ),
			]
		);
		register_post_meta(
			self::POST_TYPE,
			'whatsapp_consent',
			[
				'type'              => 'boolean',
				'single'            => true,
				'default'           => false,
				'show_in_rest'      => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => static fn(): bool => current_user_can( 'manage_maji' ),
			]
		);
	}

	/**
	 * Crée une demande de réservation.
	 *
	 * @param array<string, mixed> $data Données validées (voir ReservationController).
	 * @return int ID de la réservation créée (0 en échec).
	 */
	public static function create( array $data ): int {
		$title = sprintf(
			/* translators: 1 : nom du client, 2 : date. */
			__( 'Demande de %1$s — %2$s', 'maji-core' ),
			(string) ( $data['name'] ?? '' ),
			(string) ( $data['checkin'] ?? ( $data['date_time'] ?? '' ) )
		);

		$post_id = wp_insert_post(
			[
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => $title,
			],
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return 0;
		}

		$meta_keys = [ 'type', 'room_id', 'checkin', 'checkout', 'date_time', 'guests', 'name', 'phone', 'email', 'message', 'whatsapp_consent' ];
		foreach ( $meta_keys as $key ) {
			if ( array_key_exists( $key, $data ) ) {
				update_post_meta( $post_id, $key, $data[ $key ] );
			}
		}
		update_post_meta( $post_id, 'status', 'new' );

		/**
		 * Une demande de réservation vient d'être créée.
		 *
		 * @param int $post_id ID de la réservation.
		 */
		do_action( 'maji_reservation_created', $post_id );

		return $post_id;
	}

	/**
	 * Change le statut d'une demande et déclenche le hook de webhook.
	 *
	 * @param int    $post_id    ID de la réservation.
	 * @param string $new_status Nouveau statut.
	 * @param string $changed_by Auteur du changement (admin, system).
	 */
	public static function change_status( int $post_id, string $new_status, string $changed_by = 'admin' ): bool {
		if ( ! in_array( $new_status, self::STATUSES, true ) ) {
			return false;
		}
		$previous = (string) get_post_meta( $post_id, 'status', true );
		if ( $previous === $new_status ) {
			return true;
		}
		update_post_meta( $post_id, 'status', $new_status );

		/**
		 * Le statut d'une demande a changé.
		 *
		 * @param int    $post_id    ID de la réservation.
		 * @param string $previous   Ancien statut.
		 * @param string $new_status Nouveau statut.
		 * @param string $changed_by Auteur du changement.
		 */
		do_action( 'maji_reservation_status_changed', $post_id, $previous, $new_status, $changed_by );

		return true;
	}

	/**
	 * Libellés français des statuts.
	 *
	 * @return array<string, string> Libellés par statut.
	 */
	public static function status_labels(): array {
		return [
			'new'       => __( 'Nouvelle', 'maji-core' ),
			'confirmed' => __( 'Confirmée', 'maji-core' ),
			'declined'  => __( 'Refusée', 'maji-core' ),
			'cancelled' => __( 'Annulée', 'maji-core' ),
		];
	}

	/**
	 * Colonnes de la liste admin.
	 *
	 * @param array<string, string> $columns Colonnes existantes.
	 * @return array<string, string> Colonnes remplacées.
	 */
	public function admin_columns( array $columns ): array {
		unset( $columns );
		return [
			'cb'          => '<input type="checkbox" />',
			'title'       => __( 'Demande', 'maji-core' ),
			'maji_client' => __( 'Client', 'maji-core' ),
			'maji_dates'  => __( 'Dates', 'maji-core' ),
			'maji_room'   => __( 'Chambre / Table', 'maji-core' ),
			'maji_status' => __( 'Statut', 'maji-core' ),
			'date'        => __( 'Reçue le', 'maji-core' ),
		];
	}

	/**
	 * Contenu des colonnes admin.
	 *
	 * @param string $column  Colonne.
	 * @param int    $post_id ID.
	 */
	public function admin_column_content( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'maji_client':
				$name  = (string) get_post_meta( $post_id, 'name', true );
				$phone = (string) get_post_meta( $post_id, 'phone', true );
				printf( '%s<br><a href="tel:%s">%s</a>', esc_html( $name ), esc_attr( $phone ), esc_html( $phone ) );
				break;
			case 'maji_dates':
				$type = (string) get_post_meta( $post_id, 'type', true );
				if ( 'table' === $type ) {
					echo esc_html( (string) get_post_meta( $post_id, 'date_time', true ) );
				} else {
					printf(
						'%s → %s',
						esc_html( (string) get_post_meta( $post_id, 'checkin', true ) ),
						esc_html( (string) get_post_meta( $post_id, 'checkout', true ) )
					);
				}
				$guests = (int) get_post_meta( $post_id, 'guests', true );
				/* translators: %d : nombre de personnes. */
				printf( '<br>%s', esc_html( sprintf( _n( '%d personne', '%d personnes', $guests, 'maji-core' ), $guests ) ) );
				break;
			case 'maji_room':
				$room_id = (int) get_post_meta( $post_id, 'room_id', true );
				if ( $room_id > 0 ) {
					echo esc_html( get_the_title( $room_id ) );
				} else {
					$type = (string) get_post_meta( $post_id, 'type', true );
					echo 'table' === $type ? esc_html__( 'Table', 'maji-core' ) : esc_html__( '—', 'maji-core' );
				}
				break;
			case 'maji_status':
				$status = (string) get_post_meta( $post_id, 'status', true );
				$labels = self::status_labels();
				$label  = $labels[ $status ] ?? $status;
				printf(
					'<span class="maji-status maji-status--%s">%s</span>',
					esc_attr( $status ),
					esc_html( $label )
				);
				break;
		}
	}

	/**
	 * Filtre par statut au-dessus de la liste.
	 *
	 * @param string $post_type Type de contenu courant.
	 */
	public function status_filter_dropdown( string $post_type ): void {
		if ( self::POST_TYPE !== $post_type ) {
			return;
		}
		$current = isset( $_GET['maji_status'] ) ? sanitize_key( wp_unslash( $_GET['maji_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- filtre de liste en lecture.
		echo '<select name="maji_status">';
		echo '<option value="">' . esc_html__( 'Tous les statuts', 'maji-core' ) . '</option>';
		foreach ( self::status_labels() as $status => $label ) {
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $status ),
				selected( $current, $status, false ),
				esc_html( $label )
			);
		}
		echo '</select>';
	}

	/**
	 * Applique le filtre de statut à la requête de liste.
	 *
	 * @param \WP_Query $query Requête courante.
	 */
	public function apply_status_filter( \WP_Query $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( self::POST_TYPE !== $query->get( 'post_type' ) ) {
			return;
		}
		$status = isset( $_GET['maji_status'] ) ? sanitize_key( wp_unslash( $_GET['maji_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- filtre de liste en lecture.
		if ( '' !== $status && in_array( $status, self::STATUSES, true ) ) {
			$query->set(
				'meta_query',
				[
					[
						'key'   => 'status',
						'value' => $status,
					],
				]
			);
		}
	}

	/**
	 * Actions rapides de changement de statut dans la liste.
	 *
	 * @param array<string, string> $actions Actions existantes.
	 * @param \WP_Post              $post    Contenu courant.
	 * @return array<string, string> Actions.
	 */
	public function row_actions( array $actions, \WP_Post $post ): array {
		if ( self::POST_TYPE !== $post->post_type || ! current_user_can( 'manage_maji' ) ) {
			return $actions;
		}
		unset( $actions['inline hide-if-no-js'], $actions['view'] );
		$current = (string) get_post_meta( $post->ID, 'status', true );
		foreach ( self::status_labels() as $status => $label ) {
			if ( $status === $current ) {
				continue;
			}
			$url = wp_nonce_url(
				admin_url( 'admin-post.php?action=maji_reservation_status&reservation=' . $post->ID . '&status=' . $status ),
				'maji_reservation_status_' . $post->ID
			);
			/* translators: %s : libellé du statut. */
			$actions[ 'maji_' . $status ] = sprintf( '<a href="%s">%s</a>', esc_url( $url ), esc_html( sprintf( __( 'Marquer : %s', 'maji-core' ), $label ) ) );
		}
		return $actions;
	}

	/**
	 * Traite l'action de changement de statut (admin-post).
	 */
	public function handle_status_action(): void {
		if ( ! current_user_can( 'manage_maji' ) ) {
			wp_die( esc_html__( 'Action non autorisée.', 'maji-core' ) );
		}
		$post_id = isset( $_GET['reservation'] ) ? absint( $_GET['reservation'] ) : 0;
		$status  = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '';
		check_admin_referer( 'maji_reservation_status_' . $post_id );

		self::change_status( $post_id, $status, 'admin' );

		wp_safe_redirect( admin_url( 'edit.php?post_type=' . self::POST_TYPE ) );
		exit;
	}
}
