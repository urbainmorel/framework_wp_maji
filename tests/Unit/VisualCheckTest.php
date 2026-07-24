<?php
/**
 * Garde-fou du contrôle visuel automatique (V2-F).
 *
 * Vérifie que l'outil de capture, la grille d'auto-critique et leur intégration
 * dans l'agent orchestrateur sont présents et cohérents.
 *
 * @package maji
 */

declare(strict_types=1);

namespace MAJI\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Présence et cohérence de l'outillage de contrôle visuel.
 */
final class VisualCheckTest extends TestCase {

	private const ROOT = __DIR__ . '/../..';

	/**
	 * L'outil de capture et le prompt d'auto-critique existent.
	 */
	public function test_artifacts_exist(): void {
		self::assertFileExists( self::ROOT . '/scripts/visual-check.mjs' );
		self::assertFileExists( self::ROOT . '/docs/prompts/04-controle-visuel.md' );
	}

	/**
	 * Le script couvre mobile + desktop et tous les contrôles durs.
	 */
	public function test_script_covers_hard_checks(): void {
		$js = (string) file_get_contents( self::ROOT . '/scripts/visual-check.mjs' );

		self::assertStringContainsString( "name: 'mobile'", $js );
		self::assertStringContainsString( "name: 'desktop'", $js );

		// Contrôles durs objectifs.
		self::assertStringContainsString( 'scrollWidth', $js );      // défilement horizontal
		self::assertStringContainsString( 'console_errors', $js );   // erreurs JS
		self::assertStringContainsString( '{{maji:', $js );          // jetons résiduels
		self::assertStringContainsString( 'hasH1', $js );            // hero présent
		self::assertStringContainsString( 'brokenImages', $js );     // images cassées
		self::assertStringContainsString( 'contrast', $js );         // contraste texte

		// Sortie non-zéro en cas d'échec dur (bloque la livraison).
		self::assertStringContainsString( 'process.exit( ok ? 0 : 1 )', $js );
	}

	/**
	 * L'agent orchestrateur (prompt 03) déclenche le contrôle visuel.
	 */
	public function test_orchestrator_invokes_visual_check(): void {
		$prompt = (string) file_get_contents( self::ROOT . '/docs/prompts/03-agent-orchestrateur.md' );
		self::assertStringContainsString( 'scripts/visual-check.mjs', $prompt );
		self::assertStringContainsString( '04-controle-visuel.md', $prompt );
	}
}
