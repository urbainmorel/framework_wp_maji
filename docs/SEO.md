# Note de cadrage — SEO enrichi (V2-H)

> Statut : **cadrage V2** (jalon V2-H). Fixe la cible, le catalogue de données
> structurées, le nouveau champ d'ADN et les garde-fous. Aucune implémentation ici.

## 1. Objectif

Faire ressortir chaque site produit dans la recherche **locale et mobile** d'Afrique
de l'Ouest — là où un client tape « restaurant Haie Vive Cotonou » sur son téléphone
et déclenche le **Local Pack** (les 3 fiches Google Maps en tête). On y parvient non
pas en ajoutant du contenu, mais en **structurant le contenu déjà présent** (avis,
FAQ, horaires, menu, chambres, adresse) pour rendre les sites **éligibles aux
résultats enrichis** (étoiles, FAQ, fiche établissement).

## 2. Le constat (base actuelle)

`plugins/maji-core/src/Seo/Seo.php` fournit déjà une base **propre** :
`<title>`, meta description, Open Graph + Twitter Card, et JSON-LD
`LocalBusiness`/`Hotel`/`Restaurant` (adresse, `openingHoursSpecification`, `sameAs`,
`servesCuisine`/`currenciesAccepted`), le tout **désactivé automatiquement** si Yoast,
Rank Math ou SEOPress est actif.

**Non exploité :** les avis, les FAQ, les coordonnées géographiques, le menu et les
chambres — précisément les signaux qui déclenchent les résultats enrichis.

## 3. Principes (inchangés)

- **Source unique de vérité** : toutes les données viennent des réglages
  (`maji_settings`) et de l'ADN. La cohérence **NAP** (Nom / Adresse / Téléphone) entre
  le JSON-LD, le pied de page et la future fiche Google Business Profile est donc
  **native** — c'est le 1ᵉʳ facteur de classement local.
- **Désactivation si plugin SEO majeur** : on ne double jamais Yoast/Rank Math/SEOPress.
- **Le balisage doit refléter le visible** : on n'émet un schéma (avis, FAQ) **que si
  la donnée est réellement affichée sur la page** — sinon Google pénalise. Garde-fou
  central (voir §6).
- **Aucun bourrage de mots-clés**, aucune donnée client dans le code, respect des
  interdits (pas de CDN, etc.).

## 4. Les 3 paliers

### 🥇 Palier 1 — Résultats enrichis depuis le contenu existant

| Enrichissement | Type schema.org | Source dans le framework | Gain |
|---|---|---|---|
| **FAQ** | `FAQPage` / `Question` / `Answer` | sections `commun-faq-01/02` (`faq_1..3`) | Accordéon FAQ dans les résultats |
| **Avis** | `AggregateRating` + `Review` | sections `commun-avis-*` (`avis_1..3`) | **Étoiles** sous le lien (CTR ↑) |
| **Géo** | `GeoCoordinates` + `hasMap` | **nouveau** `identity.geo` (§5) | Local Pack / Google Maps |
| **Restaurant** | `hasMenu`, `priceRange`, `acceptsReservations`, `servesCuisine` | page menu + Woo + `facts.specialites` | Résultats restaurant enrichis |
| **Hôtel** | `amenityFeature`, `makesOffer` (chambres), `priceRange`, `checkinTime` | CPT `maji_room` + taxonomie équipements | Fiches hôtel enrichies |
| **Image** | `image` (JSON-LD) + `og:image:alt` | hero/média de la page | Affichage social + Discover |

### 🥈 Palier 2 — Technique solide

- `BreadcrumbList` (JSON-LD, + fil d'Ariane optionnel).
- **Canonical explicite** et gestion `robots` (noindex des pages utilitaires / merci).
- **Meta description par page** dérivée de la 1ʳᵉ section quand l'extrait manque
  (aujourd'hui : extrait ou description générique).
- **Sitemap** : vérifier que `maji_room` figure dans `wp-sitemap.xml` et que les
  réservations (non publiques) en sont exclues.
- `twitter:title` / `twitter:description` explicites.

### 🥉 Palier 3 — Local SEO & performance (documentation, hors-code)

- **Playbook Google Business Profile** dans `docs/OPERATIONS.md` : revendication de la
  fiche, cohérence NAP avec le JSON-LD, catégories, photos, horaires, gestion des avis.
- **Core Web Vitals** : déjà un atout (polices auto-hébergées, budget motion, 0 CDN,
  WebP + `alt`) — à documenter comme argument et signal de classement.
- Soumission **Search Console / Bing Webmaster** + sitemap.

## 5. Nouveau champ d'ADN — `identity.geo`

```jsonc
"identity": {
  "…": "…",
  "geo": { "lat": 6.3654, "lng": 2.4183 }   // NOUVEAU — optionnel
}
```

- **Optionnel**, rétrocompatible (absent ⇒ pas de `GeoCoordinates`, comportement V1).
- Validé (schéma + validateur : latitude −90..90, longitude −180..180) et saisissable
  dans la page de réglages **Établissement**.
- Alimente `GeoCoordinates` + `hasMap` du JSON-LD → **signal local décisif**.

## 6. Garde-fous de qualité

- **Balisage ⇄ contenu visible** : `FAQPage` émis uniquement sur les pages qui
  contiennent réellement une section FAQ ; `AggregateRating`/`Review` uniquement quand
  des avis sont affichés. Sinon, rien (évite les pénalités « structured data mismatch »).
- **Un seul bloc JSON-LD par entité**, `@id` stable, pas de doublon avec WooCommerce
  (qui balise déjà les produits) ni avec un plugin SEO actif.
- **JSON encodé** (`wp_json_encode`, `JSON_UNESCAPED_UNICODE`), échappement des sorties.
- **Toujours désactivé** si Yoast/Rank Math/SEOPress est présent.
- **Aucune donnée inventée** : notes et avis proviennent du contenu du site, pas de
  valeurs fictives dans le schéma.

## 7. Jalons V2-H (ordre)

1. **V2-H1 — Données structurées du contenu (Palier 1).** `FAQPage`, `AggregateRating`
   + `Review`, `image`/`og:image:alt`, enrichissement `Restaurant`/`Hotel`. Garde-fou
   « balisage = visible ». *Prérequis des suivants.*
2. **V2-H2 — Géo & fiche établissement.** Champ `identity.geo` (schéma + validateur +
   réglage), `GeoCoordinates`/`hasMap`, playbook Google Business Profile.
3. **V2-H3 — Technique.** `BreadcrumbList`, canonical/robots, meta par page, sitemap CPT.

Chaque jalon respecte la *Definition of Done* (lint, analyse, tests, doc à jour) et se
livre en release automatique (release-please).

## 8. Ce qui ne change pas

- Le SEO reste **désactivé** dès qu'un plugin SEO majeur est actif.
- Données issues des réglages/ADN uniquement ; **NAP cohérent** par construction.
- Frontière thème/plugin : le SEO vit dans le **plugin** (métier), pas dans le thème.
- Aucune dépendance tierce, aucun CDN, aucune donnée client dans le code.
