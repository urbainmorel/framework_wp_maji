<?php
/**
 * Tests des fragments JSON-LD SEO (V2-H1).
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Seo\SeoSchema;
use PHPUnit\Framework\TestCase;

/**
 * Extraction FAQ (depuis le contenu visible) et fragments d'entité.
 */
final class SeoSchemaTest extends TestCase {

	private const FAQ_HTML = '<!-- wp:details --><details class="wp-block-details"><summary>Avez-vous un parking&nbsp;?</summary>'
		. '<!-- wp:paragraph --><p>Oui, un <strong>parking sécurisé</strong> 24h/24.</p><!-- /wp:paragraph --></details><!-- /wp:details -->'
		. '<!-- wp:details --><details class="wp-block-details"><summary>Acceptez-vous les animaux&nbsp;?</summary>'
		. '<!-- wp:paragraph --><p>Les petits animaux sont bienvenus.</p><!-- /wp:paragraph --></details><!-- /wp:details -->';

	/**
	 * Les couples question/réponse sont extraits du contenu réel.
	 */
	public function test_faq_extraction(): void {
		$faqs = SeoSchema::faq_from_html( self::FAQ_HTML );
		self::assertCount( 2, $faqs );
		self::assertSame( 'Avez-vous un parking ?', $faqs[0]['q'] );
		self::assertSame( 'Oui, un parking sécurisé 24h/24.', $faqs[0]['a'] );
		self::assertSame( 'Acceptez-vous les animaux ?', $faqs[1]['q'] );
	}

	/**
	 * Aucune FAQ visible ⇒ aucune extraction (le balisage reflète l'affiché).
	 */
	public function test_no_faq_no_extraction(): void {
		self::assertSame( [], SeoSchema::faq_from_html( '<p>Une page sans FAQ.</p>' ) );
		self::assertSame( [], SeoSchema::faq_page( [] ) );
	}

	/**
	 * Le schéma FAQPage a la forme attendue.
	 */
	public function test_faq_page_shape(): void {
		$schema = SeoSchema::faq_page( SeoSchema::faq_from_html( self::FAQ_HTML ) );
		self::assertSame( 'FAQPage', $schema['@type'] );
		self::assertCount( 2, $schema['mainEntity'] );
		self::assertSame( 'Question', $schema['mainEntity'][0]['@type'] );
		self::assertSame( 'Answer', $schema['mainEntity'][0]['acceptedAnswer']['@type'] );
		self::assertSame( 'Oui, un parking sécurisé 24h/24.', $schema['mainEntity'][0]['acceptedAnswer']['text'] );
	}

	/**
	 * Offre de chambre : prix inclus seulement s'il est communiqué.
	 */
	public function test_room_offer(): void {
		$with = SeoSchema::room_offer( 'Suite Lagune', 45000, 'XOF' );
		self::assertSame( 'Offer', $with['@type'] );
		self::assertSame( 45000, $with['priceSpecification']['price'] );
		self::assertSame( 'XOF', $with['priceSpecification']['priceCurrency'] );

		$without = SeoSchema::room_offer( 'Chambre standard', 0, 'XOF' );
		self::assertArrayNotHasKey( 'priceSpecification', $without );
	}

	/**
	 * Équipement en LocationFeatureSpecification.
	 */
	public function test_amenity_feature(): void {
		$feat = SeoSchema::amenity_feature( 'Wi-Fi' );
		self::assertSame( 'LocationFeatureSpecification', $feat['@type'] );
		self::assertSame( 'Wi-Fi', $feat['name'] );
		self::assertTrue( $feat['value'] );
	}
}
