# Changelog

Toutes les évolutions notables de ce projet sont documentées ici.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/) et le projet adhère au [versionnage sémantique](https://semver.org/lang/fr/).

À partir de la 1.1.0, les entrées de version et les tags sont générés automatiquement par [release-please](https://github.com/googleapis/release-please) depuis les Conventional Commits (voir `.github/workflows/release-please.yml`). Les changements en attente apparaissent dans la « Release PR » du dépôt, pas dans une section « Unreleased ».

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
