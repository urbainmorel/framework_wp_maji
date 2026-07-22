<?php
/**
 * Tests du moteur de jetons {{maji:*}}.
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Dna\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * Résolution et détection des jetons.
 */
final class TokensTest extends TestCase {

	/**
	 * Données de test.
	 *
	 * @return array<string, mixed> Données.
	 */
	private function data(): array {
		return [
			'identity' => [
				'name'    => 'Hôtel Atlantique',
				'address' => [ 'city' => 'Cotonou' ],
			],
			'content'  => [
				'facts' => [
					'quartier'    => 'Haie Vive',
					'annee'       => 2016,
					'specialites' => [ 'poisson braisé', 'sauce arachide' ],
				],
			],
			'texts'    => [
				'content.texts.hero_tagline' => 'Un séjour inoubliable.',
			],
		];
	}

	/**
	 * Résolution d'un chemin simple et imbriqué.
	 */
	public function test_resolves_nested_paths(): void {
		$content = '<h1>{{maji:identity.name}}</h1><p>{{maji:content.facts.quartier}} — {{maji:identity.address.city}}</p>';
		$result  = Tokens::resolve( $content, $this->data() );
		self::assertSame( '<h1>Hôtel Atlantique</h1><p>Haie Vive — Cotonou</p>', $result );
	}

	/**
	 * Les surcharges `texts` priment.
	 */
	public function test_texts_override_wins(): void {
		$result = Tokens::resolve( '{{maji:content.texts.hero_tagline}}', $this->data() );
		self::assertSame( 'Un séjour inoubliable.', $result );
	}

	/**
	 * Les scalaires non-chaîne sont convertis, les listes jointes.
	 */
	public function test_scalar_and_list_conversion(): void {
		self::assertSame( '2016', Tokens::resolve( '{{maji:content.facts.annee}}', $this->data() ) );
		self::assertSame(
			'poisson braisé, sauce arachide',
			Tokens::resolve( '{{maji:content.facts.specialites}}', $this->data() )
		);
	}

	/**
	 * Un jeton inconnu reste en place et est détecté par find().
	 */
	public function test_unknown_token_is_kept_and_found(): void {
		$content = '{{maji:identity.name}} {{maji:content.texts.inconnu}}';
		$result  = Tokens::resolve( $content, $this->data() );
		self::assertStringContainsString( '{{maji:content.texts.inconnu}}', $result );
		self::assertSame( [ 'content.texts.inconnu' ], Tokens::find( $result ) );
	}

	/**
	 * find() retourne un tableau vide sans jetons.
	 */
	public function test_find_empty(): void {
		self::assertSame( [], Tokens::find( 'Aucun jeton ici.' ) );
	}
}
