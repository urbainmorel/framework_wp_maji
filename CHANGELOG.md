# Changelog

Toutes les évolutions notables de ce projet sont documentées ici.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/) et le projet adhère au [versionnage sémantique](https://semver.org/lang/fr/).

## [Unreleased]

### Added

- **M1 — Socle** : monorepo (thème `maji-framework`, plugin `maji-core`), outillage Composer (PHPCS/WPCS, PHPStan 6, PHPUnit), npm (`@wordpress/scripts`, `wp-env`), CI GitHub Actions, documentation de base (README, AGENTS/CLAUDE, docs/).
- **M2 — Thème & tokens** : `theme.json` v3 complet (palette sémantique 10 slugs, typo fluide xs→3xl, `custom.maji` radius/shadow/motion, duotone), 3 directions artistiques (`da-editorial-sombre`, `da-solaire-minimal`, `da-artisanal-texture`), catalogue de 6 paires typographiques (WOFF2 auto-hébergés, OFL), 4 en-têtes + 3 pieds de page, gabarits de base + chambres + WooCommerce.
- **M3 — Cœur métier** : réglages `maji_settings` + pages admin Settings API (Établissement, Modes & fonctionnalités, Intégrations), capacité `manage_maji`, CPT `maji_room` + taxonomie `maji_amenity`, CPT `maji_reservation` (statuts new/confirmed/declined/cancelled, colonnes, filtres, actions rapides), blocs `maji/reservation-form` et `maji/table-booking-form`, REST `maji/v1` (création de demande avec honeypot + rate limit, réglages publics).
