/**
 * Éditeur du bloc maji/whatsapp-button (sans étape de build).
 * @param {Object} wp Objet global WordPress.
 */
( function( wp ) {
	'use strict';

	const el = wp.element.createElement;
	const __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/whatsapp-button', {
		edit( props ) {
			const blockProps = wp.blockEditor.useBlockProps();
			return el(
				'div',
				blockProps,
				el(
					wp.blockEditor.InspectorControls,
					null,
					el(
						wp.components.PanelBody,
						{ title: __( 'Réglages WhatsApp', 'maji-core' ) },
						el( wp.components.SelectControl, {
							label: __( 'Contexte du message', 'maji-core' ),
							value: props.attributes.context,
							options: [
								{ value: 'contact', label: __( 'Contact général', 'maji-core' ) },
								{ value: 'dish', label: __( 'Plat', 'maji-core' ) },
								{ value: 'room', label: __( 'Chambre', 'maji-core' ) },
								{ value: 'booking', label: __( 'Réservation de table', 'maji-core' ) },
							],
							onChange( value ) {
								props.setAttributes( { context: value } );
							},
						} ),
						el( wp.components.TextControl, {
							label: __( 'Élément (plat, chambre…)', 'maji-core' ),
							value: props.attributes.item,
							onChange( value ) {
								props.setAttributes( { item: value } );
							},
						} ),
						el( wp.components.TextControl, {
							label: __( 'Libellé du bouton', 'maji-core' ),
							value: props.attributes.label,
							onChange( value ) {
								props.setAttributes( { label: value } );
							},
						} )
					)
				),
				el(
					'span',
					{ className: 'maji-whatsapp__link wp-element-button' },
					props.attributes.label || __( 'Discuter sur WhatsApp', 'maji-core' )
				)
			);
		},
		save() {
			return null;
		},
	} );
}( window.wp ) );
