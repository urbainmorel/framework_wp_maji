<?php
/**
 * Stubs minimaux des fonctions WordPress utilisées par le code testé
 * unitairement. Volontairement réduits : si un test a besoin de plus,
 * c'est le signe qu'il devrait être un test d'intégration.
 *
 * @package maji
 */

declare(strict_types=1);

if ( ! function_exists( 'wp_json_encode' ) ) {
	/**
	 * Équivalent de wp_json_encode().
	 *
	 * @param mixed $data  Données.
	 * @param int   $flags Options json_encode.
	 */
	function wp_json_encode( $data, int $flags = 0 ): string|false {
		return json_encode( $data, $flags ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
	}
}

if ( ! function_exists( '__' ) ) {
	/**
	 * Passe-plat de traduction.
	 *
	 * @param string $text   Chaîne.
	 * @param string $domain Domaine.
	 */
	function __( string $text, string $domain = 'default' ): string { // phpcs:ignore Universal.Files.SeparateFunctionsFromOO.Mixed
		unset( $domain );
		return $text;
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	/**
	 * Échappement HTML minimal.
	 *
	 * @param string $text Chaîne.
	 */
	function esc_html( string $text ): string {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * Nettoyage minimal d'un champ texte.
	 *
	 * @param string $str Chaîne.
	 */
	function sanitize_text_field( string $str ): string {
		$filtered = trim( preg_replace( '/[\r\n\t ]+/', ' ', wp_strip_all_tags( $str ) ) ?? '' );
		return $filtered;
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	/**
	 * Suppression des balises.
	 *
	 * @param string $text Chaîne.
	 */
	function wp_strip_all_tags( string $text ): string {
		return trim( strip_tags( $text ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags
	}
}

if ( ! function_exists( 'absint' ) ) {
	/**
	 * Entier absolu.
	 *
	 * @param mixed $value Valeur.
	 */
	function absint( $value ): int {
		return abs( (int) $value );
	}
}
