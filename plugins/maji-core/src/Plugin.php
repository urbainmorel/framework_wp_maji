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
	 * Constructeur privé (singleton).
	 */
	private function __construct() {}

	/**
	 * Initialise les modules du plugin.
	 */
	public function init(): void {
		load_plugin_textdomain( 'maji-core', false, dirname( plugin_basename( MAJI_CORE_FILE ) ) . '/languages' );

		// Les modules sont ajoutés jalon par jalon (M3+).
		$this->modules = [];

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
