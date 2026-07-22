<?php
/**
 * Génération d'URL WhatsApp click-to-chat et gabarits de messages.
 *
 * Aucun message n'est envoyé depuis WordPress : ce module produit
 * uniquement des liens `wa.me` — l'envoi est le rôle de n8n.
 *
 * @package maji-core
 */

declare(strict_types=1);

namespace MAJI\Core\WhatsApp;

use MAJI\Core\Settings\Settings;

/**
 * Liens wa.me contextualisés (page, plat, chambre, suivi de commande).
 */
final class WhatsApp {

	/**
	 * Constructeur.
	 *
	 * @param Settings $settings Réglages.
	 */
	public function __construct( private readonly Settings $settings ) {}

	/**
	 * Gabarits de messages par contexte.
	 *
	 * @return array<string, string> Gabarits (variables {{name}}, {{item}}, {{establishment}}).
	 */
	public static function templates(): array {
		return [
			'contact' => __( 'Bonjour {{establishment}}, je vous contacte depuis votre site web.', 'maji-core' ),
			'dish'    => __( 'Bonjour {{establishment}}, je souhaite commander : {{item}}.', 'maji-core' ),
			'room'    => __( 'Bonjour {{establishment}}, je souhaite réserver la chambre « {{item}} ».', 'maji-core' ),
			'order'   => __( 'Bonjour {{establishment}}, je souhaite des nouvelles de ma commande n°{{item}}.', 'maji-core' ),
			'booking' => __( 'Bonjour {{establishment}}, je souhaite réserver une table.', 'maji-core' ),
		];
	}

	/**
	 * Construit l'URL wa.me pour un contexte donné.
	 *
	 * @param string $context Contexte (contact, dish, room, order, booking).
	 * @param string $item    Élément concerné (nom du plat, de la chambre…).
	 * @return string URL wa.me ou chaîne vide si aucun numéro configuré.
	 */
	public function url( string $context = 'contact', string $item = '' ): string {
		$number = (string) $this->settings->get( 'establishment.whatsapp', '' );
		if ( '' === $number ) {
			$number = (string) $this->settings->get( 'establishment.phone', '' );
		}
		$number = preg_replace( '/[^0-9]/', '', $number ) ?? '';
		if ( '' === $number ) {
			return '';
		}

		$templates = self::templates();
		$template  = $templates[ $context ] ?? $templates['contact'];
		$message   = strtr(
			$template,
			[
				'{{establishment}}' => (string) $this->settings->get( 'establishment.name', '' ),
				'{{item}}'          => $item,
			]
		);

		return 'https://wa.me/' . $number . '?text=' . rawurlencode( $message );
	}
}
