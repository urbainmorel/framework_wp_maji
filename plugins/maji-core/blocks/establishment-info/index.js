/**
 * Éditeur du bloc maji/establishment-info (sans étape de build).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/establishment-info', {
		edit: function ( props ) {
			var blockProps = wp.blockEditor.useBlockProps();
			var labels = {
				coordonnees: __( 'Coordonnées complètes', 'maji-core' ),
				phone: __( 'Téléphone', 'maji-core' ),
				adresse: __( 'Adresse', 'maji-core' ),
				reseaux: __( 'Réseaux sociaux', 'maji-core' ),
			};
			return el(
				'div',
				blockProps,
				el(
					wp.blockEditor.InspectorControls,
					null,
					el(
						wp.components.PanelBody,
						{ title: __( 'Variante', 'maji-core' ) },
						el( wp.components.SelectControl, {
							label: __( 'Contenu affiché', 'maji-core' ),
							value: props.attributes.variant,
							options: Object.keys( labels ).map( function ( key ) {
								return { value: key, label: labels[ key ] };
							} ),
							onChange: function ( value ) {
								props.setAttributes( { variant: value } );
							},
						} )
					)
				),
				el(
					'span',
					{ className: 'maji-info-placeholder' },
					'ℹ ' + ( labels[ props.attributes.variant ] || labels.coordonnees )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
