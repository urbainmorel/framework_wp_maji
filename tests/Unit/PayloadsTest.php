<?php
/**
 * Tests des constructeurs de payloads (fixtures Annexe A).
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use MAJI\Core\Webhooks\Payloads;
use PHPUnit\Framework\TestCase;

/**
 * Conformité au contrat maji-webhook/1.
 */
final class PayloadsTest extends TestCase {

	/**
	 * Fixture commande (Annexe A.1).
	 *
	 * @return array<string, mixed> Données.
	 */
	private function order_fixture(): array {
		return [
			'id'          => 1042,
			'number'      => '1042',
			'status'      => 'processing',
			'customer'    => [
				'name'             => 'Ayo K.',
				'phone'            => '+22901400000',
				'whatsapp_consent' => true,
			],
			'fulfillment' => [
				'mode'        => 'delivery',
				'zone'        => 'Haie Vive',
				'fee'         => 500,
				'address'     => 'Rue 12, Cotonou',
				'pickup_time' => null,
			],
			'items'       => [
				[
					'product_id' => 88,
					'name'       => 'Poulet braisé',
					'variation'  => 'Entier',
					'qty'        => 2,
					'unit_price' => 3500,
					'total'      => 7000,
				],
			],
			'totals'      => [
				'subtotal' => 7000,
				'delivery' => 500,
				'total'    => 7500,
				'currency' => 'XOF',
			],
			'payment'     => [ 'method' => 'cod' ],
			'note'        => 'Sans piment',
		];
	}

	/**
	 * Enveloppe order.created conforme à l'Annexe A.1.
	 */
	public function test_order_created_envelope(): void {
		$payload = Payloads::order_created( 'saveurs-du-benin', '2026-07-21T12:30:00+01:00', $this->order_fixture() );

		self::assertSame( 'maji-webhook/1', $payload['schema'] );
		self::assertSame( 'order.created', $payload['event'] );
		self::assertSame( 'saveurs-du-benin', $payload['site'] );
		self::assertSame( '2026-07-21T12:30:00+01:00', $payload['sent_at'] );
		self::assertSame( 1042, $payload['order']['id'] );
		self::assertSame( 'cod', $payload['order']['payment']['method'] );
		self::assertSame( 'XOF', $payload['order']['totals']['currency'] );
		self::assertSame( 'Haie Vive', $payload['order']['fulfillment']['zone'] );
	}

	/**
	 * Enveloppe reservation.created conforme à l'Annexe A.2.
	 */
	public function test_reservation_created_envelope(): void {
		$reservation = [
			'id'       => 87,
			'type'     => 'room',
			'room'     => [
				'id'   => 12,
				'name' => 'Suite Premium',
			],
			'checkin'  => '2026-08-02',
			'checkout' => '2026-08-05',
			'guests'   => 2,
			'customer' => [
				'name'             => 'Awa D.',
				'phone'            => '+22901400001',
				'email'            => 'awa@example.com',
				'whatsapp_consent' => true,
			],
			'message'  => 'Vue mer si possible',
			'status'   => 'new',
		];
		$payload     = Payloads::reservation_created( 'hotel-atlantique', '2026-07-21T12:30:00+01:00', $reservation );

		self::assertSame( 'reservation.created', $payload['event'] );
		self::assertSame( 'hotel-atlantique', $payload['site'] );
		self::assertSame( 'Suite Premium', $payload['reservation']['room']['name'] );
		self::assertSame( 'new', $payload['reservation']['status'] );
	}

	/**
	 * status_changed enrichit l'enveloppe d'origine (Annexe A.3).
	 */
	public function test_status_changed(): void {
		$original = Payloads::order_created( 'saveurs-du-benin', '2026-07-21T12:30:00+01:00', $this->order_fixture() );
		$payload  = Payloads::status_changed( $original, 'new', 'confirmed', 'admin', '2026-07-21T13:00:00+01:00' );

		self::assertSame( 'order.status_changed', $payload['event'] );
		self::assertSame( 'new', $payload['previous_status'] );
		self::assertSame( 'confirmed', $payload['new_status'] );
		self::assertSame( 'admin', $payload['changed_by'] );
		self::assertSame( '2026-07-21T13:00:00+01:00', $payload['sent_at'] );
		// L'entité d'origine est conservée.
		self::assertSame( 1042, $payload['order']['id'] );
	}

	/**
	 * Signature HMAC-SHA256 vérifiable.
	 */
	public function test_signature(): void {
		$body   = '{"schema":"maji-webhook/1"}';
		$secret = 'secret-de-test';
		$sig    = Payloads::sign( $body, $secret );

		self::assertStringStartsWith( 'sha256=', $sig );
		self::assertSame( 'sha256=' . hash_hmac( 'sha256', $body, $secret ), $sig );
		// Un corps différent produit une signature différente.
		self::assertNotSame( $sig, Payloads::sign( $body . ' ', $secret ) );
	}
}
