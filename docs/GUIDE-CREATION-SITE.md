# Guide de création d'un site client

Ce guide s'adresse à l'intégrateur MAJI (P1). Il couvre la production complète d'un
site client, de la copie d'un ADN à la livraison, en 3 à 6 heures de travail humain.

> Résumé du flux : **ADN → validation → contrôle anti-clones → seed → provision →
> contrôle qualité → livraison → enregistrement au registre.**

---

## 1. Vue d'ensemble du système

Un site MAJI est entièrement décrit par deux artefacts :

| Artefact | Rôle | Emplacement type |
|---|---|---|
| `dna.json` | Identité, design, structure et textes du site | `clients/<slug>/dna.json` |
| Dossier seed | Contenus réels : `content.json` + `media/` | `clients/<slug>/` |

La commande `wp maji provision` transforme ces deux artefacts en site fini.
Elle est **idempotente** : la relancer met à jour le site sans effet de bord.

**Règle d'or** : on ne modifie jamais les fichiers du thème. Toute la
personnalisation passe par l'ADN (appliqué dans les global styles utilisateur et
les contenus en base). C'est ce qui permet les mises à jour de flotte sans casse.

---

## 2. Écrire l'ADN — référence champ par champ

Partez toujours d'un exemple : `dna/examples/hotel-atlantique.json` (hôtel) ou
`dna/examples/saveurs-du-benin.json` (restaurant). Schéma formel :
`dna/schemas/dna.schema.json`.

### 2.1 `schema` et `meta`

```jsonc
{
  "schema": "maji-dna/1",              // toujours cette valeur exacte
  "meta": {
    "site_slug": "hotel-atlantique",   // identifiant unique dans le registre (minuscules, chiffres, tirets)
    "client": "Hôtel Atlantique",      // nom lisible du client
    "created_at": "2026-07-21"         // AAAA-MM-JJ
  }
}
```

⚠️ `site_slug` identifie le site dans le registre anti-clones : il ne doit
**jamais changer** après livraison (une re-livraison avec le même slug remplace
l'empreinte au lieu de se bloquer elle-même).

### 2.2 `identity` — l'établissement

Même forme que les réglages « MAJI → Établissement » (aucune double saisie :
l'ADN remplit l'admin) :

```jsonc
"identity": {
  "name": "Hôtel Atlantique",
  "type": "hotel",                     // hotel | restaurant | mixte
  "phone": "+22921301020",             // E.164 obligatoire (+ indicatif pays)
  "whatsapp": "+22996010203",          // E.164 — alimente tous les boutons WhatsApp
  "email": "contact@hotel-atlantique.bj",
  "address": {
    "street": "Boulevard de la Marina",
    "district": "Haie Vive",           // quartier — très utilisé en Afrique de l'Ouest
    "city": "Cotonou",
    "country": "BJ"                    // ISO 3166-1 alpha-2
  },
  "hours": {                           // plages par jour ; [] = fermé ce jour
    "mon": [["07:00","22:00"]],
    "tue": [["11:00","15:00"],["18:30","22:30"]],  // coupure possible
    "sun": []
  },
  "currency": "XOF",
  "locale": "fr_FR",
  "socials": { "facebook": "", "instagram": "", "tiktok": "" },
  "features": {                        // modules activés
    "ordering": true,                  // commande en ligne (restaurant, Woo requis)
    "table_booking": true,             // réservation de table
    "delivery": true,
    "pickup": true
  },
  "delivery_zones": [                  // frais par quartier (restaurant)
    { "name": "Fidjrossè", "fee": 500 }
  ]
}
```

Les `hours` pilotent **à la fois** l'affichage (bloc horaires, JSON-LD) et le
blocage des commandes hors horaires (le menu reste consultable).

### 2.3 `integrations` — n8n

```jsonc
"integrations": {
  "n8n_order_url": "https://n8n…/webhook/maji-order",
  "n8n_reservation_url": "https://n8n…/webhook/maji-reservation"
}
```

Le `webhook_secret` n'est **jamais** dans l'ADN : il est généré à la provision
(récupérable via `wp option get maji_webhook_secret` pour le branchement n8n,
voir OPERATIONS.md).

### 2.4 `design` — l'apparence

C'est ici que se joue l'unicité visuelle. Quatre axes indépendants :

```jsonc
"design": {
  "da": "da-editorial-sombre",         // direction artistique (style variation)
  "font_pair": "fp-01",                // paire typographique
  "palette": {                         // surcharges hex — la DA complète le reste
    "primary": "#0D1B2A", "primary-contrast": "#FFFFFF",
    "accent": "#C9A227",  "accent-contrast": "#141414"
  },
  "radius_scale": "sm",                // sm | md | lg — arrondis globaux
  "spacing_mood": "aere",              // compact | normal | aere — respiration verticale
  "image_treatment": {
    "hero_ratio": "16:9",              // 16:9 | 4:3 | 21:9 | 3:2
    "card_ratio": "4:3",               // 4:3 | 1:1 | 3:4 | 16:9
    "overlay": "dark",                 // none | primary | dark
    "duotone": false                   // duotone auto primaire/fond sur les images
  }
}
```

**Directions artistiques disponibles** :

| Slug | Intention | Cible |
|---|---|---|
| `da-editorial-sombre` | Fonds encre, serif expressive, accents dorés, coins 2–8 px | Hôtel premium, gastro |
| `da-solaire-minimal` | Fonds clairs, sans-serif géométrique, accent vif, coins 16–24 px | Restaurant moderne, hôtel business |
| `da-artisanal-texture` | Tons terre, serif chaleureuse, bordures marquées, coins 0–4 px | Restaurant africain, hôtel boutique |

**Paires typographiques** (`themes/maji-framework/assets/fonts/fonts.json`) :

| ID | Titres | Corps | Caractère |
|---|---|---|---|
| `fp-01` | Fraunces | Inter | Éditorial contemporain |
| `fp-02` | Playfair Display | Source Sans 3 | Classique élégant |
| `fp-03` | Sora | Inter | Géométrique moderne |
| `fp-04` | Cormorant Garamond | Work Sans | Raffiné artisanal |
| `fp-05` | Lora | Nunito Sans | Chaleureux accessible |
| `fp-06` | Space Grotesk | IBM Plex Sans | Technique affirmé |

⚠️ **Contraste WCAG AA obligatoire** : le validateur mesure `ink/surface`,
`primary-contrast/primary` et `accent-contrast/accent` (minimum 4.5:1) et
**refuse** l'ADN sinon, en affichant les ratios mesurés. Testez vos couleurs sur
https://webaim.org/resources/contrastchecker/ avant de les inscrire.

### 2.5 `structure` — pages et compositions

```jsonc
"structure": {
  "header": "header-01",               // header-01 transparent | 02 solide | 03 centré | 04 split
  "footer": "footer-01",               // footer-01 complet | 02 minimal | 03 éditorial
  "nav_vocabulary": "hotel-experientiel",
  "pages": [
    {
      "slug": "accueil",               // « accueil » devient automatiquement la page d'accueil
      "title": "Accueil",
      "sections": [                    // ordre = ordre d'affichage ; le 1er = hero
        "maji/hotel-hero-03",
        "maji/hotel-chambres-01",
        "maji/commun-avis-02",
        "maji/commun-cta-01"
      ]
    }
  ]
}
```

- Catalogue complet des 28 sections avec compositions et jetons : `docs/SECTIONS.md`.
- **Vocabulaires de navigation** : `hotel-classique`, `hotel-experientiel`,
  `resto-classique`, `resto-convivial` — ils traduisent les slugs de pages en
  libellés de menu (ex. `chambres` → « Séjourner » en expérientiel).
- La **première section de l'accueil** est le « hero » au sens du registre
  anti-clones : c'est un axe d'unicité à 0,20.

### 2.6 `content` — ton, faits et textes

```jsonc
"content": {
  "tone": "premium",                   // premium | familial | local | minimal (indicatif rédaction)
  "facts": {                           // insérés via {{maji:content.facts.*}}
    "quartier": "Haie Vive",
    "annee": 2016,
    "specialites": ["vue sur l'océan"]
  },
  "texts": {                           // clé de jeton → texte final
    "content.texts.hero_tagline": "Face à l'Atlantique…",
    "content.texts.avis_1_texte": "…", "content.texts.avis_1_auteur": "…",
    "content.texts.faq_1_question": "…", "content.texts.faq_1_reponse": "…"
  },
  "seed": "demo-content/hotel-business" // dossier de contenus à importer
}
```

**Chaque section utilisée doit trouver ses jetons** — la liste par section est
dans `docs/SECTIONS.md`. Un jeton manquant fait échouer la provision avec la
liste exacte des occurrences : c'est voulu, aucun `{{maji:*}}` ne doit jamais
apparaître sur un site livré.

---

## 3. Préparer le dossier seed

Structure :

```
clients/hotel-atlantique/
├── dna.json
├── content.json
└── media/
    ├── hero.webp
    ├── chambre-1.webp
    └── …
```

### 3.1 `content.json`

```jsonc
{
  "media": {
    "hero":      { "file": "hero.webp",      "alt": "Façade de l'hôtel au coucher du soleil" },
    "chambre-1": { "file": "chambre-1.webp", "alt": "Chambre Océan avec lit king-size" }
  },
  "rooms": [                            // mode hôtel
    {
      "title": "Chambre Océan",
      "content": "<!-- wp:paragraph --><p>…</p><!-- /wp:paragraph -->",
      "price_from": 55000, "capacity": 2, "size_sqm": 28,
      "featured": true,
      "amenities": ["Wi-Fi", "Climatisation", "Vue mer"],
      "image": "chambre-1"
    }
  ],
  "dishes": [                           // mode restaurant (produits WooCommerce)
    {
      "name": "Poulet braisé entier",
      "description": "Mariné 12 h, braisé au feu de bois…",
      "price": "6500",
      "category": "Plats",              // catégorie de menu (product_cat)
      "badges": ["populaire"],          // populaire | epice | nouveau
      "available": true,
      "image": "plat-1"
    }
  ]
}
```

Règles :

- **`alt` obligatoire** sur chaque média — l'import échoue sinon (accessibilité AA).
- Clés `media` = valeurs des attributs `data-maji-media` des patterns utilisés
  (`hero`, `chambre-1`, `chambre-2`, `service-1..3`, `gallery-1..4`, `plat-*`, `map` —
  voir la colonne « Médias » de SECTIONS.md).
- Formats : WebP recommandé (JPEG/PNG acceptés), **≤ 200 Ko par image** pour tenir
  le budget performance ; 1600×900 pour le hero, 800×600 pour les cartes.
- Prix en FCFA entiers, sans séparateur.

### 3.2 Checklist photos client

- [ ] 1 photo hero (paysage, sujet fort, exploitable avec un overlay sombre)
- [ ] 1 photo par chambre / 1 photo par plat vedette
- [ ] 3–4 photos d'ambiance pour la galerie
- [ ] Droits d'utilisation confirmés par le client

---

## 4. Produire le site

```bash
# 1. Valider l'ADN (schéma, références, contrastes, formats)
wp maji dna validate clients/hotel-atlantique/dna.json

# 2. Contrôle anti-clones contre les sites livrés
wp maji dna check clients/hotel-atlantique/dna.json --registry=registry/registry.json

# 3. Provision complète (idempotente, relançable)
wp maji provision --dna=clients/hotel-atlantique/dna.json --with-woo
```

En cas de blocage anti-clones, la sortie donne le site le plus proche, les axes
en collision et des suggestions ordonnées par impact :

```
Score de similarité maximal : 0.75 (seuil : 0.70).
Site le plus proche : villa-karite (0.75).
Axes en collision : da, font_pair, hero.
  → changez la direction artistique (design.da)
  → changez la paire typographique (design.font_pair)
  → changez le hero de l'accueil (première section)
```

Changer **un seul axe lourd** (DA ou paire + hero) suffit généralement à
repasser sous 0,70.

---

## 5. Contrôle qualité avant livraison

- [ ] Accueil : hero correct, aucun jeton visible, images du client en place
- [ ] Mobile d'abord : parcours complet sur un téléphone Android moyen de gamme
- [ ] Bascule de DA test (Apparence → Éditeur → Styles) : le site se transforme sans perte
- [ ] Réservation test soumise → visible dans MAJI → Réservations en « Nouvelle »
- [ ] (Restaurant) Commande test COD avec zone → webhook 2xx dans le Journal ;
      tentative hors horaires → blocage avec message clair
- [ ] Lighthouse mobile ≥ 90 sur l'accueil (budget : ≤ 300 Ko hors images, LCP ≤ 2,5 s)
- [ ] Admin 100 % en français, réglages Établissement complets

## 6. Livrer et enregistrer

```bash
wp maji dna register clients/hotel-atlantique/dna.json --registry=registry/registry.json
git add registry/registry.json
git commit -m "chore: register hotel-atlantique"
git push
```

L'empreinte protège désormais l'unicité de ce site pour toutes les productions futures.

## 7. Réutiliser un site comme modèle

Un site particulièrement réussi peut devenir un modèle de départ :

```bash
wp maji export-model hotel-atlantique --dest=demo-content
```

Le dossier exporté (dna.json + content.json) se complète avec des médias
génériques et des textes anonymisés avant d'entrer au catalogue.
