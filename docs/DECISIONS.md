# Journal des décisions

Décisions prises par l'agent lors de l'implémentation (STI §0.3 : ambiguïté ⇒ option la plus simple + trace ici).

## M1

- **PHPUnit 9.6 plutôt que 10** : la suite WordPress officielle (`yoast/phpunit-polyfills`) et les tests unitaires purs ciblent PHPUnit 9 pour une compatibilité maximale avec l'écosystème WP actuel.
- **Autoloader interne de secours dans `maji-core.php`** : en développement (repo cloné sans `composer install` dans le plugin), un `spl_autoload_register` PSR-4 minimal évite un fatal ; le build de release embarque le vrai autoload Composer.
- **`phpstan.neon.dist` + stubs WooCommerce** : l'analyse statique utilise `szepeviktor/phpstan-wordpress` et les stubs Woo pour couvrir le mode restaurant sans installer WordPress.
- **wp-env sur WP 6.7** : la STI exige WP ≥ 6.6 et les deux dernières majeures en CI ; le développement local se fait sur la dernière stable.
- **Lockfiles non commités** : `composer.lock` et `package-lock.json` sont ignorés ; les contraintes de versions dans `composer.json`/`package.json` font foi. Simplifie la CI multi-PHP et le développement en environnements réseau restreints.
- **Hooks en underscores (`maji_init`)** plutôt que `maji/init` : conformité WPCS (`ValidHookName`), la STI n'impose pas de format de nom de hook.
