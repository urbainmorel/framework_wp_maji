/**
 * Éditeur du bloc maji/whatsapp-button (sans étape de build).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/whatsapp-button', {
		edit: function ( props ) {
			var blockProps = wp.blockEditor.useBlockProps();
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
							onChange: function ( value ) {
								props.setAttributes( { context: value } );
							},
						} ),
						el( wp.components.TextControl, {
							label: __( 'Élément (plat, chambre…)', 'maji-core' ),
							value: props.attributes.item,
							onChange: function ( value ) {
								props.setAttributes( { item: value } );
							},
						} ),
						el( wp.components.TextControl, {
							label: __( 'Libellé du bouton', 'maji-core' ),
							value: props.attributes.label,
							onChange: function ( value ) {
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
		save: function () {
			return null;
		},
	} );
} )( window.wp );
