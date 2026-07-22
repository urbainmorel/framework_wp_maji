/**
 * Éditeur du bloc maji/reservation-form (sans étape de build).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/reservation-form', {
		edit: function ( props ) {
			var blockProps = wp.blockEditor.useBlockProps( {
				className: 'maji-form-placeholder',
			} );
			return el(
				'div',
				blockProps,
				el(
					wp.components.Placeholder,
					{
						icon: 'calendar-alt',
						label: __( 'Demande de réservation', 'maji-core' ),
						instructions: __(
							'Le formulaire (dates, personnes, coordonnées, consentement WhatsApp) est rendu sur le site public.',
							'maji-core'
						),
					},
					el(
						wp.components.ToggleControl,
						{
							label: __( 'Proposer le choix de la chambre', 'maji-core' ),
							checked: props.attributes.showRoomPicker,
							onChange: function ( value ) {
								props.setAttributes( { showRoomPicker: value } );
							},
						}
					)
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
