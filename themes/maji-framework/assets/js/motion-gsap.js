/*
 * Motion premium — moteur GSAP (V2-G2), niveau « expressive ».
 *
 * Chargé en defer, uniquement quand design.motion = expressive. Discipline :
 *  - toutes les tweens vivent dans gsap.matchMedia('(prefers-reduced-motion:
 *    no-preference)') → coupées pour l'accessibilité (WCAG 2.3.3) ;
 *  - on n'anime QUE transform (x/y/scale) et opacity → 60 fps, pas de reflow ;
 *  - ScrollTrigger.batch pour les révélations (une instance, pas N observers) ;
 *  - durée/distance viennent des tokens du thème (cohérence par DA).
 */
( function () {
	'use strict';

	if ( ! window.gsap || ! window.ScrollTrigger ) {
		return;
	}

	var gsap = window.gsap;
	gsap.registerPlugin( window.ScrollTrigger );

	// Lecture des tokens de motion de la DA active (avec repli).
	var styles = window.getComputedStyle( document.documentElement );
	function token( name, fallback ) {
		var value = styles.getPropertyValue( name );
		return value ? value.trim() : fallback;
	}
	var distance = parseInt( token( '--wp--custom--maji--motion--distance', '20px' ), 10 ) || 20;
	var revealMs = parseInt( token( '--wp--custom--maji--motion--reveal', '600ms' ), 10 ) || 600;
	var staggerMs = parseInt( token( '--wp--custom--maji--motion--stagger', '70ms' ), 10 ) || 70;

	var start = function () {
		var mm = gsap.matchMedia();

		// Rien ne bouge si l'utilisateur demande un mouvement réduit.
		mm.add( '(prefers-reduced-motion: no-preference)', function () {
			var sections = gsap.utils.toArray(
				'.wp-block-post-content > .wp-block-group.alignfull, .wp-block-post-content > .wp-block-cover.alignfull, .entry-content > .wp-block-group.alignfull, .entry-content > .wp-block-cover.alignfull'
			);

			// Révélation des sections, par lots, à l'entrée dans le viewport.
			gsap.set( sections, { opacity: 0, y: distance } );
			window.ScrollTrigger.batch( sections, {
				start: 'top 85%',
				once: true,
				onEnter: function ( batch ) {
					gsap.to( batch, {
						opacity: 1,
						y: 0,
						duration: revealMs / 1000,
						ease: 'power3.out',
						stagger: staggerMs / 1000,
						overwrite: true,
					} );
				},
			} );

			// Ken Burns : très léger zoom des images de cover pendant le scroll.
			gsap.utils.toArray( '.wp-block-cover.alignfull .wp-block-cover__image-background' ).forEach(
				function ( img ) {
					gsap.fromTo(
						img,
						{ scale: 1 },
						{
							scale: 1.08,
							ease: 'none',
							scrollTrigger: {
								trigger: img.closest( '.wp-block-cover' ),
								start: 'top bottom',
								end: 'bottom top',
								scrub: true,
							},
						}
					);
				}
			);

			// Nettoyage à la sortie du contexte (changement de media query).
			return function () {
				gsap.set( sections, { clearProps: 'opacity,transform' } );
			};
		} );
	};

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
} )();
