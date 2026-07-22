<?php
/**
 * Conteneur principal du plugin.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core;

/**
 * Point d'entrée : enregistre les modules du plugin.
 */
final class Plugin {

	/**
	 * Instance unique.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Modules enregistrés, indexés par classe.
	 *
	 * @var array<class-string, object>
	 */
	private array $modules = [];

	/**
	 * Récupère l'instance unique.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Réglages MAJI.
	 *
	 * @var Settings\Settings|null
	 */
	private ?Settings\Settings $settings = null;

	/**
	 * Gestionnaire de modes.
	 *
	 * @var Modes\ModeManager|null
	 */
	private ?Modes\ModeManager $mode_manager = null;

	/**
	 * Constructeur privé (singleton).
	 */
	private function __construct() {}

	/**
	 * Réglages MAJI.
	 */
	public function settings(): Settings\Settings {
		if ( null === $this->settings ) {
			$this->settings = new Settings\Settings();
		}
		return $this->settings;
	}

	/**
	 * Gestionnaire de modes.
	 */
	public function modes(): Modes\ModeManager {
		if ( null === $this->mode_manager ) {
			$this->mode_manager = new Modes\ModeManager( $this->settings() );
		}
		return $this->mode_manager;
	}

	/**
	 * Initialise les modules du plugin.
	 */
	public function init(): void {
		load_plugin_textdomain( 'maji-core', false, dirname( plugin_basename( MAJI_CORE_FILE ) ) . '/languages' );

		$this->settings     = new Settings\Settings();
		$this->mode_manager = new Modes\ModeManager( $this->settings );

		$modules = [
			new Admin\SettingsPage( $this->settings ),
			new Hotel\Reservations(),
			new Rest\ReservationController(),
			new Rest\PublicSettingsController( $this->settings ),
			new Blocks\Blocks(),
		];

		if ( $this->mode_manager->is_hotel() ) {
			$modules[] = new Hotel\Rooms();
		}

		foreach ( $modules as $module ) {
			$this->modules[ get_class( $module ) ] = $module;
			$module->register();
		}

		/**
		 * Signale que les modules MAJI sont chargés.
		 *
		 * @param Plugin $plugin Instance du plugin.
		 */
		do_action( 'maji_init', $this );
	}

	/**
	 * Récupère un module enregistré.
	 *
	 * @param string $class_name Classe du module.
	 * @return object|null Module ou null.
	 */
	public function module( string $class_name ): ?object {
		$module = $this->modules[ $class_name ] ?? null;
		return $module instanceof $class_name ? $module : null;
	}

	/**
	 * Activation : capacité admin + tables.
	 */
	public static function activate(): void {
		$role = get_role( 'administrator' );
		if ( null !== $role && ! $role->has_cap( 'manage_maji' ) ) {
			$role->add_cap( 'manage_maji' );
		}
		flush_rewrite_rules();
	}

	/**
	 * Désactivation : nettoyage léger (les données restent en base).
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}
}
