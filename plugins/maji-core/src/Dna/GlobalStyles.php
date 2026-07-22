<?php
/**
 * Construction du fragment de global styles utilisateur (STI §5.2).
 *
 * Classe pure : produit le JSON `isGlobalStylesUserThemeJSON` écrit dans
 * le post `wp_global_styles`. Ne modifie JAMAIS les fichiers du thème.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Dna;

/**
 * Fragment theme.json partiel depuis le bloc `design` de l'ADN.
 */
final class GlobalStyles {

	/**
	 * Échelles de rayons par valeur de radius_scale.
	 *
	 * @var array<string, array<string, string>>
	 */
	private const RADIUS_SCALES = [
		'sm' => [
			'sm'   => '2px',
			'md'   => '4px',
			'lg'   => '8px',
			'pill' => '999px',
		],
		'md' => [
			'sm'   => '6px',
			'md'   => '12px',
			'lg'   => '20px',
			'pill' => '999px',
		],
		'lg' => [
			'sm'   => '12px',
			'md'   => '20px',
			'lg'   => '28px',
			'pill' => '999px',
		],
	];

	/**
	 * Pas d'échelle d'espacement par humeur.
	 *
	 * @var array<string, float>
	 */
	private const SPACING_MOODS = [
		'compact' => 1.25,
		'normal'  => 1.5,
		'aere'    => 2.0,
	];

	/**
	 * Noms des couleurs sémantiques (libellés admin).
	 *
	 * @var array<string, string>
	 */
	private const PALETTE_NAMES = [
		'base'             => 'Fond',
		'contrast'         => 'Contraste',
		'primary'          => 'Primaire',
		'primary-contrast' => 'Contraste primaire',
		'accent'           => 'Accent',
		'accent-contrast'  => 'Contraste accent',
		'surface'          => 'Surface',
		'surface-alt'      => 'Surface alternative',
		'ink'              => 'Encre',
		'ink-muted'        => 'Encre atténuée',
	];

	/**
	 * Construit le contenu du post wp_global_styles.
	 *
	 * @param array<string, mixed>      $design    Bloc `design` de l'ADN.
	 * @param array<string, mixed>|null $font_pair Paire typographique (catalogue fonts.json).
	 * @return array<string, mixed> Contenu JSON du post global styles.
	 */
	public static function build( array $design, ?array $font_pair = null ): array {
		$result = [
			'version'                     => 3,
			'isGlobalStylesUserThemeJSON' => true,
			'settings'                    => [],
			'styles'                      => [],
		];

		// Palette : la DA fournit la base, l'ADN surcharge.
		$da       = (string) ( $design['da'] ?? '' );
		$palette  = is_array( $design['palette'] ?? null ) ? $design['palette'] : [];
		$resolved = array_merge( DnaValidator::DA_PALETTES[ $da ] ?? [], $palette );
		if ( [] !== $resolved ) {
			$entries = [];
			foreach ( self::PALETTE_NAMES as $slug => $name ) {
				if ( isset( $resolved[ $slug ] ) ) {
					$entries[] = [
						'slug'  => $slug,
						'name'  => $name,
						'color' => (string) $resolved[ $slug ],
					];
				}
			}
			$result['settings']['color']['palette'] = $entries;
		}

		// Rayons.
		$radius_scale = (string) ( $design['radius_scale'] ?? '' );
		if ( isset( self::RADIUS_SCALES[ $radius_scale ] ) ) {
			$result['settings']['custom']['maji']['radius'] = self::RADIUS_SCALES[ $radius_scale ];
		}

		// Humeur d'espacement.
		$mood = (string) ( $design['spacing_mood'] ?? '' );
		if ( isset( self::SPACING_MOODS[ $mood ] ) ) {
			$result['settings']['spacing']['spacingScale']        = [
				'operator'   => '*',
				'increment'  => 1.6,
				'steps'      => 7,
				'mediumStep' => self::SPACING_MOODS[ $mood ],
				'unit'       => 'rem',
			];
			$result['settings']['spacing']['defaultSpacingSizes'] = false;
		}

		// Typographie : familles de la paire.
		if ( null !== $font_pair ) {
			$body_slug    = (string) ( $font_pair['body']['slug'] ?? '' );
			$heading_slug = (string) ( $font_pair['heading']['slug'] ?? '' );
			if ( '' !== $body_slug ) {
				$result['styles']['typography']['fontFamily'] = sprintf( 'var(--wp--preset--font-family--%s)', $body_slug );
			}
			if ( '' !== $heading_slug ) {
				$heading_var = sprintf( 'var(--wp--preset--font-family--%s)', $heading_slug );

				$result['styles']['elements']['heading']['typography']['fontFamily']       = $heading_var;
				$result['styles']['blocks']['core/site-title']['typography']['fontFamily'] = $heading_var;
			}
		}

		// Duotone selon le traitement d'image.
		$treatment = is_array( $design['image_treatment'] ?? null ) ? $design['image_treatment'] : [];
		if ( ! empty( $treatment['duotone'] ) && isset( $resolved['primary'], $resolved['base'] ) ) {
			$result['settings']['color']['duotone'] = [
				[
					'slug'   => 'maji-primary',
					'name'   => 'Duotone du site',
					'colors' => [ (string) $resolved['primary'], (string) $resolved['base'] ],
				],
			];
		}

		return $result;
	}
}
