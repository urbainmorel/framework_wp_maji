<?php
/**
 * Pages d'administration MAJI (Settings API classique).
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Admin;

use MAJI\Core\Settings\Settings;

/**
 * Menu « MAJI » : Établissement, Modes & fonctionnalités, Intégrations.
 */
final class SettingsPage {

	private const MENU_SLUG = 'maji';

	/**
	 * Constructeur.
	 *
	 * @param Settings $settings Réglages.
	 */
	public function __construct( private readonly Settings $settings ) {}

	/**
	 * Branche les hooks.
	 */
	public function register(): void {
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
		add_action( 'admin_init', [ $this, 'register_setting' ] );
		add_action( 'admin_head', [ $this, 'admin_styles' ] );
	}

	/**
	 * Déclare le menu et les sous-pages.
	 */
	public function register_menu(): void {
		add_menu_page(
			__( 'MAJI', 'maji-core' ),
			__( 'MAJI', 'maji-core' ),
			'manage_maji',
			self::MENU_SLUG,
			[ $this, 'render_establishment_page' ],
			'dashicons-store',
			58
		);
		add_submenu_page(
			self::MENU_SLUG,
			__( 'Établissement', 'maji-core' ),
			__( 'Établissement', 'maji-core' ),
			'manage_maji',
			self::MENU_SLUG,
			[ $this, 'render_establishment_page' ]
		);
		add_submenu_page(
			self::MENU_SLUG,
			__( 'Modes & fonctionnalités', 'maji-core' ),
			__( 'Modes & fonctionnalités', 'maji-core' ),
			'manage_maji',
			'maji-features',
			[ $this, 'render_features_page' ]
		);
		add_submenu_page(
			self::MENU_SLUG,
			__( 'Intégrations', 'maji-core' ),
			__( 'Intégrations', 'maji-core' ),
			'manage_maji',
			'maji-integrations',
			[ $this, 'render_integrations_page' ]
		);
	}

	/**
	 * Enregistre l'option unique avec sanitisation.
	 */
	public function register_setting(): void {
		register_setting(
			'maji',
			Settings::OPTION,
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize' ],
			]
		);
	}

	/**
	 * Fusionne et nettoie les réglages postés avec l'existant.
	 *
	 * @param mixed $input Données postées.
	 * @return array<string, mixed> Réglages nettoyés.
	 */
	public function sanitize( mixed $input ): array {
		$current = $this->settings->all();
		if ( ! is_array( $input ) ) {
			return $current;
		}

		if ( isset( $input['establishment'] ) && is_array( $input['establishment'] ) ) {
			$e               = $input['establishment'];
			$est             = $current['establishment'];
			$est['name']     = sanitize_text_field( (string) ( $e['name'] ?? $est['name'] ) );
			$type            = (string) ( $e['type'] ?? $est['type'] );
			$est['type']     = in_array( $type, [ 'hotel', 'restaurant', 'mixte' ], true ) ? $type : 'hotel';
			$est['phone']    = sanitize_text_field( (string) ( $e['phone'] ?? $est['phone'] ) );
			$est['whatsapp'] = sanitize_text_field( (string) ( $e['whatsapp'] ?? $est['whatsapp'] ) );
			$est['email']    = sanitize_email( (string) ( $e['email'] ?? $est['email'] ) );
			$est['currency'] = strtoupper( sanitize_text_field( (string) ( $e['currency'] ?? $est['currency'] ) ) );
			if ( isset( $e['address'] ) && is_array( $e['address'] ) ) {
				foreach ( [ 'street', 'district', 'city', 'country' ] as $field ) {
					$est['address'][ $field ] = sanitize_text_field( (string) ( $e['address'][ $field ] ?? '' ) );
				}
			}
			if ( isset( $e['socials'] ) && is_array( $e['socials'] ) ) {
				foreach ( [ 'facebook', 'instagram', 'tiktok' ] as $network ) {
					$est['socials'][ $network ] = esc_url_raw( (string) ( $e['socials'][ $network ] ?? '' ) );
				}
			}
			if ( isset( $e['hours'] ) && is_array( $e['hours'] ) ) {
				foreach ( array_keys( $est['hours'] ) as $day ) {
					$raw                  = (string) ( $e['hours'][ $day ] ?? '' );
					$est['hours'][ $day ] = $this->parse_hours( $raw );
				}
			}
			$current['establishment'] = $est;
		}

		if ( isset( $input['features'] ) && is_array( $input['features'] ) ) {
			foreach ( [ 'ordering', 'table_booking', 'delivery', 'pickup' ] as $flag ) {
				$current['features'][ $flag ] = ! empty( $input['features'][ $flag ] );
			}
		}

		if ( isset( $input['delivery_zones'] ) && is_array( $input['delivery_zones'] ) ) {
			$zones = [];
			foreach ( $input['delivery_zones'] as $zone ) {
				if ( ! is_array( $zone ) ) {
					continue;
				}
				$name = sanitize_text_field( (string) ( $zone['name'] ?? '' ) );
				if ( '' === $name ) {
					continue;
				}
				$zones[] = [
					'name' => $name,
					'fee'  => absint( $zone['fee'] ?? 0 ),
				];
			}
			$current['delivery_zones'] = $zones;
		}

		if ( isset( $input['integrations'] ) && is_array( $input['integrations'] ) ) {
			foreach ( [ 'n8n_order_url', 'n8n_reservation_url' ] as $key ) {
				if ( array_key_exists( $key, $input['integrations'] ) ) {
					$current['integrations'][ $key ] = esc_url_raw( (string) $input['integrations'][ $key ] );
				}
			}
		}

		return $current;
	}

	/**
	 * Convertit « 08:00-12:00, 15:00-22:00 » en [["08:00","12:00"],["15:00","22:00"]].
	 *
	 * @param string $raw Saisie utilisateur.
	 * @return array<int, array{0: string, 1: string}> Plages horaires.
	 */
	private function parse_hours( string $raw ): array {
		$ranges = [];
		foreach ( explode( ',', $raw ) as $chunk ) {
			if ( 1 === preg_match( '/^\s*([01]\d|2[0-3]):([0-5]\d)\s*-\s*([01]\d|2[0-3]):([0-5]\d)\s*$/', $chunk, $m ) ) {
				$ranges[] = [ $m[1] . ':' . $m[2], $m[3] . ':' . $m[4] ];
			}
		}
		return $ranges;
	}

	/**
	 * Formate les plages pour l'affichage dans le champ.
	 *
	 * @param array<int, array{0: string, 1: string}> $ranges Plages.
	 */
	private function format_hours( array $ranges ): string {
		return implode(
			', ',
			array_map(
				static fn( array $r ): string => $r[0] . '-' . $r[1],
				$ranges
			)
		);
	}

	/**
	 * Page Établissement.
	 */
	public function render_establishment_page(): void {
		if ( ! current_user_can( 'manage_maji' ) ) {
			wp_die( esc_html__( 'Accès refusé.', 'maji-core' ) );
		}
		$s          = $this->settings->all();
		$est        = $s['establishment'];
		$day_labels = [
			'mon' => __( 'Lundi', 'maji-core' ),
			'tue' => __( 'Mardi', 'maji-core' ),
			'wed' => __( 'Mercredi', 'maji-core' ),
			'thu' => __( 'Jeudi', 'maji-core' ),
			'fri' => __( 'Vendredi', 'maji-core' ),
			'sat' => __( 'Samedi', 'maji-core' ),
			'sun' => __( 'Dimanche', 'maji-core' ),
		];
		?>
		<div class="wrap maji-admin">
			<h1><?php esc_html_e( 'Établissement', 'maji-core' ); ?></h1>
			<p><?php esc_html_e( 'Ces informations alimentent automatiquement l\'en-tête, le pied de page, les boutons WhatsApp, la page contact et les données structurées. Aucune double saisie nécessaire.', 'maji-core' ); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'maji' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="maji-name"><?php esc_html_e( 'Nom de l\'établissement', 'maji-core' ); ?></label></th>
						<td><input id="maji-name" class="regular-text" type="text" name="maji_settings[establishment][name]" value="<?php echo esc_attr( (string) $est['name'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="maji-type"><?php esc_html_e( 'Type', 'maji-core' ); ?></label></th>
						<td>
							<select id="maji-type" name="maji_settings[establishment][type]">
								<option value="hotel" <?php selected( $est['type'], 'hotel' ); ?>><?php esc_html_e( 'Hôtel', 'maji-core' ); ?></option>
								<option value="restaurant" <?php selected( $est['type'], 'restaurant' ); ?>><?php esc_html_e( 'Restaurant', 'maji-core' ); ?></option>
								<option value="mixte" <?php selected( $est['type'], 'mixte' ); ?>><?php esc_html_e( 'Hôtel-restaurant', 'maji-core' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="maji-phone"><?php esc_html_e( 'Téléphone', 'maji-core' ); ?></label></th>
						<td>
							<input id="maji-phone" class="regular-text" type="tel" name="maji_settings[establishment][phone]" value="<?php echo esc_attr( (string) $est['phone'] ); ?>" placeholder="+22901020304">
							<p class="description"><?php esc_html_e( 'Format international, ex. +22901020304.', 'maji-core' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="maji-whatsapp"><?php esc_html_e( 'WhatsApp', 'maji-core' ); ?></label></th>
						<td><input id="maji-whatsapp" class="regular-text" type="tel" name="maji_settings[establishment][whatsapp]" value="<?php echo esc_attr( (string) $est['whatsapp'] ); ?>" placeholder="+22901020304"></td>
					</tr>
					<tr>
						<th scope="row"><label for="maji-email"><?php esc_html_e( 'E-mail', 'maji-core' ); ?></label></th>
						<td><input id="maji-email" class="regular-text" type="email" name="maji_settings[establishment][email]" value="<?php echo esc_attr( (string) $est['email'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="maji-street"><?php esc_html_e( 'Adresse', 'maji-core' ); ?></label></th>
						<td>
							<input id="maji-street" class="regular-text" type="text" name="maji_settings[establishment][address][street]" value="<?php echo esc_attr( (string) $est['address']['street'] ); ?>" placeholder="<?php esc_attr_e( 'Rue', 'maji-core' ); ?>"><br>
							<input class="regular-text" type="text" name="maji_settings[establishment][address][district]" value="<?php echo esc_attr( (string) $est['address']['district'] ); ?>" placeholder="<?php esc_attr_e( 'Quartier', 'maji-core' ); ?>"><br>
							<input class="regular-text" type="text" name="maji_settings[establishment][address][city]" value="<?php echo esc_attr( (string) $est['address']['city'] ); ?>" placeholder="<?php esc_attr_e( 'Ville', 'maji-core' ); ?>"><br>
							<input class="small-text" type="text" maxlength="2" name="maji_settings[establishment][address][country]" value="<?php echo esc_attr( (string) $est['address']['country'] ); ?>" placeholder="BJ">
							<p class="description"><?php esc_html_e( 'Code pays ISO à deux lettres (BJ, CI, SN…).', 'maji-core' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Horaires d\'ouverture', 'maji-core' ); ?></th>
						<td>
							<?php foreach ( $day_labels as $day => $label ) : ?>
								<label style="display:inline-block;width:6em"><?php echo esc_html( $label ); ?></label>
								<input class="regular-text" type="text" name="maji_settings[establishment][hours][<?php echo esc_attr( $day ); ?>]" value="<?php echo esc_attr( $this->format_hours( $est['hours'][ $day ] ) ); ?>" placeholder="08:00-22:00"><br>
							<?php endforeach; ?>
							<p class="description"><?php esc_html_e( 'Plages au format 08:00-12:00, 15:00-22:00. Laisser vide si fermé.', 'maji-core' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="maji-currency"><?php esc_html_e( 'Devise', 'maji-core' ); ?></label></th>
						<td><input id="maji-currency" class="small-text" type="text" maxlength="3" name="maji_settings[establishment][currency]" value="<?php echo esc_attr( (string) $est['currency'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Réseaux sociaux', 'maji-core' ); ?></th>
						<td>
							<input class="regular-text" type="url" name="maji_settings[establishment][socials][facebook]" value="<?php echo esc_attr( (string) $est['socials']['facebook'] ); ?>" placeholder="https://facebook.com/…"><br>
							<input class="regular-text" type="url" name="maji_settings[establishment][socials][instagram]" value="<?php echo esc_attr( (string) $est['socials']['instagram'] ); ?>" placeholder="https://instagram.com/…"><br>
							<input class="regular-text" type="url" name="maji_settings[establishment][socials][tiktok]" value="<?php echo esc_attr( (string) $est['socials']['tiktok'] ); ?>" placeholder="https://tiktok.com/@…">
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Enregistrer', 'maji-core' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Page Modes & fonctionnalités.
	 */
	public function render_features_page(): void {
		if ( ! current_user_can( 'manage_maji' ) ) {
			wp_die( esc_html__( 'Accès refusé.', 'maji-core' ) );
		}
		$s     = $this->settings->all();
		$f     = $s['features'];
		$zones = $s['delivery_zones'];
		?>
		<div class="wrap maji-admin">
			<h1><?php esc_html_e( 'Modes & fonctionnalités', 'maji-core' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'maji' ); ?>
				<h2><?php esc_html_e( 'Fonctionnalités', 'maji-core' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Commande en ligne', 'maji-core' ); ?></th>
						<td><label><input type="checkbox" name="maji_settings[features][ordering]" value="1" <?php checked( (bool) $f['ordering'] ); ?>> <?php esc_html_e( 'Activer la commande en ligne (mode restaurant, nécessite WooCommerce)', 'maji-core' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Réservation de table', 'maji-core' ); ?></th>
						<td><label><input type="checkbox" name="maji_settings[features][table_booking]" value="1" <?php checked( (bool) $f['table_booking'] ); ?>> <?php esc_html_e( 'Activer les demandes de réservation de table', 'maji-core' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Livraison', 'maji-core' ); ?></th>
						<td><label><input type="checkbox" name="maji_settings[features][delivery]" value="1" <?php checked( (bool) $f['delivery'] ); ?>> <?php esc_html_e( 'Proposer la livraison', 'maji-core' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Retrait sur place', 'maji-core' ); ?></th>
						<td><label><input type="checkbox" name="maji_settings[features][pickup]" value="1" <?php checked( (bool) $f['pickup'] ); ?>> <?php esc_html_e( 'Proposer le retrait sur place', 'maji-core' ); ?></label></td>
					</tr>
				</table>
				<h2><?php esc_html_e( 'Zones de livraison', 'maji-core' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Une zone par ligne : nom du quartier et frais en FCFA.', 'maji-core' ); ?></p>
				<table class="form-table" role="presentation">
					<?php
					$rows = array_merge(
						$zones,
						[
							[
								'name' => '',
								'fee'  => 0,
							],
							[
								'name' => '',
								'fee'  => 0,
							],
						]
					);
					foreach ( $rows as $i => $zone ) :
						?>
					<tr>
						<th scope="row">
							<?php
							/* translators: %d : numéro de zone. */
							echo esc_html( sprintf( __( 'Zone %d', 'maji-core' ), $i + 1 ) );
							?>
						</th>
						<td>
							<input type="text" name="maji_settings[delivery_zones][<?php echo esc_attr( (string) $i ); ?>][name]" value="<?php echo esc_attr( (string) $zone['name'] ); ?>" placeholder="<?php esc_attr_e( 'Quartier', 'maji-core' ); ?>">
							<input class="small-text" type="number" min="0" step="50" name="maji_settings[delivery_zones][<?php echo esc_attr( (string) $i ); ?>][fee]" value="<?php echo esc_attr( (string) $zone['fee'] ); ?>"> FCFA
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button( __( 'Enregistrer', 'maji-core' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Page Intégrations (n8n).
	 */
	public function render_integrations_page(): void {
		if ( ! current_user_can( 'manage_maji' ) ) {
			wp_die( esc_html__( 'Accès refusé.', 'maji-core' ) );
		}
		$s = $this->settings->all();
		$i = $s['integrations'];
		?>
		<div class="wrap maji-admin">
			<h1><?php esc_html_e( 'Intégrations', 'maji-core' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'maji' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="maji-n8n-order"><?php esc_html_e( 'URL n8n — commandes', 'maji-core' ); ?></label></th>
						<td><input id="maji-n8n-order" class="large-text" type="url" name="maji_settings[integrations][n8n_order_url]" value="<?php echo esc_attr( (string) $i['n8n_order_url'] ); ?>" placeholder="https://n8n.example.com/webhook/…"></td>
					</tr>
					<tr>
						<th scope="row"><label for="maji-n8n-resa"><?php esc_html_e( 'URL n8n — réservations', 'maji-core' ); ?></label></th>
						<td><input id="maji-n8n-resa" class="large-text" type="url" name="maji_settings[integrations][n8n_reservation_url]" value="<?php echo esc_attr( (string) $i['n8n_reservation_url'] ); ?>" placeholder="https://n8n.example.com/webhook/…"></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Secret de signature', 'maji-core' ); ?></th>
						<td>
							<p class="description"><?php esc_html_e( 'Le secret HMAC est généré automatiquement et n\'est jamais affiché en clair. Utilisez WP-CLI (wp maji) pour le récupérer lors du branchement de n8n.', 'maji-core' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Enregistrer', 'maji-core' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Styles inline pour les pastilles de statut.
	 */
	public function admin_styles(): void {
		echo '<style>
			.maji-status{display:inline-block;padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600}
			.maji-status--new{background:#dbeafe;color:#1e40af}
			.maji-status--confirmed{background:#dcfce7;color:#166534}
			.maji-status--declined{background:#fee2e2;color:#991b1b}
			.maji-status--cancelled{background:#e5e7eb;color:#374151}
		</style>';
	}
}
