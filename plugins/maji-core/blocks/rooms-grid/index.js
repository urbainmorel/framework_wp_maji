/**
 * Éditeur du bloc maji/rooms-grid (sans étape de build).
 * @param {Object} wp Objet global WordPress.
 */
( function( wp ) {
	'use strict';

	const el = wp.element.createElement;
	const __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/rooms-grid', {
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
						{ title: __( 'Réglages de la grille', 'maji-core' ) },
						el( wp.components.RangeControl, {
							label: __( 'Nombre de chambres', 'maji-core' ),
							min: 1,
							max: 24,
							value: props.attributes.count,
							onChange( value ) {
								props.setAttributes( { count: value } );
							},
						} ),
						el( wp.components.RangeControl, {
							label: __( 'Colonnes', 'maji-core' ),
							min: 1,
							max: 4,
							value: props.attributes.columns,
							onChange( value ) {
								props.setAttributes( { columns: value } );
							},
						} ),
						el( wp.components.TextControl, {
							label: __( 'Filtrer par équipement (slug)', 'maji-core' ),
							value: props.attributes.amenity,
							onChange( value ) {
								props.setAttributes( { amenity: value } );
							},
						} ),
						el( wp.components.ToggleControl, {
							label: __( 'Chambres mises en avant uniquement', 'maji-core' ),
							checked: props.attributes.featuredOnly,
							onChange( value ) {
								props.setAttributes( { featuredOnly: value } );
							},
						} )
					)
				),
				el( wp.components.Placeholder, {
					icon: 'grid-view',
					label: __( 'Grille de chambres', 'maji-core' ),
					instructions: __(
						'Les chambres publiées s’affichent ici sur le site public.',
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
