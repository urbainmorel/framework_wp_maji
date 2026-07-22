/**
 * Éditeur du bloc maji/opening-hours (sans étape de build).
 */
( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'maji/opening-hours', {
		edit: function () {
			var blockProps = wp.blockEditor.useBlockProps();
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
		save: function () {
			return null;
		},
	} );
} )( window.wp );
