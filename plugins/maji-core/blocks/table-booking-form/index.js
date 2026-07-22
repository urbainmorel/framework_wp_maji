/**
 * Éditeur du bloc maji/table-booking-form (sans étape de build).
 * @param {Object} wp Objet global WordPress.
 */
( function( wp ) {
	'use strict';

	const el = wp.element.createElement;
	const __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/table-booking-form', {
		edit() {
			const blockProps = wp.blockEditor.useBlockProps( {
				className: 'maji-form-placeholder',
			} );
			return el(
				'div',
				blockProps,
				el( wp.components.Placeholder, {
					icon: 'food',
					label: __( 'Réservation de table', 'maji-core' ),
					instructions: __(
						'Le formulaire (date, heure, couverts, coordonnées) est rendu sur le site public.',
						'maji-core'
					),
				} )
			);
		},
		save() {
			return null;
		},
	} );
}( window.wp ) );
