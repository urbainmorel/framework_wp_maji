# Note de cadrage — Diversité visuelle à grande échelle (V2)

> Statut : **cadrage V2** (hors périmètre V1). Ce document fixe la cible, les
> maximums de vocabulaire et la nouvelle formule d'empreinte anti-clones. Aucune
> implémentation ici : il sert de référence avant développement.

## 1. Objectif

Garantir que **tout site produit soit toujours beau, haut de gamme et bien
structuré**, tout en restant **perceptiblement différent** d'un site à l'autre —
jusqu'à **1000 sites et au-delà**, sans jamais dégrader la qualité.

Principe fondateur (inchangé) : **la beauté vient des contraintes, pas de la
liberté.** Palette sémantique, DA curées, tokens partout, WCAG AA bloquant, aucun
style en dur. Un intégrateur ne *peut pas* produire un site laid. La diversité, elle,
vient de l'ampleur du vocabulaire curé et de la richesse de l'empreinte anti-clones.

## 2. Les 6 leviers retenus pour la V2

| # | Levier | V1 (actuel) | V2 (cible) | Maximum (plafond) |
|---|---|---|---|---|
| L1 | Directions artistiques (DA) | 3 | **10** | **12** |
| L2 | Heros par secteur | 6 | **10** | **12** |
| L3 | Sections de contenu | 16 | **40** | **48** |
| L4 | Paires typographiques | 6 | **12** | **16** |
| L5 | Empreinte anti-clones enrichie | 6 axes | **11 axes** | — |
| L6 | Contrôle visuel automatique | — | intégré à l'agent | — |

Compléments de vocabulaire liés :

| Élément | V1 | V2 | Maximum |
|---|---|---|---|
| Headers | 4 | 6 | 8 |
| Footers | 3 | 5 | 6 |
| Variations de style par section (block styles) | 0 | 2–3 / section | 3 |
| Rythmes d'alternance de fonds de sections | implicite | 4 | 6 |

> **Pourquoi des maximums ?** Chaque DA / hero / section doit rester *curée,
> maintenue et testée dans toutes les DA*. Au-delà de ces plafonds, le coût de
> maintenance dépasse le gain de diversité, d'autant que l'empreinte anti-clones
> sature déjà bien au-dessus de 1000 sites (voir §5). Les maximums sont des
> **plafonds de qualité**, pas des objectifs à remplir coûte que coûte.

### Détail par levier

- **L1 — DA (10, max 12).** Le levier n°1 : la DA change *tout* le ressenti
  (palette, typo par défaut, rayons, ombres, duotone, styles de blocs). Chaque DA
  reste un ensemble cohérent conçu à la main, validé dans les deux secteurs.
- **L2 — Heros (10/secteur, max 12).** Le hero est la première impression et un axe
  d'empreinte fort. Plus de compositions (DOM distincts) = plus de diversité
  structurelle réelle.
- **L3 — Sections (40, max 48).** Enrichit la combinatoire des pages et nourrit le
  hash de composition. Objectif : couvrir tous les besoins hôtel/restaurant avec
  plusieurs variantes de chaque intention (menu, avis, galerie, équipements…).
- **L4 — Paires typo (12, max 16).** Doublement du catalogue (licences SIL OFL,
  WOFF2, ≤ 4 fichiers chargés/site).
- **L5 — Empreinte enrichie (§4).** Fait *compter* dans l'unicité des axes
  aujourd'hui ignorés (espacement, rayons, traitement d'image, rythme des fonds,
  variantes de style), ce qui récompense et impose leur variation.
- **L6 — Contrôle visuel automatique.** L'agent orchestrateur capture l'accueil,
  s'auto-critique (hiérarchie, densité, contraste, équilibre) et ajuste **avant
  livraison**. Relève le *plancher* de qualité, pas seulement la moyenne.

## 3. Nouveaux champs d'ADN (design)

Le bloc `design` du `dna.json` gagne :

```jsonc
"design": {
  "da": "…", "font_pair": "…", "palette": { … },
  "radius_scale": "sm|md|lg",
  "spacing_mood": "compact|normal|aere",
  "image_treatment": { "hero_ratio": "…", "card_ratio": "…", "overlay": "…", "duotone": false },
  "section_bg_rhythm": "uni|alterne|bandes|contraste",   // NOUVEAU — alternance des fonds de sections
  "section_style": "auto|net|ombre|minimal"              // NOUVEAU — variante de block style par défaut des sections
}
```

`section_bg_rhythm` et `section_style` sont appliqués via les global styles
utilisateur et des `blockTypes`/block styles, **jamais** dans les fichiers du thème
(contrat §5.2 inchangé). Rétrocompatibilité : champs optionnels, valeur par défaut
= comportement V1.

## 4. Empreinte anti-clones enrichie (formule V2)

L'empreinte passe de 6 à **11 axes**. Poids conçus pour que la **« trilogie de
première impression »** (DA + hero + paire typo) soit **unique sur toute la
flotte** : leur somme égale exactement le seuil.

| Axe | Poids V2 | Cardinalité cible |
|---|---|---|
| `da` | **0.30** | 10 |
| `hero` | **0.22** | 10 / secteur |
| `font_pair` | **0.18** | 12 |
| `palette_hue_bucket` | 0.06 | 12 (teinte primaire /30°) |
| `header` | 0.05 | 6 |
| `footer` | 0.04 | 5 |
| `image_treatment_bucket` | 0.04 | ~6 (overlay × duotone × ratio) |
| `section_bg_rhythm` | 0.03 | 4 |
| `spacing_mood` | 0.03 | 3 |
| `composition_hash` | 0.03 | élevé (ordre + variantes de style) |
| `radius_scale` | 0.02 | 3 |
| **Total** | **1.00** | — |

Seuil de blocage : **0,70** (inchangé).

Conséquence directe : `da + hero + font_pair = 0.30 + 0.22 + 0.18 = 0.70`. Deux
sites **ne peuvent pas** partager à la fois la même DA, le même hero et la même
paire typo. C'est la **garantie de distinction perceptuelle** : la première
impression de chaque site de la flotte est unique. Les 8 autres axes ajoutent une
diversité fine (couleurs, rythme, formes, composition) et affinent la détection des
quasi-clones (ex. même DA + même hero + beaucoup de secondaires ⇒ ≥ 0,70 ⇒ bloqué).

La classe `Registry` conserve son API (`fingerprint`, `similarity`, `check`,
`suggestions`) ; seuls la table des poids et le calcul des nouveaux axes évoluent.
Les suggestions listeront les nouveaux axes en collision.

## 5. Capacité — démonstration chiffrée

Garantie « trilogie unique » ⇒ nombre de combinaisons distinctes (DA × hero ×
paire) :

| Vocabulaire | DA | hero/secteur | paires | Trilogies uniques / secteur | 2 secteurs |
|---|---|---|---|---|---|
| V1 actuel | 3 | 6 | 6 | 108 | 216 |
| **V2 cible** | 10 | 10 | 12 | **1 200** | **2 400** |
| Plafond (max) | 12 | 12 | 16 | 2 304 | 4 608 |

- **V1 (108/secteur)** : confortable pour l'objectif produit (~30 sites), tendu vers
  150–200 — d'où le besoin de la V2 pour l'ambition « 1000 ».
- **V2 (1 200/secteur, 2 400 au total)** : **> 1000 sites perceptiblement distincts
  garantis**, chacun avec une première impression unique. Et ce n'est qu'un plancher :
  deux sites peuvent partager 2 des 3 axes de tête (ex. même DA + même hero, paires
  différentes) et rester valides s'ils divergent assez sur les axes secondaires — la
  capacité réelle est donc **supérieure à 1 200/secteur**.
- **Plafond (2 304/secteur)** : marge large ; au-delà, on n'ajoute plus de vocabulaire
  (coût de maintenance) mais on s'appuie sur les axes secondaires et la variation de
  contenu/photos.

Au-delà de la trilogie, la couleur (12 teintes), le header (6), le footer (5), le
traitement d'image (~6), le rythme de fonds (4), l'humeur d'espacement (3), les
rayons (3) et le hash de composition multiplient encore les rendus distincts : la
diversité *perçue* effective se compte en **dizaines de milliers** de rendus.

## 6. Garde-fous de qualité (inchangés et renforcés)

- **Contraste WCAG AA bloquant** sur toutes les paires critiques (déjà en V1) —
  étendu aux nouvelles DA et à `section_bg_rhythm` (chaque combinaison fond/texte
  vérifiée).
- **Aucun style en dur** : tout passe par les tokens et les global styles.
- **Rendu validé dans chaque DA** : toute nouvelle section doit être belle dans les
  10 DA (critère de recette, comme en V1 pour 3).
- **Contrôle visuel automatique (L6)** : capture + auto-critique de l'agent avant
  livraison ; journalisé dans le rapport de provision.
- **Budget de performance** maintenu (≤ 300 Ko hors images, ≤ 4 WOFF2/site) malgré
  l'élargissement du catalogue — les polices et styles sont chargés *par site selon
  l'ADN*, pas en bloc.

## 7. Jalons V2 proposés (ordre)

1. **V2-A — Empreinte enrichie (L5). ✅ Livré.** Nouveaux axes + poids + tests (cas
   limite 0,70, non-régression V1). Petit changement, gros gain de diversité mesurée,
   sans nouveau design. *Prérequis des suivants.*
2. **V2-B — Champs d'ADN `section_bg_rhythm` / `section_style` (L3/L5). ✅ Livré.**
   Appliqués à l'import via `SectionStyler` (contenu en base) + block styles du plugin
   (tokens only) ; schéma + validateur + 6 ADN enrichis. Rythme de fonds cantonné aux
   neutres validés WCAG ; heros et sections à fond intentionnel préservés.
3. **V2-C — +7 DA (L1). ✅ Livré.** 3 → 10 directions artistiques (`da-lagune-fraiche`,
   `da-nuit-cuivre`, `da-savane-doree`, `da-terracotta-vive`, `da-ardoise-moderne`,
   `da-jardin-botanique`, `da-onyx-emeraude`), chacune curée (palette 10 slugs, duotones,
   rayons/ombres/espacement, typographie, block styles) et validée WCAG AA sur toutes les
   paires critiques par un test automatique. Correction au passage d'un défaut d'accent
   non conforme de `da-solaire-minimal`.
4. **V2-D — +4 heros/secteur et +24 sections (L2/L3).** 6 → 10 heros, 16 → 40 sections.
   Découpé en sous-lots : **D1 heros ✅ (10/secteur)**, D2 sections restaurant, D3 sections
   hôtel, D4 sections communes.
5. **V2-E — +6 paires typo (L4). ✅ Livré.** 6 → 12 paires (fp-07…fp-12), par
   recombinaisons inédites des 11 familles OFL déjà auto-hébergées (aucune police
   ajoutée, budget ≤ 4 WOFF2/site préservé) ; test de garde sur familles, WOFF2 et
   budget.
6. **V2-F — Contrôle visuel automatique (L6).** Intégration capture + auto-critique
   dans l'agent orchestrateur.

Chaque jalon respecte la *Definition of Done* du projet (lint, analyse, tests,
build, budget perf, doc à jour) et reste dans l'interdit V2 déjà connu (pas de
Mobile Money, pas de multilingue, etc. — hors périmètre de cette note).

## 8. Ce qui ne change pas

- Aucun page builder tiers — **Gutenberg / FSE natif** uniquement.
- Personnalisation dans les **global styles utilisateur**, jamais dans les fichiers
  du thème (mises à jour de flotte sûres).
- Frontière thème (présentation) / plugin (métier) stricte.
- Le seuil anti-clones reste **0,70**, la commande `wp maji dna check` reste
  bloquante à la livraison.
