# Journal des décisions

Décisions prises par l'agent lors de l'implémentation (STI §0.3 : ambiguïté ⇒ option la plus simple + trace ici).

## M1

- **PHPUnit 9.6 plutôt que 10** : la suite WordPress officielle (`yoast/phpunit-polyfills`) et les tests unitaires purs ciblent PHPUnit 9 pour une compatibilité maximale avec l'écosystème WP actuel.
- **Autoloader interne de secours dans `maji-core.php`** : en développement (repo cloné sans `composer install` dans le plugin), un `spl_autoload_register` PSR-4 minimal évite un fatal ; le build de release embarque le vrai autoload Composer.
- **`phpstan.neon.dist` + stubs WooCommerce** : l'analyse statique utilise `szepeviktor/phpstan-wordpress` et les stubs Woo pour couvrir le mode restaurant sans installer WordPress.
- **wp-env sur WP 6.7** : la STI exige WP ≥ 6.6 et les deux dernières majeures en CI ; le développement local se fait sur la dernière stable.
- **Lockfiles non commités** : `composer.lock` et `package-lock.json` sont ignorés ; les contraintes de versions dans `composer.json`/`package.json` font foi. Simplifie la CI multi-PHP et le développement en environnements réseau restreints.
- **Hooks en underscores (`maji_init`)** plutôt que `maji/init` : conformité WPCS (`ValidHookName`), la STI n'impose pas de format de nom de hook.

## M2

- **Polices via paquets Fontsource** (WOFF2 latin, graisses 400/700 uniquement, 2 fichiers par famille) : licences SIL OFL respectées, budget performance tenu (≈ 90 Ko chargés par site pour une paire).
- **Le concept de « paire » vit dans `fonts.json` + les DA/ADN** : theme.json déclare les 11 familles individuellement (il n'a pas de notion de paire) ; la paire active est appliquée par la style variation puis par les global styles utilisateur.

## M3

- **Blocs sans étape de build** : les scripts éditeur utilisent `wp.element.createElement` (pas de JSX) et les scripts front sont en vanilla — pas de webpack, dépendances déclarées via `*.asset.php`. Simplicité, robustesse en environnement contraint, budget JS minimal. `@wordpress/scripts` reste utilisé pour ESLint.
- **Statuts de réservation en meta `status`** (`new|confirmed|declined|cancelled`) plutôt qu'en post_status custom : plus simple, conforme au schéma STI §4.2, filtrage admin via meta_query.

## M4

- **Jetons remplacés à l'import, pas au rendu** : les pages publiées contiennent le texte final (cache de page friendly, aucun jeton ne peut fuir en front) ; `wp maji provision` échoue si un jeton subsiste.
- **Marqueurs `data-maji-media="clé"`** sur les images des patterns (placeholders SVG neutres du thème en attendant) : l'import remplace les `src` par les médias du seed. Les aperçus de patterns restent visuels dans l'inserter, et aucun jeton ne peut rester dans un attribut src.

## M5

- **Relances webhook via Action Scheduler si présent, sinon WP-Cron** (`wp_schedule_single_event`) : Action Scheduler arrive avec WooCommerce ; les sites hôtel purs n'embarquent pas la bibliothèque pour trois relances espacées.
- **« Renvoyer » reconstruit le payload depuis l'entité source** (colonne `entity_id` du journal) : le journal ne stocke jamais le payload complet (hash + extrait seulement, conformité STI §4.5/§11).

## M6

- **Validateur d'ADN en PHP pur** (mêmes règles que `dna/schemas/dna.schema.json`) plutôt qu'un moteur JSON Schema embarqué : zéro dépendance exotique (N2), référentiels injectables et testables ; le fichier schema sert de contrat documenté pour l'outillage externe.
- **Variantes header/footer par templates en base** (`wp_template`) : l'ADN choisit `header-01..04`/`footer-01..03` en surchargeant les templates du thème dans la base — jamais dans les fichiers (STI §5.2).
- **Médias de démo générés (WebP dégradés)** : pas de photos tierces dans le dépôt (droits) ; les seeds sont fonctionnels et légers (≈ 165 Ko), les vraies photos client remplacent les clés `media` au moment de la production.

## V2-A — Empreinte anti-clones enrichie

- **Empreinte à 11 axes** (voir `docs/DIVERSITE.md` §4) : la table des poids de `Registry` passe de 6 à 11 axes. La « trilogie de première impression » `da + hero + font_pair` somme exactement au seuil (0.30 + 0.22 + 0.18 = 0.70), garantissant que deux sites ne partagent jamais à la fois DA, hero et paire typo. Les 8 axes secondaires (`palette_hue_bucket`, `header`, `footer`, `image_treatment_bucket`, `section_bg_rhythm`, `spacing_mood`, `composition_hash`, `radius_scale`) affinent la détection des quasi-clones. Seuil de blocage inchangé (0,70).
- **`composition_hash` remplace `section_order_hash`** : le hash intègre désormais l'ordre des sections de l'accueil **et** la variante `section_style`, pour distinguer deux accueils au même ordre mais au style de sections différent.
- **Rétrocompatibilité par axes vides** : un axe optionnel absent de l'ADN produit une chaîne vide dans l'empreinte ; `similarity()` ne compte que les axes égaux **et non vides**. Un ADN V1 se fingerprint donc sans erreur et sans faux positif — aucun changement d'API (`fingerprint`, `similarity`, `check`, `suggestions` conservés).
- **Champs d'ADN `section_bg_rhythm` / `section_style` permis dès V2-A** : ajoutés au schéma (`dna.schema.json`) et au validateur PHP comme champs **optionnels**, pour que les ADN qui les portent soient valides et alimentent l'empreinte. Leur *application* visuelle (global styles + block styles) relève du jalon V2-B ; V2-A reste « sans nouveau design » (les ADN existants sont inchangés).
