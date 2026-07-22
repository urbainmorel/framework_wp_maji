/**
 * Éditeur du bloc maji/table-booking-form (sans étape de build).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/table-booking-form', {
		edit: function () {
			var blockProps = wp.blockEditor.useBlockProps( {
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
		save: function () {
			return null;
		},
	} );
} )( window.wp );
