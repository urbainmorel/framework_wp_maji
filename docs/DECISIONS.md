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

## Automatisation des releases

- **release-please (Google) plutôt qu'un workflow maison de tag-sur-bump** : le
  projet suit déjà Conventional Commits + Keep a Changelog ; release-please en tire
  automatiquement le bump SemVer, le CHANGELOG et la release via une « Release PR »
  fusionnable. Type `simple` + `extra-files` pour porter la version dans les en-têtes
  WordPress (`style.css`, `maji-core.php`) — aucune dépendance PHP ajoutée (action GH).
- **Build des zips dans le workflow release-please** (étapes conditionnées à
  `release_created`) plutôt qu'en s'appuyant sur `release.yml` : un tag créé par
  `GITHUB_TOKEN` ne déclenche pas un autre workflow (`on: push: tags`). Enchaîner le
  build dans la même exécution garantit l'attache des zips. `release.yml` est conservé
  comme filet manuel (tag poussé à la main).
- **Déclencheur calé sur la branche par défaut du dépôt** (`if: github.ref ==
  refs/heads/<default>`) plutôt qu'un nom de branche en dur : robuste à un futur
  passage `main`, sans édition du workflow.

## V2-B — Application des leviers `section_bg_rhythm` / `section_style`

- **Application à l'import, dans le contenu en base** (classe pure `SectionStyler`
  appelée par `DnaApplier`) plutôt que par réécriture des patterns ou du thème :
  respecte le contrat §5.2 (personnalisation en base, jamais dans les fichiers du
  thème) et reste déterministe + testable sans WordPress (10 tests unitaires).
- **Rythme de fonds via la palette existante, sans CSS sur mesure** : les sections
  *neutres* (fond `base`) alternent parmi `base`/`surface`/`surface-alt`, tous des
  neutres clairs déjà contrôlés WCAG contre `ink` par le validateur. Les sections à
  fond intentionnel (CTA `accent`, heros en image) et les heros ne sont jamais
  recolorés et ne décalent pas la parité. Aucun risque de contraste, aucune règle CSS
  de rythme à maintenir.
- **`section_style` via block styles du plugin** (`core/group` : `maji-net`,
  `maji-ombre`, `maji-minimal`), CSS 100 % tokens (`--wp--custom--maji--shadow--card`,
  `--wp--preset--color--surface-alt`). `SectionStyler` pose la classe `is-style-maji-*`
  sur les sections hors heros ; `auto` = aucune surcharge. Le CSS fin (séparateurs)
  reste à valider visuellement en wp-env — le mécanisme, lui, est neutre et sûr.
- **6 ADN d'exemple/modèles enrichis** des deux champs (valeurs variées) : nourrit
  aussi l'empreinte anti-clones V2-A (max de similarité par paire : 0,39).

## V2-C — +7 directions artistiques (3 → 10)

- **7 nouvelles DA curées** (`da-lagune-fraiche`, `da-nuit-cuivre`, `da-savane-doree`,
  `da-terracotta-vive`, `da-ardoise-moderne`, `da-jardin-botanique`, `da-onyx-emeraude`)
  livrées comme style variations (`themes/maji-framework/styles/*.json`) : palette
  sémantique 10 slugs, 2 duotones, échelles rayons/ombres/espacement et typographie
  propres à chaque DA (parmi les 11 familles déjà enregistrées — aucune police ajoutée,
  L4/V2-E reste à venir). 3 DA sombres + 7 claires, teintes primaires réparties pour
  maximiser l'axe `palette_hue_bucket` de l'empreinte.
- **WCAG AA garanti par test** (`DaPalettesTest`) : chaque DA est vérifiée sur 6 paires
  — `ink` sur `base`/`surface`/`surface-alt` (couvre le rythme de fonds V2-B), `contrast`
  sur `base`, `primary-contrast`/`primary`, `accent-contrast`/`accent` — et sa cohérence
  palette ⇄ fichier de style ⇄ acceptation par le validateur. Les couleurs ont été
  conçues puis validées numériquement avant écriture.
- **Correction d'un défaut latent** : le défaut d'accent de `da-solaire-minimal`
  (`#F2620F`, 3,22:1 sur blanc) échouait AA ; ramené à `#C2410C` (5,18:1), déjà éprouvé
  par le site modèle restaurant-premium. Le test l'a révélé.
- **`DA_PALETTES` (validateur/GlobalStyles) reste la source de vérité PHP** : les 7
  palettes y sont ajoutées en plus du schéma, du validateur (`DEFAULT_REFS`) et du prompt
  générateur, pour que `apply-dna` applique correctement les nouvelles DA via les global
  styles utilisateur.

## V2-E — +6 paires typographiques (6 → 12)

- **Doublement par recombinaison des 11 familles OFL existantes** plutôt qu'ajout de
  nouvelles polices : fp-07…fp-12 sont des associations heading/body inédites (Playfair /
  Work Sans, Cormorant / Nunito Sans, Fraunces / IBM Plex Sans, Lora / Inter,
  Space Grotesk / Source Sans 3, Sora / Work Sans). Aucun WOFF2 à télécharger, licences
  SIL OFL déjà en place, budget performance intact (≤ 4 fichiers chargés par site, la
  paire active uniquement). Les 11 familles couvrent déjà une large variété ; ajouter de
  nouvelles familles resterait possible (plafond 16) mais n'apporte pas assez de diversité
  pour son coût de maintenance à ce stade.
- **Garde-fou `FontPairsTest`** : 12 paires, identifiants et combinaisons heading/body
  uniques, chaque famille enregistrée dans `theme.json` avec ses WOFF2 présents sur disque,
  budget de 4 fichiers respecté, et acceptation par le validateur. `font_pair` étendu au
  schéma, au validateur (`DEFAULT_REFS`) et au prompt générateur.

## V2-G1 — Socle motion premium (CSS natif)

- **Animations 100 % natives, zéro dépendance** (pas de GSAP/AOS/CDN/jQuery) :
  révélations au scroll via **CSS Scroll-driven Animations** (`animation-timeline:
  view()`), micro-interactions par transitions. Le seul JS (header/compteurs, View
  Transitions) est reporté à V2-G2.
- **CSS livré par le thème** (`assets/css/motion.css`, couche présentation statique
  et tokenisée), enqueue après `maji.css`. Le pilotage par l'ADN (V2-G2) écrira dans
  les global styles + classes, jamais dans les fichiers du thème.
- **Amélioration progressive stricte** : les états cachés (opacity 0) ne vivent que
  sous `@media (prefers-reduced-motion: no-preference)` **et**
  `@supports (animation-timeline: view())`. Sans support ou avec préférence de
  mouvement réduit, le contenu reste pleinement visible — jamais masqué.
- **Compositor-only** : les animations continues n'agissent que sur `transform` /
  `opacity` ; aucune propriété de layout transitionnée. Garanti par `MotionCssTest`.
- **Signature par DA** : tokens `custom.maji.motion` étendus (base + `reveal`,
  `distance`, `stagger`, `easing-emphasized`, `duration-fast`) et surchargés dans les
  10 DA (dérivés de leur `duration`) → le mouvement devient un axe de diversité (7ᵉ
  levier, voir `docs/MOTION.md`).

## V2-G2 — Moteur GSAP auto-hébergé (motion « expressive »)

- **GSAP + ScrollTrigger adoptés pour la couche `expressive`** (storytelling, scroll
  synchrone, séquences), là où le CSS natif atteint ses limites. Le socle CSS de G1
  reste la base sur tous les sites (niveaux none/subtle/standard).
- **Auto-hébergé, jamais en CDN** (`themes/maji-framework/assets/js/vendor/gsap/`,
  vendoré via npm, version figée 3.15.0) : respecte l'interdit « CDN tiers en front »,
  supprime un point de défaillance/latence sur les connexions instables d'Afrique de
  l'Ouest, évite toute fuite d'IP client (RGPD), et fige la version dans les zips de
  release (fleet update reproductible). **Aucun interdit du framework n'est modifié** —
  GSAP n'est pas jQuery et n'est pas servi depuis un CDN.
- **Chargement conditionnel + `defer`** : GSAP n'est enqueue qu'au niveau
  `design.motion = expressive`, en `defer`/`in_footer`, hors admin. Les autres niveaux
  = 0 Ko de JS d'animation. Évite les 3 pièges classiques (render-blocking, layout
  thrashing, chargement partout).
- **Discipline runtime** : toutes les tweens vivent dans `gsap.matchMedia(
  '(prefers-reduced-motion: no-preference)')` (WCAG 2.3.3) ; on n'anime que
  `transform`/`opacity` ; `ScrollTrigger.batch` pour les révélations (une instance).
  Garanti par `MotionEngineTest`.
- **Licence** : GSAP est gratuit depuis fin 2024 (Webflow, club plugins inclus) mais
  sous licence GreenSock « No Charge », **non GPL**. Acceptable ici car MAJI se
  distribue via **GitHub Releases** (pas le dépôt WordPress.org). Notice dans
  `assets/js/vendor/gsap/NOTICE.md`.
- **Frontière respectée** : la validation du champ `design.motion` est côté plugin
  (`DnaValidator`) ; l'application (body class + enqueue conditionnel) est côté thème
  (présentation), qui lit l'option `maji_dna` en lecture seule. Rien n'est écrit dans
  les fichiers du thème.

## V2-F — Contrôle visuel automatique (clôture V2)

- **Outil déterministe + auto-critique d'agent** plutôt qu'un simple item de prompt :
  `scripts/visual-check.mjs` (Playwright/Chromium fournis par le projet) capture
  l'accueil en mobile ET desktop (above-the-fold + pleine page), exécute des
  **contrôles durs objectifs** (défilement horizontal, erreurs console/JS, jeton
  `{{maji:*}}` résiduel, absence de H1, images cassées, contraste du texte < 4,5:1) et
  **sort en code ≠ 0** pour bloquer la livraison. La part subjective (hiérarchie,
  densité, équilibre…) est une grille /5 dans `docs/prompts/04-controle-visuel.md`,
  consommée par l'agent à partir des captures + du rapport JSON.
- **Boucle capture → critique → ajuste** intégrée au prompt orchestrateur (03, étape 6) :
  l'agent n'ajuste que l'ADN du client (jamais le thème), relance la provision et
  recapture jusqu'au rendu net ; il journalise le résultat dans le rapport humain.
- **Cohérence avec les leviers V2** : la grille §4 mappe chaque défaut visuel à un axe
  d'ADN (section_bg_rhythm, section_style, spacing_mood, hero, da, motion…) — corriger
  le rendu améliore aussi l'unicité anti-clones.
- **Complète, ne remplace pas** les garde-fous automatiques (WCAG du validateur, budget
  perf, jetons) : le contrôle visuel relève le *plancher* de qualité perçue.
