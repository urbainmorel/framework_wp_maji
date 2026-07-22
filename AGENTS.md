# Instructions agent — MAJI WordPress Framework

Ce fichier s'adresse aux agents de codage IA (Codex, Claude Code). `CLAUDE.md` a le même contenu.

## Lire d'abord

1. `docs/PRD-MAJI-Framework.md` — le *quoi* et le *pourquoi* (périmètre).
2. `docs/STI-MAJI-Framework.md` — le *comment* (architecture, schémas, jalons M1–M6).

En cas de conflit : la STI prime pour les choix techniques, le PRD pour le périmètre.

## Règles

- **Ambiguïté ⇒ décision simple + trace** dans `docs/DECISIONS.md` (1–3 lignes). Ne jamais inventer de fonctionnalité.
- **Périmètre V2 interdit** : Mobile Money, calendrier de disponibilités, 10 DA, pipeline d'images génératif, multilingue, envoi WhatsApp depuis WordPress.
- Code, identifiants, commits en **anglais** (Conventional Commits) ; chaînes visibles en **français traduisible** (text domains `maji-framework` / `maji-core`).
- `declare(strict_types=1)` partout ; PSR-4 `MAJI\Core\` ⇒ `plugins/maji-core/src/`.

## Interdits absolus

- Page builders tiers ; CDN tiers en front ; jQuery en front.
- Valeurs de style codées en dur dans les patterns (tout passe par les tokens `theme.json`).
- Données client dans le code.
- Modification des fichiers du thème par les outils de personnalisation (l'ADN écrit dans les global styles utilisateur, jamais dans les fichiers).

## Commandes

```bash
composer install && npm install     # setup
npm run start                       # wp-env (WordPress fr_FR)
composer lint                       # PHPCS
composer analyse                    # PHPStan niveau 6
composer test                       # PHPUnit
npm run build                       # build blocs
npm run lint:js                     # ESLint
```

Avant de déclarer un jalon terminé : `composer lint && composer analyse && composer test && npm run build && npm run lint:js` doivent tous passer, et `CHANGELOG.md` (Keep a Changelog) + la documentation touchée doivent être à jour.

## Structure

Voir STI §2 (monorepo) : `themes/maji-framework/`, `plugins/maji-core/`, `dna/`, `registry/`, `demo-content/`, `n8n/`, `scripts/`, `docs/`.
