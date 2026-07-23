# Changelog

Toutes les évolutions notables de ce projet sont documentées ici.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/) et le projet adhère au [versionnage sémantique](https://semver.org/lang/fr/).

À partir de la 1.1.0, les entrées de version et les tags sont générés automatiquement par [release-please](https://github.com/googleapis/release-please) depuis les Conventional Commits (voir `.github/workflows/release-please.yml`). Les changements en attente apparaissent dans la « Release PR » du dépôt, pas dans une section « Unreleased ».

## [1.8.0](https://github.com/urbainmorel/framework_wp_maji/compare/v1.7.0...v1.8.0) (2026-07-23)


### Ajouté

* V2-D4 — 8 new shared sections (32 to 40, V2-D complete) ([a609648](https://github.com/urbainmorel/framework_wp_maji/commit/a609648343cd75fb836a8edddd3ca10891127e4d))

## [1.7.0](https://github.com/urbainmorel/framework_wp_maji/compare/v1.6.0...v1.7.0) (2026-07-23)


### Ajouté

* V2-D3 — 8 new hotel sections (24 to 32) ([e284e9f](https://github.com/urbainmorel/framework_wp_maji/commit/e284e9f83aee37a69428553a0f7beade9c72186f))

## [1.6.0](https://github.com/urbainmorel/framework_wp_maji/compare/v1.5.0...v1.6.0) (2026-07-23)


### Ajouté

* V2-D2 — 8 new restaurant sections (16 to 24) ([709a92d](https://github.com/urbainmorel/framework_wp_maji/commit/709a92d25e617a531e0c37893a112d94869f5039))

## [1.5.0](https://github.com/urbainmorel/framework_wp_maji/compare/v1.4.0...v1.5.0) (2026-07-23)


### Ajouté

* V2-D1 — 8 new heros (6 to 10 per sector) ([18bc97c](https://github.com/urbainmorel/framework_wp_maji/commit/18bc97ce51258cd5a0ba9e0aef6ac5ef5af2db60))

## [1.4.0](https://github.com/urbainmorel/framework_wp_maji/compare/v1.3.0...v1.4.0) (2026-07-23)


### Ajouté

* V2-E — double the typographic pair catalogue (6 to 12) ([00d97c6](https://github.com/urbainmorel/framework_wp_maji/commit/00d97c6fb3204b36f04f7c0aa6da3181e8591758))

## [1.3.0](https://github.com/urbainmorel/framework_wp_maji/compare/v1.2.0...v1.3.0) (2026-07-23)


### Ajouté

* V2-C — add 7 curated art directions (3 to 10) ([9dc7847](https://github.com/urbainmorel/framework_wp_maji/commit/9dc7847e91e9cbf0714124ba3576db5c4bdd9fc5))

## [1.2.0](https://github.com/urbainmorel/framework_wp_maji/compare/v1.1.0...v1.2.0) (2026-07-22)


### Ajouté

* V2-B — apply section background rhythm and section style levers ([3d3c4fe](https://github.com/urbainmorel/framework_wp_maji/commit/3d3c4fed8007f8725d36282a62828ccdbf6ee3a7))

## [1.1.0] — 2026-07-22

### Added

- **V2-A — Empreinte anti-clones enrichie** (`docs/DIVERSITE.md` §4) : le registre passe de 6 à 11 axes pondérés (`da`, `hero`, `font_pair`, `palette_hue_bucket`, `header`, `footer`, `image_treatment_bucket`, `section_bg_rhythm`, `spacing_mood`, `composition_hash`, `radius_scale`). La trilogie `da + hero + font_pair` somme exactement au seuil (0.70) : deux sites ne peuvent plus partager à la fois direction artistique, hero et paire typographique. Nouveaux buckets `image_treatment_bucket` (overlay × duotone × ratio) et `composition_hash` (ordre des sections + `section_style`, remplace `section_order_hash`).
- **Champs d'ADN optionnels `design.section_bg_rhythm` (uni/alterne/bandes/contraste) et `design.section_style` (auto/net/ombre/minimal)** : ajoutés au schéma `dna.schema.json` et au validateur ; alimentent l'empreinte enrichie. Rétrocompatibles (absents ⇒ comportement V1).

## [1.0.0]

### Added

- **M1 — Socle** : monorepo (thème `maji-framework`, plugin `maji-core`), outillage Composer (PHPCS/WPCS, PHPStan 6, PHPUnit), npm (`@wordpress/scripts`, `wp-env`), CI GitHub Actions, documentation de base (README, AGENTS/CLAUDE, docs/).
- **M2 — Thème & tokens** : `theme.json` v3 complet (palette sémantique 10 slugs, typo fluide xs→3xl, `custom.maji` radius/shadow/motion, duotone), 3 directions artistiques (`da-editorial-sombre`, `da-solaire-minimal`, `da-artisanal-texture`), catalogue de 6 paires typographiques (WOFF2 auto-hébergés, OFL), 4 en-têtes + 3 pieds de page, gabarits de base + chambres + WooCommerce.
- **M3 — Cœur métier** : réglages `maji_settings` + pages admin Settings API (Établissement, Modes & fonctionnalités, Intégrations), capacité `manage_maji`, CPT `maji_room` + taxonomie `maji_amenity`, CPT `maji_reservation` (statuts new/confirmed/declined/cancelled, colonnes, filtres, actions rapides), blocs `maji/reservation-form` et `maji/table-booking-form`, REST `maji/v1` (création de demande avec honeypot + rate limit, réglages publics).
- **M4 — Bibliothèque de sections** : 12 heros (6 hôtel + 6 restaurant) et 16 sections (Annexe C) en variantes de composition avec jetons `{{maji:*}}` et marqueurs média `data-maji-media`, 7 blocs d'affichage (`rooms-grid`, `menu-grid`, `menu-list`, `menu-categories`, `whatsapp-button`, `opening-hours`, `establishment-info`), moteur de jetons, gabarits WhatsApp, catalogue `docs/SECTIONS.md`.
- **M6 — Usine** : schéma formel `dna/schemas/dna.schema.json` (maji-dna/1), validateur (références, contrastes WCAG AA, E.164, ISO 4217), registre anti-clones avec score pondéré et blocage ≥ 0,70, commandes `wp maji` (dna validate/check/register, apply-dna, import-content, export-model, provision idempotente), application ADN via global styles utilisateur (jamais les fichiers du thème), vocabulaires de navigation (2 par secteur), mise à jour de flotte (plugin-update-checker v5, thème + plugin, canal stable/beta), `scripts/install.sh` + `release.sh` + workflow `release.yml`, 2 ADN d'exemple + 4 sites modèles complets (`demo-content/`), `INSTALLATION.md` + `OPERATIONS.md`.
- **M5 — Transactions & intégrations** : restaurant complet (méta plats `maji_available`/`maji_badges`, blocage hors horaires, livraison/retrait + zones et frais au checkout, consentement WhatsApp, COD), webhooks signés HMAC-SHA256 (`X-MAJI-*`, timeout 5 s, relances +1/+10/+60 min via Action Scheduler ou WP-Cron, table journal + page admin avec renvoi), SEO de base (title/meta/OG/JSON-LD avec détection Yoast/Rank Math/SEOPress), 3 workflows n8n importables.
