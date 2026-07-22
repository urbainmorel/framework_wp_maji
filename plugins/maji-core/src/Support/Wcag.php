<?php
/**
 * Calcul de contraste WCAG 2.x.
 *
 * Classe pure (sans dépendance WordPress), utilisée par le validateur
 * d'ADN pour garantir l'accessibilité AA des palettes.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Support;

/**
 * Algorithme de luminance relative et ratio de contraste WCAG 2.x.
 */
final class Wcag {

	/**
	 * Ratio minimal AA pour le texte normal.
	 */
	public const AA_NORMAL = 4.5;

	/**
	 * Convertit une couleur hex en composantes RGB (0–255).
	 *
	 * @param string $hex Couleur (#RGB ou #RRGGBB).
	 * @return array{0: int, 1: int, 2: int} Composantes RGB.
	 */
	public static function hex_to_rgb( string $hex ): array {
		$hex = ltrim( $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		return [
			(int) hexdec( substr( $hex, 0, 2 ) ),
			(int) hexdec( substr( $hex, 2, 2 ) ),
			(int) hexdec( substr( $hex, 4, 2 ) ),
		];
	}

	/**
	 * Luminance relative d'une couleur (WCAG 2.x).
	 *
	 * @param string $hex Couleur hexadécimale.
	 */
	public static function relative_luminance( string $hex ): float {
		[ $r, $g, $b ] = self::hex_to_rgb( $hex );
		$channels      = array_map(
			static function ( int $channel ): float {
				$c = $channel / 255;
				return $c <= 0.03928 ? $c / 12.92 : ( ( $c + 0.055 ) / 1.055 ) ** 2.4;
			},
			[ $r, $g, $b ]
		);
		return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
	}

	/**
	 * Ratio de contraste entre deux couleurs (1–21).
	 *
	 * @param string $color_a Première couleur.
	 * @param string $color_b Seconde couleur.
	 */
	public static function contrast_ratio( string $color_a, string $color_b ): float {
		$la = self::relative_luminance( $color_a );
		$lb = self::relative_luminance( $color_b );
		$l1 = max( $la, $lb );
		$l2 = min( $la, $lb );
		return ( $l1 + 0.05 ) / ( $l2 + 0.05 );
	}

	/**
	 * Les deux couleurs respectent-elles le niveau AA (texte normal) ?
	 *
	 * @param string $foreground Couleur de texte.
	 * @param string $background Couleur de fond.
	 */
	public static function passes_aa( string $foreground, string $background ): bool {
		return self::contrast_ratio( $foreground, $background ) >= self::AA_NORMAL;
	}

	/**
	 * Teinte HSL (0–360) d'une couleur hex.
	 *
	 * Utilisée par le registre anti-clones (quantification par tranches de 30°).
	 *
	 * @param string $hex Couleur hexadécimale.
	 */
	public static function hue( string $hex ): float {
		[ $r, $g, $b ] = self::hex_to_rgb( $hex );
		$r             = (float) $r / 255;
		$g             = (float) $g / 255;
		$b             = (float) $b / 255;
		$mx            = max( $r, $g, $b );
		$mn            = min( $r, $g, $b );
		$d             = $mx - $mn;
		if ( abs( $d ) < 1e-9 ) {
			return 0.0;
		}
		if ( $mx === $r ) {
			$h = fmod( ( $g - $b ) / $d, 6 );
		} elseif ( $mx === $g ) {
			$h = ( $b - $r ) / $d + 2;
		} else {
			$h = ( $r - $g ) / $d + 4;
		}
		$h *= 60;
		return $h < 0 ? $h + 360 : $h;
	}
}
