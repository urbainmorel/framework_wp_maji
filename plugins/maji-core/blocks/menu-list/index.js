/**
 * Éditeur du bloc maji/menu-list (sans étape de build).
 * @param {Object} wp Objet global WordPress.
 */
( function( wp ) {
	'use strict';

	const el = wp.element.createElement;
	const __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/menu-list', {
		edit() {
			const blockProps = wp.blockEditor.useBlockProps();
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
		save() {
			return null;
		},
	} );
}( window.wp ) );
