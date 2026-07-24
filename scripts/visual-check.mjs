#!/usr/bin/env node
/*
 * Contrôle visuel automatique (V2-F).
 *
 * Capture l'accueil d'un site provisionné en mobile ET desktop (above-the-fold
 * + pleine page), exécute des CONTRÔLES DURS objectifs, et écrit un rapport
 * JSON + les PNG. Sort en code ≠ 0 si un contrôle dur échoue → la livraison
 * doit être bloquée. Les captures et le rapport nourrissent l'auto-critique de
 * l'agent (voir docs/prompts/04-controle-visuel.md).
 *
 * Usage :
 *   node scripts/visual-check.mjs --url=https://site.test --slug=chez-fatou --out=reports/visual
 *
 * Playwright + Chromium sont fournis par le projet (aucun téléchargement).
 */

import { chromium } from 'playwright';
import { mkdirSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';

function arg( name, fallback = null ) {
	const hit = process.argv.find( ( a ) => a.startsWith( `--${ name }=` ) );
	return hit ? hit.slice( name.length + 3 ) : fallback;
}

const url = arg( 'url' );
if ( ! url ) {
	console.error( 'Erreur : --url=<adresse du site> est requis.' );
	process.exit( 2 );
}
const slug = arg( 'slug', 'site' );
const outDir = arg( 'out', 'reports/visual' );
mkdirSync( outDir, { recursive: true } );

const VIEWPORTS = [
	{ name: 'mobile', width: 390, height: 844, isMobile: true },
	{ name: 'desktop', width: 1440, height: 900, isMobile: false },
];

// Contraste WCAG (mêmes maths que le validateur PHP).
function channel( c ) {
	const s = c / 255;
	return s <= 0.03928 ? s / 12.92 : ( ( s + 0.055 ) / 1.055 ) ** 2.4;
}
function luminance( [ r, g, b ] ) {
	return 0.2126 * channel( r ) + 0.7152 * channel( g ) + 0.0722 * channel( b );
}
function parseRgb( value ) {
	const m = value.match( /rgba?\(([^)]+)\)/ );
	if ( ! m ) {
		return null;
	}
	const parts = m[ 1 ].split( ',' ).map( ( n ) => parseFloat( n.trim() ) );
	if ( parts.length >= 4 && parts[ 3 ] === 0 ) {
		return null; // transparent : contraste indéterminable.
	}
	return [ parts[ 0 ], parts[ 1 ], parts[ 2 ] ];
}
function contrast( fg, bg ) {
	const a = parseRgb( fg );
	const b = parseRgb( bg );
	if ( ! a || ! b ) {
		return null;
	}
	const la = luminance( a );
	const lb = luminance( b );
	return ( Math.max( la, lb ) + 0.05 ) / ( Math.min( la, lb ) + 0.05 );
}

const report = { url, slug, generated_at: new Date().toISOString(), viewports: {}, hard_failures: [], warnings: [] };
const browser = await chromium.launch();

for ( const vp of VIEWPORTS ) {
	const context = await browser.newContext( {
		viewport: { width: vp.width, height: vp.height },
		deviceScaleFactor: 2,
		isMobile: vp.isMobile,
	} );
	const page = await context.newPage();

	const consoleErrors = [];
	page.on( 'console', ( msg ) => {
		if ( msg.type() === 'error' ) {
			consoleErrors.push( msg.text() );
		}
	} );
	page.on( 'pageerror', ( err ) => consoleErrors.push( String( err ) ) );

	await page.goto( url, { waitUntil: 'networkidle', timeout: 30000 } );

	const foldPath = join( outDir, `${ slug }-${ vp.name }-fold.png` );
	const fullPath = join( outDir, `${ slug }-${ vp.name }-full.png` );
	await page.screenshot( { path: foldPath } );
	await page.screenshot( { path: fullPath, fullPage: true } );

	const probe = await page.evaluate( () => {
		const root = document.scrollingElement || document.documentElement;
		const imgs = Array.from( document.querySelectorAll( 'img' ) );
		const bodyStyle = getComputedStyle( document.body );
		const weight = performance
			.getEntriesByType( 'resource' )
			.reduce( ( sum, r ) => sum + ( r.transferSize || 0 ), 0 );
		return {
			scrollWidth: root.scrollWidth,
			innerWidth: window.innerWidth,
			hasH1: !! document.querySelector( 'h1' ),
			tokenResidue: document.body.innerText.includes( '{{maji:' ),
			sections: document.querySelectorAll( '.wp-block-group.alignfull, .wp-block-cover.alignfull' ).length,
			brokenImages: imgs.filter( ( i ) => i.complete && i.naturalWidth === 0 ).length,
			imagesMissingAlt: imgs.filter( ( i ) => i.getAttribute( 'alt' ) === null ).length,
			fg: bodyStyle.color,
			bg: bodyStyle.backgroundColor,
			transferBytes: Math.round( weight ),
		};
	} );

	const horizontalOverflow = probe.scrollWidth > probe.innerWidth + 2;
	const textContrast = contrast( probe.fg, probe.bg );

	const v = {
		screenshots: { fold: foldPath, full: fullPath },
		console_errors: consoleErrors,
		horizontal_overflow: horizontalOverflow,
		has_hero_h1: probe.hasH1,
		token_residue: probe.tokenResidue,
		sections: probe.sections,
		broken_images: probe.brokenImages,
		images_missing_alt: probe.imagesMissingAlt,
		text_contrast: textContrast ? Math.round( textContrast * 100 ) / 100 : null,
		transfer_bytes: probe.transferBytes,
	};
	report.viewports[ vp.name ] = v;

	// Contrôles DURS (bloquants).
	if ( consoleErrors.length ) {
		report.hard_failures.push( `${ vp.name } : ${ consoleErrors.length } erreur(s) console/JS.` );
	}
	if ( horizontalOverflow ) {
		report.hard_failures.push( `${ vp.name } : défilement horizontal (${ probe.scrollWidth } > ${ probe.innerWidth }).` );
	}
	if ( probe.tokenResidue ) {
		report.hard_failures.push( `${ vp.name } : jeton {{maji:*}} résiduel dans le rendu.` );
	}
	if ( ! probe.hasH1 ) {
		report.hard_failures.push( `${ vp.name } : aucun titre H1 (hero manquant ?).` );
	}
	if ( probe.brokenImages > 0 ) {
		report.hard_failures.push( `${ vp.name } : ${ probe.brokenImages } image(s) cassée(s).` );
	}
	if ( textContrast !== null && textContrast < 4.5 ) {
		report.hard_failures.push( `${ vp.name } : contraste du texte courant insuffisant (${ v.text_contrast }:1).` );
	}

	// AVERTISSEMENTS (pour l'auto-critique, non bloquants).
	if ( probe.imagesMissingAlt > 0 ) {
		report.warnings.push( `${ vp.name } : ${ probe.imagesMissingAlt } image(s) sans attribut alt.` );
	}
	if ( probe.sections < 3 ) {
		report.warnings.push( `${ vp.name } : seulement ${ probe.sections } section(s) — page peut-être trop courte.` );
	}

	await context.close();
}

await browser.close();

writeFileSync( join( outDir, `${ slug }-report.json` ), JSON.stringify( report, null, 2 ) );

const ok = report.hard_failures.length === 0;
console.log( `Contrôle visuel ${ slug } : ${ ok ? 'OK' : 'ÉCHEC' }` );
if ( ! ok ) {
	report.hard_failures.forEach( ( f ) => console.error( ' ✗ ' + f ) );
}
report.warnings.forEach( ( w ) => console.warn( ' ! ' + w ) );
console.log( `Rapport : ${ join( outDir, `${ slug }-report.json` ) }` );

process.exit( ok ? 0 : 1 );
