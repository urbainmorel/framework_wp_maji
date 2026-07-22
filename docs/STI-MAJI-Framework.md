# STI — Spécifications Techniques d'Implémentation
## MAJI Framework (thème) · MAJI Core (plugin) · Outillage d'usine

| Champ | Valeur |
|---|---|
| Version | 1.0 — 21 juillet 2026 |
| Document parent | `PRD-MAJI-Framework.md` (périmètre, exigences F-*/N-*, DoD produit) |
| Destinataire | Agent de codage IA (OpenAI Codex ou Claude Code) |

---

## 0. Mode d'emploi pour l'agent — À LIRE EN PREMIER

1. **Lire le PRD puis cette STI en entier avant de coder.** Le PRD définit le périmètre ; la STI définit l'implémentation. Ne rien construire qui soit marqué V2 ou hors périmètre.
2. **Travailler jalon par jalon** (§13), dans l'ordre, une branche/PR par jalon. Un jalon n'est terminé que si sa *Definition of Done* est vérifiée.
3. **Ambiguïté ⇒ décision simple + trace.** Si un point n'est pas spécifié, choisir l'option la plus simple compatible avec le PRD et consigner le choix (1–3 lignes) dans `docs/DECISIONS.md`. Ne jamais inventer de fonctionnalité.
4. **Conventions** : code, identifiants, noms de fichiers et messages de commit en **anglais** (Conventional Commits : `feat:`, `fix:`, `chore:`…) ; toutes les chaînes visibles par l'utilisateur en **français traduisible** ; commentaires en français acceptés.
5. **Fichiers d'instructions agent** : maintenir à la racine `AGENTS.md` (lu par Codex) et `CLAUDE.md` (lu par Claude Code — réf. : https://docs.claude.com/en/docs/claude-code/overview), avec le même contenu : résumé des règles ci-dessus, commandes de build/test, périmètre interdit (V2), lien vers PRD/STI.
6. **Avant de déclarer un jalon terminé**, exécuter et faire passer :
   ```bash
   composer lint && composer analyse && composer test && npm run build && npm run lint:js
   ```
7. **Tenir à jour** `CHANGELOG.md` (format Keep a Changelog) et la documentation touchée par le jalon.
8. **Interdits absolus** : page builders tiers ; CDN tiers en front ; jQuery en front ; valeurs de style codées en dur dans les patterns ; données client dans le code ; modification des fichiers du thème par les outils de personnalisation (cf. §5.2).

---

## 1. Stack et versions

| Composant | Version / choix |
|---|---|
| WordPress | ≥ 6.6 (block theme / FSE, `theme.json` v3, styles de section) |
| PHP | ≥ 8.1 (développement en 8.2) — `declare(strict_types=1)` partout |
| Base de données | MySQL 5.7+ / MariaDB 10.4+ |
| WooCommerce | Dernière stable (≥ 9.x) — requis en mode restaurant uniquement |
| Node.js | 20 LTS + `@wordpress/scripts` (build des blocs et assets) |
| PHP outillage | Composer 2 ; `phpcs` + WordPress Coding Standards ; PHPStan (niveau 6, extension WordPress) ; PHPUnit |
| Environnement de dev | `@wordpress/env` (`wp-env`) — config fournie dans le repo |
| CLI | WP-CLI 2.x (commandes custom `wp maji …`) |
| Tâches asynchrones | Action Scheduler (embarqué avec WooCommerce ; l'inclure comme bibliothèque si Woo absent) |
| Mises à jour | `yahnis-elsts/plugin-update-checker` v5 + GitHub Releases |
| E2E | Playwright (via `@wordpress/scripts` / wp-env) |

## 2. Structure du monorepo

```
maji-wordpress-framework/
├── README.md
├── AGENTS.md                  # instructions agent (Codex)
├── CLAUDE.md                  # instructions agent (Claude Code) — même contenu
├── CHANGELOG.md
├── docs/
│   ├── PRD-MAJI-Framework.md
│   ├── STI-MAJI-Framework.md
│   ├── DECISIONS.md           # journal des décisions de l'agent
│   ├── INSTALLATION.md
│   ├── OPERATIONS.md          # exploitation : provision, mises à jour, registre
│   └── SECTIONS.md            # catalogue des sections avec captures
├── themes/
│   └── maji-framework/
├── plugins/
│   └── maji-core/
├── dna/
│   ├── schemas/dna.schema.json    # JSON Schema du fichier ADN (§5.1)
│   ├── examples/hotel-atlantique.json
│   └── examples/saveurs-du-benin.json
├── registry/
│   └── registry.json          # empreintes des sites livrés (§5.3)
├── demo-content/
│   ├── hotel-business/        # dna.json + content.json + media/
│   ├── hotel-boutique/
│   ├── restaurant-premium/
│   └── restaurant-africain/
├── n8n/
│   ├── restaurant-order.json
│   ├── hotel-reservation.json
│   └── status-update.json
├── scripts/
│   ├── install.sh             # installation WP complète + provision
│   └── release.sh             # build des zips de release
├── .wp-env.json
└── .github/workflows/         # ci.yml, release.yml
```

## 3. Thème `maji-framework`

### 3.1 Arborescence

```
themes/maji-framework/
├── style.css                # en-tête thème, Text Domain: maji-framework
├── theme.json               # v3 — source unique des tokens
├── functions.php            # enregistrement patterns, polices, supports
├── templates/               # index, front-page, page, single, archive,
│                            # single-maji_room, archive-maji_room, gabarits Woo
├── parts/                   # header-01..04.html, footer-01..03.html
├── patterns/                # *.php (§3.6)
├── styles/                  # da-*.json — directions artistiques (§3.3)
├── assets/
│   ├── fonts/               # WOFF2 auto-hébergés + fonts.json (catalogue de paires)
│   └── img/                 # décors neutres éventuels (motifs SVG)
├── src/                     # JS/CSS additionnels (build @wordpress/scripts)
└── inc/                     # helpers PHP du thème (aucune logique métier)
```

### 3.2 `theme.json` — architecture des tokens

Règles :
- **Palette sémantique uniquement** — slugs imposés : `base`, `contrast`, `primary`, `primary-contrast`, `accent`, `accent-contrast`, `surface`, `surface-alt`, `ink`, `ink-muted`. Les patterns référencent exclusivement ces presets.
- **Typographie fluide** : `settings.typography.fluid: true` ; tailles nommées `xs → 3xl` ; deux familles par site (`heading`, `body`) résolues depuis le catalogue de paires (§3.4).
- **Espacements** : `spacingScale` généré ; l'« humeur » d'espacement de l'ADN (compact / normal / aéré) module le pas de l'échelle via les global styles (§5.2), pas en éditant ce fichier.
- **Tokens custom** sous `settings.custom.maji` → variables CSS `--wp--custom--maji--…` :
  ```json
  {
    "settings": {
      "custom": {
        "maji": {
          "radius": { "sm": "4px", "md": "12px", "lg": "20px", "pill": "999px" },
          "shadow": { "card": "0 8px 24px rgba(0,0,0,.08)", "float": "0 16px 48px rgba(0,0,0,.14)" },
          "motion": { "duration": "200ms", "easing": "cubic-bezier(.2,.8,.2,1)" }
        }
      }
    }
  }
  ```
- **Duotone/overlay** : presets duotone déclarés par DA (utilisés par les blocs image/bannière selon le `image_treatment` de l'ADN).
- Layout : `contentSize` ≈ 720px, `wideSize` ≈ 1200px (surchargés par DA si pertinent).

### 3.3 Directions artistiques — `styles/da-*.json`

Chaque DA est une *style variation* complète et cohérente : palette par défaut, paire typo par défaut, rayons, ombres, presets duotone, styles par bloc et par section (styles de section WP 6.6+). **V1 : 3 DA.**

| Slug | Intention (à respecter dans les choix de styles) | Cible type |
|---|---|---|
| `da-editorial-sombre` | Fonds encre profonde, serif éditoriale expressive + sans humaniste, photos plein cadre sombres, accents dorés discrets, coins 4–8 px, ombres douces, rythme vertical ample | Hôtel premium, restaurant gastronomique |
| `da-solaire-minimal` | Fonds clairs, sans-serif géométrique, larges respirations, accent saturé unique, coins 16–24 px, photos lumineuses, cartes nettes sans ombre lourde | Restaurant moderne, hôtel business |
| `da-artisanal-texture` | Tons terre, grain/texture papier subtils, serif chaleureuse, bordures marquées, coins 0–4 px, motifs géométriques discrets inspirés des tissus locaux en décor SVG | Restaurant africain premium, hôtel boutique |

Critère de recette : activer une DA différente sur un même contenu doit produire un rendu **immédiatement distinguable** (composition des styles, pas seulement couleurs).

### 3.4 Polices — catalogue de paires

`assets/fonts/fonts.json` : tableau d'objets `{ "id": "fp-01", "heading": {...}, "body": {...} }` avec familles, graisses, fichiers WOFF2. **6 paires V1** (licences SIL OFL) :

`fp-01` Fraunces / Inter · `fp-02` Playfair Display / Source Sans 3 · `fp-03` Sora / Inter · `fp-04` Cormorant Garamond / Work Sans · `fp-05` Lora / Nunito Sans · `fp-06` Space Grotesk / IBM Plex Sans.

Déclaration via `settings.typography.fontFamilies[].fontFace` ; ≤ 4 fichiers chargés par site ; `font-display: swap` ; préchargement de la police de titres.

### 3.5 Headers / footers

`parts/header-01` transparent sur hero · `header-02` solide · `header-03` logo centré · `header-04` split (nav gauche / actions droite). `footer-01` complet (coordonnées + horaires + nav) · `footer-02` minimal · `footer-03` éditorial. Tous consomment les données Établissement via les blocs dynamiques `maji/*` (§4.4) — jamais de texte en dur.

### 3.6 Patterns — bibliothèque de sections

- Fichiers PHP dans `patterns/`, en-têtes standard ; **slug** : `maji/{secteur}-{section}-{nn}` (`hotel`, `resto`, `commun`) ; **catégories** : `maji-hotel`, `maji-restaurant`, `maji-commun`.
- Contenus de démonstration = **jetons** `{{maji:chemin}}` (ex. `{{maji:identity.name}}`, `{{maji:content.facts.quartier}}`), remplacés à l'import (§6). L'import échoue si un jeton subsiste — un jeton ne doit **jamais** apparaître en front.
- Les variantes d'une même section diffèrent par la **composition/DOM** (alignements, superpositions, largeurs, ordre interne), pas seulement par des classes.
- Inventaire V1 imposé : **6 heros hôtel + 6 heros restaurant + 15 sections** — liste exacte en Annexe C.

### 3.7 Assets & interactivité

Build via `@wordpress/scripts`. JS front minimal (menu mobile, galerie/lightbox) implémenté avec l'**Interactivity API** de WordPress ou en vanilla ; budget §10. Pas de framework CSS externe ; le CSS additionnel consomme les variables des tokens.

## 4. Plugin `maji-core`

### 4.1 Arborescence (PSR-4)

```
plugins/maji-core/
├── maji-core.php            # bootstrap ; Text Domain: maji-core
├── composer.json            # autoload PSR-4 "MAJI\\Core\\" => "src/"
├── src/
│   ├── Plugin.php           # container léger, enregistrement des modules
│   ├── Settings/            # pages d'admin + schéma d'options
│   ├── Modes/               # activation hôtel / restaurant
│   ├── Hotel/               # CPT chambres, réservations, admin
│   ├── Restaurant/          # intégration Woo : champs, zones, horaires, checkout
│   ├── Webhooks/            # Dispatcher, Signer, Log, retries
│   ├── WhatsApp/            # génération d'URL, gabarits de messages
│   ├── Seo/                 # meta + JSON-LD (désactivable, détection plugins SEO)
│   ├── Rest/                # endpoints maji/v1
│   ├── Blocks/              # enregistrement des blocs dynamiques
│   └── Cli/                 # commandes wp maji (§6)
├── blocks/                  # sources des blocs (block.json + edit/save/render)
├── languages/
└── tests/
```

### 4.2 Modèle de données

**CPT `maji_room`** (public) — supports : title, editor, thumbnail. Meta (toutes via `register_post_meta`, `show_in_rest` + schéma, sanitisation stricte) : `price_from` (int, FCFA), `capacity` (int), `size_sqm` (int), `gallery` (int[] d'IDs médias), `featured` (bool). Taxonomie `maji_amenity` (équipements).

**CPT `maji_reservation`** (non public, `show_ui`) — meta : `type` (`room`|`table`), `room_id`, `checkin`/`checkout` (`Y-m-d`), `date_time` (tables), `guests` (int), `name`, `phone` (E.164), `email`, `message`, `whatsapp_consent` (bool), `status` (`new`|`confirmed`|`declined`|`cancelled`). Admin : colonnes (client, dates, chambre, statut), filtres par statut, actions rapides de changement de statut (chaque changement ⇒ webhook).

**Restaurant** — plats = produits WooCommerce. Meta produit : `maji_available` (bool, masque à la vente sans dépublier), `maji_badges` (subset de `populaire|epice|nouveau`). Catégories de menu = `product_cat`. **Zones de livraison** : option `maji_settings.delivery_zones` = `[{ "name": "Haie Vive", "fee": 500 }, …]` ; au checkout, choix livraison/retrait + zone ⇒ frais Woo ajoutés (`woocommerce_cart_calculate_fees`). **Horaires** : `hours` par jour (`[["11:00","15:00"],["18:30","22:30"]]`) ; hors plage ⇒ blocage à l'ajout panier/checkout avec notice (F-R4). Paiement V1 : COD uniquement (config par défaut à la provision).

**Option `maji_settings`** (autoload oui, sauf secrets) — schéma :
```json
{
  "establishment": {
    "name": "", "type": "hotel|restaurant|mixte",
    "phone": "+229…", "whatsapp": "+229…", "email": "",
    "address": { "street": "", "district": "", "city": "", "country": "BJ" },
    "hours": { "mon": [["08:00","22:00"]], "…": [] },
    "currency": "XOF", "locale": "fr_FR",
    "socials": { "facebook": "", "instagram": "", "tiktok": "" }
  },
  "features": { "ordering": true, "table_booking": false, "delivery": true, "pickup": true },
  "delivery_zones": [ { "name": "", "fee": 0 } ],
  "integrations": {
    "n8n_order_url": "", "n8n_reservation_url": "",
    "webhook_secret": ""   // stocké dans une option séparée non-autoload, jamais affiché en clair
  }
}
```

### 4.3 Admin

Menu de premier niveau « MAJI » ; sous-pages : Établissement, Modes & fonctionnalités, Intégrations (n8n/WhatsApp), Journal des webhooks. Implémentation **Settings API classique** (pas de SPA) : rapide à générer, robuste, accessible. Capacité custom `manage_maji` (accordée aux administrateurs) requise partout. Tous les libellés en français.

### 4.4 Blocs dynamiques (V1)

`maji/reservation-form`, `maji/table-booking-form`, `maji/rooms-grid` (attributs : nombre, colonnes, filtre équipement), `maji/menu-grid` / `maji/menu-list` / `maji/menu-categories` (source Woo, badges, disponibilité), `maji/whatsapp-button` (contexte, message pré-rempli), `maji/opening-hours`, `maji/establishment-info` (variantes : coordonnées / adresse / réseaux). Tous : `block.json` + rendu PHP, styles via tokens uniquement, aucun texte en dur.

### 4.5 Webhooks sortants (F-C4)

- Événements : `reservation.created`, `reservation.status_changed`, `order.created` (hook `woocommerce_checkout_order_processed`), `order.status_changed` (`woocommerce_order_status_changed`).
- Requête : `POST` JSON UTF-8 vers l'URL n8n configurée ; en-têtes `X-MAJI-Event`, `X-MAJI-Site` (slug), `X-MAJI-Delivery` (UUID), `X-MAJI-Signature: sha256=` HMAC-SHA256 du corps avec `webhook_secret`. Timeout 5 s.
- Échec (timeout, HTTP ≥ 400) ⇒ relances via **Action Scheduler** : +1 min, +10 min, +60 min (3 tentatives max).
- **Journal** : table custom `{prefix}maji_webhook_log` (`id, event, url, payload_hash, status_code, attempts, last_error, created_at, updated_at`) + page admin listant les 200 derniers envois avec bouton « Renvoyer ». Jamais de secret ni de payload complet en clair dans le log (hash + extrait).
- Payloads **exacts** : Annexe A. Toute évolution de payload = version dans le champ `schema` du corps.

### 4.6 WhatsApp (F-C3, F-C5)

Click-to-chat : `https://wa.me/{numéro}?text={message urlencodé}`. Gabarits de messages par contexte (contact, plat, chambre, suivi de commande) avec variables `{{…}}` résolues côté PHP. Consentement : case obligatoire sur formulaires et checkout (« J'accepte d'être contacté·e sur WhatsApp pour cette demande »), valeur stockée (meta commande/réservation) et transmise dans les webhooks. **Aucun envoi de message depuis WordPress** — c'est le rôle des workflows n8n.

### 4.7 SEO (F-C6)

Si aucun plugin SEO majeur détecté (Yoast, Rank Math, SEOPress) : `<title>`/meta description par gabarits, Open Graph/Twitter, JSON-LD `Hotel`/`Restaurant`/`LocalBusiness` construit depuis `maji_settings` (nom, adresse, téléphone, horaires, géo si fournie). Sinon : tout se désactive proprement.

### 4.8 REST (`maji/v1`)

- `POST /reservations` (public) : crée une `maji_reservation`. Protections : nonce, champ honeypot, limitation par IP (transient, ex. 5/heure), validation stricte (dates cohérentes, téléphone E.164). Réponse 201 + message français.
- `GET /public-settings` (public) : sous-ensemble non sensible de `maji_settings` pour les blocs (jamais `integrations`).

## 5. Système « ADN de site »

### 5.1 Schéma de `dna.json`

Un JSON Schema formel est à produire dans `dna/schemas/dna.schema.json` (jalon M6), conforme à la spécification annotée suivante :

```jsonc
{
  "schema": "maji-dna/1",
  "meta":     { "site_slug": "hotel-atlantique", "client": "Hôtel Atlantique", "created_at": "2026-07-21" },
  "identity": { /* même forme que maji_settings.establishment + features + delivery_zones */ },
  "integrations": { "n8n_order_url": "", "n8n_reservation_url": "" },  // le secret est généré à la provision, jamais dans l'ADN
  "design": {
    "da": "da-editorial-sombre",            // slug d'une style variation existante
    "font_pair": "fp-01",                   // id du catalogue §3.4
    "palette": {                             // hex ; complétée par la DA pour les slugs absents
      "primary": "#0D1B2A", "primary-contrast": "#FFFFFF",
      "accent": "#C9A227",  "accent-contrast": "#141414",
      "surface": "#0B0F14", "ink": "#F3EFE6"
    },
    "radius_scale": "sm|md|lg",
    "spacing_mood": "compact|normal|aere",
    "image_treatment": { "hero_ratio": "16:9", "card_ratio": "4:3", "overlay": "none|primary|dark", "duotone": false }
  },
  "structure": {
    "header": "header-01", "footer": "footer-01",
    "nav_vocabulary": "hotel-experientiel",  // jeu de libellés (≥ 2 par secteur en V1)
    "pages": [
      { "slug": "accueil",  "title": "Accueil",
        "sections": ["maji/hotel-hero-03","maji/hotel-chambres-01","maji/commun-avis-02","maji/commun-cta-01"] },
      { "slug": "chambres", "title": "Séjourner", "sections": ["maji/hotel-chambres-02","maji/hotel-equipements-01"] }
    ]
  },
  "content": {
    "tone": "premium|familial|local|minimal",
    "facts": { "quartier": "Haie Vive", "annee": 2016, "specialites": ["…"] },
    "texts": { /* surcharges optionnelles : clé de jeton -> texte final */ },
    "seed":  "demo-content/hotel-business"   // dossier contenus/médias à importer
  }
}
```

### 5.2 `wp maji apply-dna` — application sans toucher aux fichiers

Ordre d'application (idempotent) :
1. **Validation** (§5.4) puis **contrôle registre** (§5.3) — arrêt en erreur si échec (sauf `--skip-registry`, réservé aux environnements de dev).
2. `identity` + `features` + `delivery_zones` + `integrations` → `maji_settings` ; génération du `webhook_secret` s'il n'existe pas.
3. `design` → **global styles utilisateur** : construire un `theme.json` partiel (palette, familles typo de la paire, `custom.maji.radius`, pas de l'échelle d'espacement selon `spacing_mood`, presets duotone selon `image_treatment`) et l'écrire dans le post `wp_global_styles` du thème actif (`WP_Theme_JSON_Resolver::get_user_global_styles_post_id()`, contenu `{"version":3,"isGlobalStylesUserThemeJSON":true,…}`), après avoir activé la style variation `da`. **Interdiction absolue d'écrire dans les fichiers du thème** (sinon les mises à jour de flotte écrasent la personnalisation).
4. `structure` → sélection des template parts header/footer ; création/mise à jour des pages ; insertion des patterns listés avec **remplacement des jetons** `{{maji:*}}` depuis `identity`/`content` ; application du jeu `nav_vocabulary` au menu.
5. Vérification finale : aucun jeton résiduel dans le contenu publié (sinon erreur listant les occurrences).

### 5.3 Registre anti-clones — `registry/registry.json`

Une entrée par site livré :
```json
{ "site_slug": "…", "da": "…", "font_pair": "…", "hero": "maji/hotel-hero-03",
  "header": "header-01", "palette_hue_bucket": 7, "section_order_hash": "sha1…", "delivered_at": "…" }
```
- `palette_hue_bucket` = teinte HSL de `primary` quantifiée par tranches de 30° (0–11).
- `section_order_hash` = SHA-1 de la concaténation ordonnée des slugs de sections de l'accueil.
- **Score de similarité** entre deux sites = somme pondérée des égalités : `da` 0,30 · `font_pair` 0,20 · `hero` 0,20 · `palette_hue_bucket` 0,15 · `header` 0,10 · `section_order_hash` 0,05.
- `wp maji dna check <dna> --registry=<path>` : calcule le score contre chaque entrée ; **si max ≥ 0,70 ⇒ code retour ≠ 0**, avec le détail (site le plus proche, axes en collision) et des suggestions concrètes (« changez la DA ou la paire typographique et le hero »). `--force` existe mais est journalisé dans la sortie.
- `wp maji dna register <dna>` : ajoute l'empreinte après livraison (à intégrer au processus d'exploitation, `docs/OPERATIONS.md`).

### 5.4 Validation d'ADN — `wp maji dna validate`

1. Conformité au JSON Schema (`dna/schemas/dna.schema.json`).
2. Références existantes : DA, paire typo, header/footer, slugs de patterns, jeu de vocabulaire.
3. **Contraste WCAG AA** (algorithme WCAG 2.x) sur les paires : `ink`/`surface`, `primary-contrast`/`primary`, `accent-contrast`/`accent` — échec = erreur listant les ratios mesurés.
4. Téléphones en E.164, devise ISO 4217, dates valides.

## 6. CLI et scripts

Commandes (namespace `wp maji`, classe(s) dans `src/Cli/`, sorties claires, code retour ≠ 0 en échec) :

| Commande | Rôle |
|---|---|
| `wp maji dna validate <file>` | §5.4 |
| `wp maji dna check <file> --registry=<path>` | §5.3 |
| `wp maji dna register <file> --registry=<path>` | Ajout de l'empreinte |
| `wp maji apply-dna <file>` | §5.2 |
| `wp maji import-content <file\|dir>` | Importe `content.json` + médias du dossier seed : chambres/plats (avec images, `alt` requis), textes, avis ; remplace les jetons |
| `wp maji export-model <slug> --dest=<dir>` | Exporte pages + réglages + contenus en dossier `demo-content/` réutilisable |
| `wp maji provision --dna=<file> [--model=<dir>] [--with-woo]` | Orchestrateur idempotent : validate → check → (installe/active Woo si demandé) → apply-dna → import-content → contrôle final. Relançable sans effet de bord |

`scripts/install.sh` : à partir d'une base vide — `wp core download/config/install` (locale `fr_FR`), installation thème + plugin depuis les zips buildés, puis `wp maji provision …`. `scripts/release.sh` : build des zips de release (utilisé par la CI).

## 7. Mises à jour de flotte

- Intégrer `yahnis-elsts/plugin-update-checker` v5 **dans le thème et dans le plugin**, pointés sur les **GitHub Releases** du dépôt (assets `maji-framework.zip` et `maji-core.zip` construits par la CI au tag `vX.Y.Z`).
- Versionnage **SemVer** ; constante `MAJI_UPDATE_CHANNEL` (`stable`|`beta`) filtrant les pré-releases ; procédure (dont jeton d'accès si dépôt privé et sa rotation) documentée dans `docs/OPERATIONS.md`.
- Contrat de compatibilité : une mise à jour ne doit jamais écraser la personnalisation (garanti par §5.2) ni casser les patterns déjà insérés (les patterns insérés sont figés dans le contenu — toute variante retirée du thème doit rester rendue correctement).

## 8. Standards et qualité

- **PHP** : WPCS (ruleset du repo), PHPStan niveau 6 min ; échappement systématique en sortie (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`), sanitisation en entrée ; nonces + `current_user_can('manage_maji')` sur toute écriture admin ; SQL uniquement via `$wpdb->prepare` (table de log) ; pas d'`extract()`, pas de suppression d'erreurs.
- **i18n** : text domains `maji-framework` / `maji-core` ; aucune chaîne en dur ; fichiers `.pot` générés au build.
- **JS** : ESLint config `@wordpress` ; toute dépendance front > 10 KB gz doit être justifiée dans `docs/DECISIONS.md`.
- **Accessibilité** : labels de formulaires, focus visibles, contrastes garantis par §5.4, `alt` obligatoires à l'import.

## 9. Tests

- `wp-env` : WP 6.6 + dernière stable, avec WooCommerce.
- **PHPUnit (unitaires purs, prioritaires)** : validateur d'ADN (schéma + contrastes), calcul de similarité du registre (cas limites : score exactement 0,70), constructeurs de payloads webhook (Annexe A comme fixtures), résolution des jetons `{{maji:*}}`, générateur de fragment global styles.
- **Intégration** : création d'une réservation ⇒ webhook planifié ; commande Woo ⇒ payload conforme ; échec HTTP simulé ⇒ relances + journal.
- **Playwright (2 parcours e2e)** : (a) provision de la fixture hôtel → soumission d'une demande de réservation → un mock n8n reçoit le payload attendu ; (b) fixture restaurant → commande COD avec zone de livraison → payload commande conforme ; + vérification du blocage hors horaires.
- La CI exécute l'ensemble sur chaque PR.

## 10. Budget de performance (bloquant, mesuré sur l'accueil des modèles, mobile)

| Métrique | Budget |
|---|---|
| Poids transféré hors images | ≤ 300 KB |
| CSS + JS (gzip) | ≤ 90 KB |
| LCP (profil mobile milieu de gamme, Fast 3G) | ≤ 2,5 s |
| CLS / TBT | < 0,1 / < 200 ms |
| Polices | ≤ 4 WOFF2, `font-display: swap`, preload titres |
| Images | `srcset` + lazy (sauf image LCP), WebP généré par WordPress |
| Requêtes tierces bloquantes | 0 |

Vérification : Lighthouse CI dans le pipeline si praticable avec `wp-env` ; à défaut, script `npm run audit` documenté et exécuté à chaque jalon M4–M6 (résultats consignés dans la PR).

## 11. Sécurité

Secrets (`webhook_secret`, jetons) : options non-autoload, jamais affichés en clair ni journalisés. Endpoints publics : honeypot + rate limiting + validation stricte (§4.8). Webhooks signés HMAC (§4.5). Uploads d'import restreints aux types image. Aucune donnée personnelle dans les logs (hash/extraits). En-têtes de sécurité de base sur les réponses du plugin.

## 12. CI/CD (GitHub Actions)

- `ci.yml` (sur PR) : `composer lint` + PHPStan + PHPUnit + `npm run lint:js` + `npm run build` + e2e Playwright (matrice WP 6.6 / dernière).
- `release.yml` (sur tag `v*`) : build des zips thème/plugin (`scripts/release.sh`), création de la GitHub Release avec les assets — consommés par le mécanisme §7.

## 13. Jalons de livraison (ordre imposé) et Definition of Done

**M1 — Socle.** Monorepo §2, `composer`/`npm` configurés, `wp-env` fonctionnel, CI verte (lint sur squelettes), `AGENTS.md`/`CLAUDE.md`, thème et plugin activables (vides mais propres).
*DoD : `wp-env start` donne un WP fr_FR avec thème+plugin actifs ; CI passe.*

**M2 — Thème & tokens.** `theme.json` complet (§3.2), 3 DA (§3.3), catalogue de 6 paires de polices (§3.4), 4 headers + 3 footers, gabarits de base + Woo.
*DoD : basculer de DA sur un contenu de démo transforme visiblement le rendu ; aucun style en dur ; budget CSS respecté.*

**M3 — Cœur métier.** Réglages + capacité `manage_maji` (§4.3), modes, CPT `maji_room`/`maji_reservation` + admin, blocs formulaires (`reservation-form`, `table-booking-form`) avec REST §4.8.
*DoD : une demande soumise en front apparaît en admin avec statut « nouvelle » ; anti-spam actif ; tests unitaires du module verts.*

**M4 — Bibliothèque de sections.** 12 heros + 15 sections (Annexe C) avec jetons, blocs d'affichage (`rooms-grid`, `menu-*`, `whatsapp-button`, `opening-hours`, `establishment-info`), `docs/SECTIONS.md` avec captures.
*DoD : chaque pattern rend correctement dans les 3 DA ; aucun jeton visible après import de test ; budget de perf tenu.*

**M5 — Transactions & intégrations.** Restaurant complet (§4.2 : zones, horaires, COD), webhooks signés + journal + relances (§4.5), WhatsApp (§4.6), SEO (§4.7), workflows n8n importables + doc de branchement.
*DoD : critères webhooks du PRD §12 vérifiés contre un n8n de test ; e2e (a) et (b) verts.*

**M6 — Usine.** Schéma ADN formel, commandes §6 complètes, registre + règle de similarité (§5.3), `install.sh`, mise à jour de flotte branchée (§7), **4 sites modèles produits via `wp maji provision`**, `INSTALLATION.md`/`OPERATIONS.md`.
*DoD : Definition of Done produit du PRD §12 intégralement verte.*

---

## Annexe A — Payloads de webhooks (contrat exact)

### A.1 `order.created`
```json
{
  "schema": "maji-webhook/1",
  "event": "order.created",
  "site": "saveurs-du-benin",
  "sent_at": "2026-07-21T12:30:00+01:00",
  "order": {
    "id": 1042,
    "number": "1042",
    "status": "processing",
    "customer": { "name": "Ayo K.", "phone": "+22901400000", "whatsapp_consent": true },
    "fulfillment": { "mode": "delivery", "zone": "Haie Vive", "fee": 500, "address": "Rue …, Cotonou", "pickup_time": null },
    "items": [
      { "product_id": 88, "name": "Poulet braisé", "variation": "Entier", "qty": 2, "unit_price": 3500, "total": 7000 }
    ],
    "totals": { "subtotal": 7000, "delivery": 500, "total": 7500, "currency": "XOF" },
    "payment": { "method": "cod" },
    "note": "Sans piment"
  }
}
```

### A.2 `reservation.created`
```json
{
  "schema": "maji-webhook/1",
  "event": "reservation.created",
  "site": "hotel-atlantique",
  "sent_at": "2026-07-21T12:30:00+01:00",
  "reservation": {
    "id": 87,
    "type": "room",
    "room": { "id": 12, "name": "Suite Premium" },
    "checkin": "2026-08-02", "checkout": "2026-08-05", "guests": 2,
    "customer": { "name": "…", "phone": "+229…", "email": "…", "whatsapp_consent": true },
    "message": "…",
    "status": "new"
  }
}
```

### A.3 `*.status_changed`
Même enveloppe que l'événement d'origine, avec en plus :
```json
{ "previous_status": "new", "new_status": "confirmed", "changed_by": "admin" }
```

## Annexe B — Exemple complet d'ADN

`dna/examples/hotel-atlantique.json` : reprendre intégralement la forme du §5.1 avec des valeurs réalistes (Hôtel Atlantique, Cotonou, `da-editorial-sombre`, `fp-01`, palette bleu nuit/doré, `spacing_mood: aere`, accueil = `hotel-hero-03 → chambres-01 → equipements-02 → avis-02 → localisation-01 → cta-01`, vocabulaire `hotel-experientiel`, seed `demo-content/hotel-business`). Ce fichier sert de fixture aux tests et de référence de documentation.

## Annexe C — Inventaire des sections V1 (obligatoire)

**Heros hôtel (6)** : `hotel-hero-01` image plein écran + titre centré · `02` split texte gauche / image droite · `03` éditorial asymétrique, titre débordant · `04` formulaire de réservation visible · `05` mosaïque 3 images · `06` bandeau bas + carte flottante.

**Heros restaurant (6)** : `resto-hero-01` plein écran + bouton Commander · `02` split plat détouré · `03` typographique (menu du jour en vedette) · `04` carrousel de plats signature · `05` vidéo/ambiance (image animée légère) · `06` double CTA Commander / Réserver.

**Sections (15)** : `hotel-chambres-01` grille · `hotel-chambres-02` liste éditoriale alternée · `hotel-equipements-01` grille d'icônes · `hotel-equipements-02` bandes horizontales · `hotel-services-01` cartes · `resto-menu-01` grille photo · `resto-menu-02` liste typographique · `resto-menu-03` onglets par catégories · `resto-populaires-01` rangée mise en avant · `resto-livraison-01` zones + frais · `commun-avis-01` cartes · `commun-avis-02` citation pleine largeur · `commun-galerie-01` mosaïque · `commun-localisation-01` adresse + carte statique + horaires · `commun-faq-01` accordéon · `commun-cta-01` bandeau final WhatsApp/Réserver. *(16 listées : `commun-cta-01` compte comme la marge de sécurité.)*

Chaque section : jetons `{{maji:*}}`, rendu validé dans les 3 DA, mobile d'abord.

---

*Fin de la STI. Tout écart par rapport à ce document doit être consigné dans `docs/DECISIONS.md` et rester dans le périmètre du PRD.*
