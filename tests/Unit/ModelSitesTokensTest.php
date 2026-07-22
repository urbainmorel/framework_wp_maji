<?php
/**
 * Garde-fou : chaque ADN livré résout tous les jetons de ses sections.
 *
 * Reproduit le contrôle final de `wp maji provision` (résolution des jetons)
 * sans WordPress, en s'appuyant sur les vrais fichiers de patterns du thème et
 * la vraie classe Tokens. Empêche qu'une modification d'ADN casse la provision.
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Dna\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * Complétude des jetons pour les ADN d'exemple et les 4 sites modèles.
 */
final class ModelSitesTokensTest extends TestCase {

	private const THEME_PATTERNS = __DIR__ . '/../../themes/maji-framework/patterns';

	/**
	 * Construit la carte section => jetons requis, depuis les patterns du thème.
	 *
	 * @return array<string, string[]> Jetons par slug de section.
	 */
	private function section_tokens(): array {
		$map = [];
		foreach ( glob( self::THEME_PATTERNS . '/*.php' ) as $file ) {
			$slug = 'maji/' . basename( $file, '.php' );
			preg_match_all( '/\{\{maji:([a-z0-9_.\-]+)\}\}/', (string) file_get_contents( $file ), $m );
			$map[ $slug ] = array_values( array_unique( $m[1] ) );
		}
		return $map;
	}

	/**
	 * Données de résolution (identity + content + texts) comme DnaApplier.
	 *
	 * @param array<string, mixed> $dna ADN.
	 * @return array<string, mixed> Données pour Tokens.
	 */
	private function token_data( array $dna ): array {
		return [
			'identity' => is_array( $dna['identity'] ?? null ) ? $dna['identity'] : [],
			'content'  => is_array( $dna['content'] ?? null ) ? $dna['content'] : [],
			'texts'    => is_array( $dna['content']['texts'] ?? null ) ? $dna['content']['texts'] : [],
		];
	}

	/**
	 * Fournit chaque fichier ADN à tester.
	 *
	 * @return array<string, array{0: string}> Cas de test.
	 */
	public static function dna_provider(): array {
		$files = array_merge(
			glob( __DIR__ . '/../../dna/examples/*.json' ) ?: [],
			glob( __DIR__ . '/../../demo-content/*/dna.json' ) ?: []
		);
		$cases = [];
		foreach ( $files as $file ) {
			$cases[ basename( dirname( $file ) ) . '/' . basename( $file ) ] = [ $file ];
		}
		return $cases;
	}

	/**
	 * Tous les jetons des sections de chaque ADN sont résolus (aucun résiduel).
	 *
	 * @dataProvider dna_provider
	 *
	 * @param string $file Chemin de l'ADN.
	 */
	public function test_all_section_tokens_resolve( string $file ): void {
		$dna = json_decode( (string) file_get_contents( $file ), true );
		self::assertIsArray( $dna, "ADN illisible : $file" );

		$section_tokens = $this->section_tokens();
		$data           = $this->token_data( $dna );

		$sections = [];
		foreach ( $dna['structure']['pages'] ?? [] as $page ) {
			foreach ( $page['sections'] ?? [] as $slug ) {
				$sections[] = (string) $slug;
			}
		}
		self::assertNotEmpty( $sections, "Aucune section dans $file" );

		$unresolved = [];
		foreach ( array_unique( $sections ) as $slug ) {
			self::assertArrayHasKey( $slug, $section_tokens, "Section inconnue : $slug ($file)" );
			foreach ( $section_tokens[ $slug ] as $token ) {
				$probe  = '{{maji:' . $token . '}}';
				$result = Tokens::resolve( $probe, $data );
				if ( $result === $probe || '' === $result ) {
					$unresolved[] = $token . ' (requis par ' . $slug . ')';
				}
			}
		}

		self::assertSame(
			[],
			array_values( array_unique( $unresolved ) ),
			"Jetons non résolus dans $file"
		);
	}
}
