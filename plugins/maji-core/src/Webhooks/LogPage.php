<?php
/**
 * Page admin « Journal des webhooks ».
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Webhooks;

/**
 * Liste les 200 derniers envois avec bouton « Renvoyer ».
 */
final class LogPage {

	/**
	 * Constructeur.
	 *
	 * @param Dispatcher $dispatcher Dispatcher (pour le renvoi).
	 */
	public function __construct( private readonly Dispatcher $dispatcher ) {}

	/**
	 * Branche les hooks.
	 */
	public function register(): void {
		add_action( 'admin_menu', [ $this, 'register_page' ], 20 );
		add_action( 'admin_post_maji_webhook_resend', [ $this, 'handle_resend' ] );
	}

	/**
	 * Sous-page du menu MAJI.
	 */
	public function register_page(): void {
		add_submenu_page(
			'maji',
			__( 'Journal des webhooks', 'maji-core' ),
			__( 'Journal des webhooks', 'maji-core' ),
			'manage_maji',
			'maji-webhooks',
			[ $this, 'render' ]
		);
	}

	/**
	 * Affiche le journal.
	 */
	public function render(): void {
		if ( ! current_user_can( 'manage_maji' ) ) {
			wp_die( esc_html__( 'Accès refusé.', 'maji-core' ) );
		}
		$rows = Log::recent( 200 );
		?>
		<div class="wrap maji-admin">
			<h1><?php esc_html_e( 'Journal des webhooks', 'maji-core' ); ?></h1>
			<p class="description"><?php esc_html_e( 'Les 200 derniers envois vers n8n. Un envoi en échec est relancé automatiquement après 1, 10 puis 60 minutes.', 'maji-core' ); ?></p>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'maji-core' ); ?></th>
						<th><?php esc_html_e( 'Événement', 'maji-core' ); ?></th>
						<th><?php esc_html_e( 'Statut HTTP', 'maji-core' ); ?></th>
						<th><?php esc_html_e( 'Tentatives', 'maji-core' ); ?></th>
						<th><?php esc_html_e( 'Dernière erreur', 'maji-core' ); ?></th>
						<th><?php esc_html_e( 'Action', 'maji-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( [] === $rows ) : ?>
						<tr><td colspan="6"><?php esc_html_e( 'Aucun envoi pour le moment.', 'maji-core' ); ?></td></tr>
					<?php endif; ?>
					<?php foreach ( $rows as $row ) : ?>
						<?php
						$code = (int) $row->status_code;
						$ok   = $code >= 200 && $code < 300;
						?>
						<tr>
							<td><?php echo esc_html( (string) $row->created_at ); ?></td>
							<td><code><?php echo esc_html( (string) $row->event ); ?></code></td>
							<td>
								<span style="color:<?php echo $ok ? '#166534' : '#991b1b'; ?>;font-weight:600">
									<?php echo esc_html( $code > 0 ? (string) $code : __( 'Erreur réseau', 'maji-core' ) ); ?>
								</span>
							</td>
							<td><?php echo esc_html( (string) $row->attempts ); ?></td>
							<td><?php echo esc_html( (string) ( $row->last_error ?? '' ) ); ?></td>
							<td>
								<?php if ( ! $ok ) : ?>
									<a class="button button-small" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=maji_webhook_resend&log=' . (int) $row->id ), 'maji_webhook_resend_' . (int) $row->id ) ); ?>">
										<?php esc_html_e( 'Renvoyer', 'maji-core' ); ?>
									</a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Traite le renvoi manuel.
	 */
	public function handle_resend(): void {
		if ( ! current_user_can( 'manage_maji' ) ) {
			wp_die( esc_html__( 'Action non autorisée.', 'maji-core' ) );
		}
		$log_id = isset( $_GET['log'] ) ? absint( $_GET['log'] ) : 0;
		check_admin_referer( 'maji_webhook_resend_' . $log_id );
		$this->dispatcher->resend( $log_id );
		wp_safe_redirect( admin_url( 'admin.php?page=maji-webhooks' ) );
		exit;
	}
}
