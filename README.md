# MAJI WordPress Framework

Usine à sites WordPress pour hôtels et restaurants — marchés d'Afrique de l'Ouest francophone (Bénin, Côte d'Ivoire, Sénégal).

**Vision** : base technique commune + modèle sectoriel + direction artistique + ADN de site + contenus réels du client = site client unique, livré en 3 à 6 heures de travail humain.

## Composants

| Composant | Emplacement | Rôle |
|---|---|---|
| Thème `maji-framework` | `themes/maji-framework/` | Block theme (FSE) piloté par tokens `theme.json`, 3 directions artistiques, bibliothèque de sections |
| Plugin `maji-core` | `plugins/maji-core/` | Modes hôtel/restaurant, réservations, commandes, WhatsApp, webhooks, SEO, CLI `wp maji` |
| Système ADN | `dna/` | Schéma + exemples de fichiers `dna.json` par client |
| Registre anti-clones | `registry/registry.json` | Empreintes des sites livrés, contrôle de similarité bloquant |
| Contenus de démo | `demo-content/` | 4 sites modèles (2 hôtels, 2 restaurants) |
| Workflows n8n | `n8n/` | Notifications commande/réservation via WhatsApp |
| Scripts | `scripts/` | `install.sh` (provision complète), `release.sh` (zips de release) |

## Prérequis

- PHP ≥ 8.1, Composer 2
- Node.js 20+, npm
- Docker (pour `wp-env`)

## Démarrage rapide

```bash
composer install
npm install
npm run start          # démarre wp-env (WordPress fr_FR + thème + plugin + WooCommerce)
```

## Qualité

```bash
composer lint          # PHPCS (WordPress Coding Standards)
composer analyse       # PHPStan niveau 6
composer test          # PHPUnit (tests unitaires purs)
npm run build          # build des blocs (@wordpress/scripts)
npm run lint:js        # ESLint @wordpress
```

## Documentation

- [PRD](docs/PRD-MAJI-Framework.md) — périmètre produit
- [STI](docs/STI-MAJI-Framework.md) — spécifications techniques
- [Installation](docs/INSTALLATION.md) — installation d'un site client
- [Exploitation](docs/OPERATIONS.md) — provision, registre, mises à jour de flotte
- [Sections](docs/SECTIONS.md) — catalogue de la bibliothèque de sections
- [Décisions](docs/DECISIONS.md) — journal des décisions techniques

## Licence

GPL-2.0-or-later.
