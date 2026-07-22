/**
 * Éditeur du bloc maji/opening-hours (sans étape de build).
 * @param {Object} wp Objet global WordPress.
 */
( function( wp ) {
	'use strict';

	const el = wp.element.createElement;
	const __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/opening-hours', {
		edit() {
			const blockProps = wp.blockEditor.useBlockProps();
			return el(
				'div',
				blockProps,
				el( wp.components.Placeholder, {
					icon: 'clock',
					label: __( 'Horaires d’ouverture', 'maji-core' ),
					instructions: __(
						'Les horaires proviennent des réglages MAJI → Établissement.',
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
