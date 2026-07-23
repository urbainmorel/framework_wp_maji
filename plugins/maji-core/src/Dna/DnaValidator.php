<?php
/**
 * Validateur d'ADN (STI §5.4).
 *
 * Classe pure : les référentiels (DA, paires typo, headers, patterns,
 * vocabulaires) sont injectés pour rester testable sans WordPress.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Dna;

use MAJI\Core\Support\Validator;
use MAJI\Core\Support\Wcag;

/**
 * Validation structurelle, référentielle et de contraste d'un dna.json.
 */
final class DnaValidator {

	/**
	 * Référentiels par défaut (V1).
	 *
	 * @var array<string, string[]>
	 */
	private const DEFAULT_REFS = [
		'das'          => [
			'da-editorial-sombre',
			'da-solaire-minimal',
			'da-artisanal-texture',
			'da-lagune-fraiche',
			'da-nuit-cuivre',
			'da-savane-doree',
			'da-terracotta-vive',
			'da-ardoise-moderne',
			'da-jardin-botanique',
			'da-onyx-emeraude',
		],
		'font_pairs'   => [ 'fp-01', 'fp-02', 'fp-03', 'fp-04', 'fp-05', 'fp-06', 'fp-07', 'fp-08', 'fp-09', 'fp-10', 'fp-11', 'fp-12' ],
		'headers'      => [ 'header-01', 'header-02', 'header-03', 'header-04' ],
		'footers'      => [ 'footer-01', 'footer-02', 'footer-03' ],
		'vocabularies' => [ 'hotel-classique', 'hotel-experientiel', 'resto-classique', 'resto-convivial' ],
	];

	/**
	 * Paires de contraste critiques (avant-plan → arrière-plan).
	 *
	 * @var array<string, string>
	 */
	private const CONTRAST_PAIRS = [
		'ink'              => 'surface',
		'primary-contrast' => 'primary',
		'accent-contrast'  => 'accent',
	];

	/**
	 * Palettes par défaut des DA (complètent les slugs absents de l'ADN).
	 *
	 * @var array<string, array<string, string>>
	 */
	public const DA_PALETTES = [
		'da-editorial-sombre'  => [
			'base'             => '#0B0F14',
			'contrast'         => '#F3EFE6',
			'primary'          => '#0D1B2A',
			'primary-contrast' => '#FFFFFF',
			'accent'           => '#C9A227',
			'accent-contrast'  => '#141414',
			'surface'          => '#121820',
			'surface-alt'      => '#1A222C',
			'ink'              => '#E9E4D8',
			'ink-muted'        => '#9AA1A9',
		],
		'da-solaire-minimal'   => [
			'base'             => '#FDFDFB',
			'contrast'         => '#14181C',
			'primary'          => '#0E7C66',
			'primary-contrast' => '#FFFFFF',
			'accent'           => '#C2410C',
			'accent-contrast'  => '#FFFFFF',
			'surface'          => '#F5F6F4',
			'surface-alt'      => '#EBEEE9',
			'ink'              => '#232A30',
			'ink-muted'        => '#5F6A72',
		],
		'da-artisanal-texture' => [
			'base'             => '#F7F1E7',
			'contrast'         => '#2C1F14',
			'primary'          => '#8A3B12',
			'primary-contrast' => '#FDF7EE',
			'accent'           => '#3F5E3A',
			'accent-contrast'  => '#F7F1E7',
			'surface'          => '#EFE5D5',
			'surface-alt'      => '#E4D5BE',
			'ink'              => '#3A2E22',
			'ink-muted'        => '#71614F',
		],
		'da-lagune-fraiche'    => [
			'base'             => '#F7FBFC',
			'contrast'         => '#0C2A33',
			'primary'          => '#0B6E7A',
			'primary-contrast' => '#FFFFFF',
			'accent'           => '#C2451F',
			'accent-contrast'  => '#FFFFFF',
			'surface'          => '#EAF3F4',
			'surface-alt'      => '#DCE9EB',
			'ink'              => '#1B2E33',
			'ink-muted'        => '#5A6E73',
		],
		'da-nuit-cuivre'       => [
			'base'             => '#14100D',
			'contrast'         => '#F5EDE4',
			'primary'          => '#3A2A20',
			'primary-contrast' => '#F5EDE4',
			'accent'           => '#C87B45',
			'accent-contrast'  => '#14100D',
			'surface'          => '#1C1712',
			'surface-alt'      => '#26201A',
			'ink'              => '#EBE0D4',
			'ink-muted'        => '#A8988A',
		],
		'da-savane-doree'      => [
			'base'             => '#FBF6EC',
			'contrast'         => '#2A2114',
			'primary'          => '#3B5233',
			'primary-contrast' => '#FFFFFF',
			'accent'           => '#B8791C',
			'accent-contrast'  => '#201A0E',
			'surface'          => '#F3EBDA',
			'surface-alt'      => '#E9DEC6',
			'ink'              => '#33291A',
			'ink-muted'        => '#6E6146',
		],
		'da-terracotta-vive'   => [
			'base'             => '#FCF7F3',
			'contrast'         => '#241410',
			'primary'          => '#A83E1F',
			'primary-contrast' => '#FFFFFF',
			'accent'           => '#0F7A6B',
			'accent-contrast'  => '#FFFFFF',
			'surface'          => '#F6ECE4',
			'surface-alt'      => '#EFDFD3',
			'ink'              => '#33231C',
			'ink-muted'        => '#6F5A4F',
		],
		'da-ardoise-moderne'   => [
			'base'             => '#F7F8FA',
			'contrast'         => '#12171F',
			'primary'          => '#2B3A55',
			'primary-contrast' => '#FFFFFF',
			'accent'           => '#C77A0A',
			'accent-contrast'  => '#1A1206',
			'surface'          => '#EEF1F5',
			'surface-alt'      => '#E1E6EC',
			'ink'              => '#1E2530',
			'ink-muted'        => '#5A6472',
		],
		'da-jardin-botanique'  => [
			'base'             => '#F9F8F3',
			'contrast'         => '#1B2318',
			'primary'          => '#2F5D3A',
			'primary-contrast' => '#FFFFFF',
			'accent'           => '#A83D50',
			'accent-contrast'  => '#FFFFFF',
			'surface'          => '#F0F1E9',
			'surface-alt'      => '#E4E7DA',
			'ink'              => '#26301F',
			'ink-muted'        => '#5C6653',
		],
		'da-onyx-emeraude'     => [
			'base'             => '#0C1210',
			'contrast'         => '#EAF3EE',
			'primary'          => '#0F3D30',
			'primary-contrast' => '#EAF3EE',
			'accent'           => '#C9A227',
			'accent-contrast'  => '#0C1210',
			'surface'          => '#121C18',
			'surface-alt'      => '#1A2621',
			'ink'              => '#E4EFE8',
			'ink-muted'        => '#92A69C',
		],
	];

	/**
	 * Référentiels effectifs.
	 *
	 * @var array<string, string[]>
	 */
	private array $refs;

	/**
	 * Constructeur.
	 *
	 * @param array<string, string[]> $refs        Référentiels (fusionnés avec les défauts).
	 * @param string[]                $known_slugs Slugs de patterns connus (vide = pas de contrôle).
	 */
	public function __construct( array $refs = [], private readonly array $known_slugs = [] ) {
		$this->refs = array_merge( self::DEFAULT_REFS, $refs );
	}

	/**
	 * Valide un ADN décodé.
	 *
	 * @param array<string, mixed> $dna ADN.
	 * @return string[] Erreurs (vide = valide).
	 */
	public function validate( array $dna ): array {
		$errors = [];

		// 1. Structure minimale (conforme au JSON Schema maji-dna/1).
		if ( ( $dna['schema'] ?? '' ) !== 'maji-dna/1' ) {
			$errors[] = 'schema : doit valoir « maji-dna/1 ».';
		}
		foreach ( [ 'meta', 'identity', 'design', 'structure', 'content' ] as $key ) {
			if ( ! isset( $dna[ $key ] ) || ! is_array( $dna[ $key ] ) ) {
				$errors[] = sprintf( '%s : section obligatoire manquante.', $key );
			}
		}
		if ( [] !== $errors ) {
			return $errors;
		}

		$meta      = $dna['meta'];
		$identity  = $dna['identity'];
		$design    = $dna['design'];
		$structure = $dna['structure'];

		// Meta.
		if ( ! is_string( $meta['site_slug'] ?? null ) || 1 !== preg_match( '/^[a-z0-9][a-z0-9-]{1,60}$/', (string) $meta['site_slug'] ) ) {
			$errors[] = 'meta.site_slug : slug invalide (minuscules, chiffres, tirets).';
		}
		if ( ! is_string( $meta['created_at'] ?? null ) || ! Validator::is_date( (string) $meta['created_at'] ) ) {
			$errors[] = 'meta.created_at : date invalide (format AAAA-MM-JJ).';
		}

		// Identity.
		if ( '' === trim( (string) ( $identity['name'] ?? '' ) ) ) {
			$errors[] = 'identity.name : nom de l\'établissement requis.';
		}
		if ( ! in_array( $identity['type'] ?? '', [ 'hotel', 'restaurant', 'mixte' ], true ) ) {
			$errors[] = 'identity.type : hotel, restaurant ou mixte.';
		}
		foreach ( [ 'phone', 'whatsapp' ] as $field ) {
			$value = (string) ( $identity[ $field ] ?? '' );
			if ( '' !== $value && ! Validator::is_e164( $value ) ) {
				$errors[] = sprintf( 'identity.%s : « %s » n\'est pas au format E.164 (+22901020304).', $field, $value );
			}
		}
		$currency = (string) ( $identity['currency'] ?? 'XOF' );
		if ( ! Validator::is_currency( $currency ) ) {
			$errors[] = sprintf( 'identity.currency : « %s » n\'est pas un code ISO 4217.', $currency );
		}

		// Design : références.
		$da = (string) ( $design['da'] ?? '' );
		if ( ! in_array( $da, $this->refs['das'], true ) ) {
			$errors[] = sprintf( 'design.da : « %s » inconnue (disponibles : %s).', $da, implode( ', ', $this->refs['das'] ) );
		}
		$pair = (string) ( $design['font_pair'] ?? '' );
		if ( ! in_array( $pair, $this->refs['font_pairs'], true ) ) {
			$errors[] = sprintf( 'design.font_pair : « %s » inconnue (disponibles : %s).', $pair, implode( ', ', $this->refs['font_pairs'] ) );
		}
		if ( isset( $design['radius_scale'] ) && ! in_array( $design['radius_scale'], [ 'sm', 'md', 'lg' ], true ) ) {
			$errors[] = 'design.radius_scale : sm, md ou lg.';
		}
		if ( isset( $design['spacing_mood'] ) && ! in_array( $design['spacing_mood'], [ 'compact', 'normal', 'aere' ], true ) ) {
			$errors[] = 'design.spacing_mood : compact, normal ou aere.';
		}
		if ( isset( $design['section_bg_rhythm'] ) && ! in_array( $design['section_bg_rhythm'], [ 'uni', 'alterne', 'bandes', 'contraste' ], true ) ) {
			$errors[] = 'design.section_bg_rhythm : uni, alterne, bandes ou contraste.';
		}
		if ( isset( $design['section_style'] ) && ! in_array( $design['section_style'], [ 'auto', 'net', 'ombre', 'minimal' ], true ) ) {
			$errors[] = 'design.section_style : auto, net, ombre ou minimal.';
		}

		// Palette : hex valides puis contrastes AA.
		$palette = is_array( $design['palette'] ?? null ) ? $design['palette'] : [];
		foreach ( $palette as $slug => $hex ) {
			if ( ! is_string( $hex ) || ! Validator::is_hex_color( $hex ) ) {
				$errors[] = sprintf( 'design.palette.%s : couleur hexadécimale invalide.', (string) $slug );
			}
		}
		if ( in_array( $da, $this->refs['das'], true ) ) {
			$resolved = array_merge( self::DA_PALETTES[ $da ] ?? [], $palette );
			foreach ( self::CONTRAST_PAIRS as $fg => $bg ) {
				if ( isset( $resolved[ $fg ], $resolved[ $bg ] ) && is_string( $resolved[ $fg ] ) && is_string( $resolved[ $bg ] ) ) {
					$ratio = Wcag::contrast_ratio( $resolved[ $fg ], $resolved[ $bg ] );
					if ( $ratio < Wcag::AA_NORMAL ) {
						$errors[] = sprintf(
							'design.palette : contraste %s/%s insuffisant (%.2f:1, minimum AA 4.5:1).',
							$fg,
							$bg,
							$ratio
						);
					}
				}
			}
		}

		// Structure : références header/footer/vocabulaire/patterns.
		if ( ! in_array( $structure['header'] ?? '', $this->refs['headers'], true ) ) {
			$errors[] = 'structure.header : header-01 à header-04.';
		}
		if ( ! in_array( $structure['footer'] ?? '', $this->refs['footers'], true ) ) {
			$errors[] = 'structure.footer : footer-01 à footer-03.';
		}
		$vocab = (string) ( $structure['nav_vocabulary'] ?? '' );
		if ( ! in_array( $vocab, $this->refs['vocabularies'], true ) ) {
			$errors[] = sprintf( 'structure.nav_vocabulary : « %s » inconnu (disponibles : %s).', $vocab, implode( ', ', $this->refs['vocabularies'] ) );
		}

		$pages = is_array( $structure['pages'] ?? null ) ? $structure['pages'] : [];
		if ( [] === $pages ) {
			$errors[] = 'structure.pages : au moins une page est requise.';
		}
		foreach ( $pages as $i => $page ) {
			if ( ! is_array( $page ) || '' === (string) ( $page['slug'] ?? '' ) || '' === (string) ( $page['title'] ?? '' ) ) {
				$errors[] = sprintf( 'structure.pages[%d] : slug et title requis.', (int) $i );
				continue;
			}
			$sections = is_array( $page['sections'] ?? null ) ? $page['sections'] : [];
			if ( [] === $sections ) {
				$errors[] = sprintf( 'structure.pages[%d] (%s) : au moins une section.', (int) $i, (string) $page['slug'] );
			}
			foreach ( $sections as $section ) {
				$section = (string) $section;
				if ( ! str_starts_with( $section, 'maji/' ) ) {
					$errors[] = sprintf( 'structure.pages[%d] : slug de section invalide « %s » (préfixe maji/).', (int) $i, $section );
				} elseif ( [] !== $this->known_slugs && ! in_array( $section, $this->known_slugs, true ) ) {
					$errors[] = sprintf( 'structure.pages[%d] : pattern inconnu « %s ».', (int) $i, $section );
				}
			}
		}

		return $errors;
	}
}
