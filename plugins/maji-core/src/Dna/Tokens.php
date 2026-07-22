<?php
/**
 * Résolution des jetons `{{maji:*}}` des patterns.
 *
 * Classe pure (sans dépendance WordPress) : les jetons sont remplacés
 * à l'import (apply-dna / import-content), jamais au rendu. Un jeton
 * résiduel en front est une erreur bloquante.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\Dna;

/**
 * Moteur de jetons : `{{maji:identity.name}}`, `{{maji:content.facts.quartier}}`…
 */
final class Tokens {

	private const PATTERN = '/\{\{maji:([a-z0-9_.\-]+)\}\}/i';

	/**
	 * Remplace les jetons d'un contenu depuis un tableau de données.
	 *
	 * Les jetons sans valeur restent en place (détectés ensuite par find()).
	 *
	 * @param string               $content Contenu avec jetons.
	 * @param array<string, mixed> $data    Données (identity, content, texts…).
	 * @return string Contenu résolu.
	 */
	public static function resolve( string $content, array $data ): string {
		$result = preg_replace_callback(
			self::PATTERN,
			static function ( array $matches ) use ( $data ): string {
				$value = self::lookup( $data, $matches[1] );
				return null === $value ? $matches[0] : $value;
			},
			$content
		);
		return $result ?? $content;
	}

	/**
	 * Liste les jetons présents dans un contenu.
	 *
	 * @param string $content Contenu.
	 * @return string[] Chemins des jetons trouvés (uniques).
	 */
	public static function find( string $content ): array {
		if ( 1 !== preg_match_all( self::PATTERN, $content, $matches ) && empty( $matches[1] ) ) {
			return [];
		}
		return array_values( array_unique( $matches[1] ) );
	}

	/**
	 * Recherche une valeur par chemin pointé dans les données.
	 *
	 * Les surcharges `texts` (clé de jeton → texte final) priment sur le
	 * chemin structurel.
	 *
	 * @param array<string, mixed> $data Données.
	 * @param string               $path Chemin pointé.
	 * @return string|null Valeur scalaire ou null si absente.
	 */
	private static function lookup( array $data, string $path ): ?string {
		if ( isset( $data['texts'] ) && is_array( $data['texts'] ) && array_key_exists( $path, $data['texts'] ) ) {
			$override = $data['texts'][ $path ];
			return is_scalar( $override ) ? (string) $override : null;
		}

		$value = $data;
		foreach ( explode( '.', $path ) as $segment ) {
			if ( ! is_array( $value ) || ! array_key_exists( $segment, $value ) ) {
				return null;
			}
			$value = $value[ $segment ];
		}

		if ( is_scalar( $value ) ) {
			return (string) $value;
		}
		if ( is_array( $value ) && array_is_list( $value ) && [] !== $value ) {
			$scalars = array_filter( $value, 'is_scalar' );
			if ( count( $scalars ) === count( $value ) ) {
				return implode( ', ', array_map( 'strval', $scalars ) );
			}
		}
		return null;
	}
}
