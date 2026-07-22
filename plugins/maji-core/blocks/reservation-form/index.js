/**
 * Éditeur du bloc maji/reservation-form (sans étape de build).
 * @param {Object} wp Objet global WordPress.
 */
( function( wp ) {
	'use strict';

	const el = wp.element.createElement;
	const __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/reservation-form', {
		edit( props ) {
			const blockProps = wp.blockEditor.useBlockProps( {
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
							onChange( value ) {
								props.setAttributes( { showRoomPicker: value } );
							},
						}
					)
				)
			);
		},
		save() {
			return null;
		},
	} );
}( window.wp ) );
