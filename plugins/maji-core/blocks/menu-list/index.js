/**
 * Éditeur du bloc maji/menu-list (sans étape de build).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/menu-list', {
		edit: function () {
			var blockProps = wp.blockEditor.useBlockProps();
			return el(
				'div',
				blockProps,
				el( wp.components.Placeholder, {
					icon: 'list-view',
					label: __( 'Menu du restaurant', 'maji-core' ),
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
