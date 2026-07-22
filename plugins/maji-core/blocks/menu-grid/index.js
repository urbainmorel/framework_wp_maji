/**
 * Éditeur du bloc maji/menu-grid (sans étape de build).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/menu-grid', {
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
						{ title: __( 'Réglages du menu', 'maji-core' ) },
						el( wp.components.RangeControl, {
							label: __( 'Nombre de plats', 'maji-core' ),
							min: 1,
							max: 48,
							value: props.attributes.count,
							onChange: function ( value ) {
								props.setAttributes( { count: value } );
							},
						} ),
						el( wp.components.RangeControl, {
							label: __( 'Colonnes', 'maji-core' ),
							min: 2,
							max: 4,
							value: props.attributes.columns,
							onChange: function ( value ) {
								props.setAttributes( { columns: value } );
							},
						} ),
						el( wp.components.TextControl, {
							label: __( 'Catégorie (slug)', 'maji-core' ),
							value: props.attributes.category,
							onChange: function ( value ) {
								props.setAttributes( { category: value } );
							},
						} ),
						el( wp.components.SelectControl, {
							label: __( 'Filtrer par badge', 'maji-core' ),
							value: props.attributes.badge,
							options: [
								{ value: '', label: __( 'Tous les plats', 'maji-core' ) },
								{ value: 'populaire', label: __( 'Populaire', 'maji-core' ) },
								{ value: 'epice', label: __( 'Épicé', 'maji-core' ) },
								{ value: 'nouveau', label: __( 'Nouveau', 'maji-core' ) },
							],
							onChange: function ( value ) {
								props.setAttributes( { badge: value } );
							},
						} )
					)
				),
				el( wp.components.Placeholder, {
					icon: 'screenoptions',
					label: __( 'Menu — grille photo', 'maji-core' ),
					instructions: __(
						'Les plats WooCommerce disponibles s’affichent ici sur le site public.',
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
