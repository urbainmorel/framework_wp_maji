<?php
/**
 * Block styles de section (levier de diversité `section_style`, V2-B).
 *
 * Enregistre trois variantes de style pour le conteneur des sections
 * (`core/group`), appliquées par `SectionStyler` via la classe
 * `is-style-maji-*`. CSS 100 % tokens (`theme.json`) — jamais de valeur en
 * dur, jamais de fichier de thème modifié (contrat STI §5.2).
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Blocks;

/**
 * Déclare les block styles net / ombré / minimal pour les sections.
 */
final class SectionStyles {

	/**
	 * Définition des variantes : name ⇒ [label, CSS inline (tokens only)].
	 *
	 * @var array<string, array{0: string, 1: string}>
	 */
	private const VARIANTS = [
		'maji-net'     => [
			'Net',
			'.wp-block-group.is-style-maji-net{border-top:1px solid var(--wp--preset--color--surface-alt);box-shadow:none;}',
		],
		'maji-ombre'   => [
			'Ombré',
			'.wp-block-group.is-style-maji-ombre{box-shadow:var(--wp--custom--maji--shadow--card);}',
		],
		'maji-minimal' => [
			'Minimal',
			'.wp-block-group.is-style-maji-minimal{border:0;box-shadow:none;}',
		],
	];

	/**
	 * Branche les hooks.
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'register_styles' ] );
	}

	/**
	 * Enregistre chaque variante comme block style de `core/group`.
	 */
	public function register_styles(): void {
		if ( ! function_exists( 'register_block_style' ) ) {
			return;
		}
		$labels = [
			'maji-net'     => __( 'Net', 'maji-core' ),
			'maji-ombre'   => __( 'Ombré', 'maji-core' ),
			'maji-minimal' => __( 'Minimal', 'maji-core' ),
		];
		foreach ( self::VARIANTS as $name => $definition ) {
			register_block_style(
				'core/group',
				[
					'name'         => $name,
					'label'        => $labels[ $name ] ?? $definition[0],
					'inline_style' => $definition[1],
				]
			);
		}
	}
}
