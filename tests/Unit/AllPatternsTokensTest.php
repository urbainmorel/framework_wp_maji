<?php
/**
 * Garde-fou : chaque pattern du thème résout tous ses jetons.
 *
 * Complète ModelSitesTokensTest (qui ne couvre que les sections référencées par
 * les ADN livrés) : ici on éprouve TOUS les patterns du thème contre un jeu de
 * données complet, pour qu'un pattern n'introduise jamais un jeton hors du
 * vocabulaire résoluble.
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Dna\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * Résolution des jetons pour l'ensemble des patterns.
 */
final class AllPatternsTokensTest extends TestCase {

	private const PATTERNS = __DIR__ . '/../../themes/maji-framework/patterns';

	/**
	 * Jeu de données « kitchen-sink » couvrant tout le vocabulaire de jetons.
	 *
	 * @return array<string, mixed> Données pour Tokens::resolve.
	 */
	private function fixture(): array {
		$texts = [];
		foreach (
			[
				'hero_tagline',
				'menu_du_jour',
				'menu_intro',
				'apropos_texte',
				'experience_texte',
				'chambres_intro',
				'cta_titre',
				'chambre_1_nom',
				'chambre_1_description',
				'chambre_2_nom',
				'chambre_2_description',
				'service_1_description',
				'service_2_description',
				'service_3_description',
				'avis_1_auteur',
				'avis_1_texte',
				'avis_2_auteur',
				'avis_2_texte',
				'avis_3_auteur',
				'avis_3_texte',
				'faq_1_question',
				'faq_1_reponse',
				'faq_2_question',
				'faq_2_reponse',
				'faq_3_question',
				'faq_3_reponse',
				'zone_1_nom',
				'zone_1_frais',
				'zone_2_nom',
				'zone_2_frais',
				'zone_3_nom',
				'zone_3_frais',
			] as $key
		) {
			$texts[ $key ] = 'Texte ' . $key;
		}

		$content = [
			'facts' => [
				'quartier'    => 'Haie Vive',
				'annee'       => 2015,
				'specialites' => 'béninoise',
			],
			'texts' => $texts,
		];

		return [
			'identity' => [
				'name'    => 'Établissement Test',
				'phone'   => '+22990000000',
				'address' => [ 'city' => 'Cotonou', 'district' => 'Haie Vive' ],
			],
			'content'  => $content,
			'texts'    => $texts,
		];
	}

	/**
	 * Fournit chaque fichier de pattern.
	 *
	 * @return array<string, array{0: string}> Cas.
	 */
	public static function pattern_provider(): array {
		$cases = [];
		foreach ( glob( self::PATTERNS . '/*.php' ) ?: [] as $file ) {
			$cases[ basename( $file ) ] = [ $file ];
		}
		return $cases;
	}

	/**
	 * Tous les jetons d'un pattern se résolvent contre le jeu de données complet.
	 *
	 * @dataProvider pattern_provider
	 *
	 * @param string $file Chemin du pattern.
	 */
	public function test_pattern_tokens_resolve( string $file ): void {
		$source = (string) file_get_contents( $file );
		preg_match_all( '/\{\{maji:([a-z0-9_.\-]+)\}\}/', $source, $matches );
		$tokens = array_values( array_unique( $matches[1] ) );

		if ( [] === $tokens ) {
			$this->addToAssertionCount( 1 );
			return;
		}

		$data       = $this->fixture();
		$unresolved = [];
		foreach ( $tokens as $token ) {
			$probe  = '{{maji:' . $token . '}}';
			$result = Tokens::resolve( $probe, $data );
			if ( $result === $probe || '' === $result ) {
				$unresolved[] = $token;
			}
		}

		self::assertSame(
			[],
			$unresolved,
			'Jetons non résolus dans ' . basename( $file )
		);
	}
}
