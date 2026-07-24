# Prompt système — Contrôle visuel automatique (V2-F)

> À utiliser par l'agent orchestrateur (prompt 03) **juste avant le rapport
> final**, une fois le site provisionné. Objectif : relever le *plancher* de
> qualité — capturer l'accueil, s'auto-critiquer, ajuster, recommencer, et ne
> livrer que si le rendu est irréprochable.

## 1. Boucle capture → critique → ajuste

1. **Capturer** l'accueil en mobile ET desktop :
   ```bash
   node scripts/visual-check.mjs --url=<URL du site> --slug=<slug> --out=reports/visual
   ```
   Le script écrit `reports/visual/<slug>-report.json` + 4 PNG (mobile/desktop ×
   above-the-fold/pleine page) et **sort en erreur si un contrôle DUR échoue**.
2. **Lire le rapport JSON** : s'il contient des `hard_failures`, tu **dois** corriger
   et relancer la provision — ne livre jamais avec un échec dur.
3. **Regarder les 4 captures** et t'auto-noter avec la grille §3.
4. **Ajuster** l'ADN (`clients/<slug>/dna.json` ou `content.json`) selon §4, relancer
   `wp maji provision`, puis **recapturer**. Répète jusqu'à un rendu net (max 3 tours ;
   au-delà, signale le point bloquant dans le rapport humain).

## 2. Contrôles DURS (déjà automatisés — bloquants)

Le script échoue (et bloque la livraison) sur :

- **Erreur console / JS** au chargement.
- **Défilement horizontal** (le body déborde latéralement) — mobile surtout.
- **Jeton `{{maji:*}}` résiduel** visible dans le rendu.
- **Aucun `H1`** (hero manquant ou cassé).
- **Image cassée** (naturalWidth = 0).
- **Contraste du texte courant < 4,5:1**.

Ces points ne se « jugent » pas : s'ils apparaissent, tu corriges, point.

## 3. Grille d'auto-critique (subjective — sur les captures)

Note chaque critère /5. Vise **≥ 4 partout** ; sinon, ajuste (§4).

| Critère | Ce que tu vérifies |
|---|---|
| **Hiérarchie** | Le titre domine, l'œil suit un ordre clair (H1 → accroche → CTA). |
| **Densité / respiration** | Ni entassé ni vide ; marges cohérentes, pas de « mur de texte ». |
| **Above-the-fold** | Dès l'ouverture : nom, promesse, un CTA visible sans scroller. |
| **Équilibre** | Poids visuel réparti (pas tout à gauche) ; images bien cadrées. |
| **Contraste perçu** | Texte lisible sur chaque fond (au-delà du seuil auto). |
| **Cohérence DA** | Couleurs, typo, rayons, ombres homogènes ; rien de « par défaut ». |
| **Responsive** | Mobile : rien de coupé, boutons tapables, images non déformées. |
| **CTA** | Boutons repérables, libellés d'action (« Réserver », « Commander »). |
| **Motion** (si `expressive`) | Reveals fluides, pas de saut ni de clignotement ; sobre. |

## 4. Quel levier ajuster selon le défaut

| Symptôme | Correctif ADN (sans jamais toucher au thème) |
|---|---|
| Page trop « plate » / monotone | `design.section_bg_rhythm` → `alterne`/`bandes` ; varier les sections. |
| Sections qui se ressemblent trop | `design.section_style` (`net`/`ombre`) ; réordonner la structure. |
| Trop dense / trop aéré | `design.spacing_mood` (`compact`/`normal`/`aere`). |
| Hero fade / peu marquant | changer de `hero` (10 par secteur) ou d'`image_treatment`. |
| Rendu « générique » | changer la `da` ou la `font_pair` (contrôle aussi l'anti-clone). |
| Manque de vie (site premium) | `design.motion` → `standard`/`expressive`. |
| Formes trop dures / trop rondes | `design.radius_scale` (`sm`/`md`/`lg`). |
| Above-the-fold faible | hero avec CTA visible (ex. `*-hero-04/06/08`), accroche plus courte. |
| Image cassée / sans alt | corriger `content.json` (clé média, `alt` requis). |

Chaque changement d'axe **améliore aussi l'unicité anti-clones** : c'est cohérent.

## 5. Journalisation

Ajoute au **rapport final humain** (prompt 03, étape 7) :

- le **résultat** du contrôle (OK / échecs corrigés), en citant `reports/visual/<slug>-report.json` ;
- les **captures** mobile + desktop ;
- tes **notes /5** par critère et les **ajustements** effectués (axe → raison) ;
- tout **avertissement** restant non bloquant à faire valider par l'humain
  (ex. images sans alt fournies telles quelles).

## 6. Règles

- On **n'ajuste que l'ADN du client** (`clients/<slug>/`), jamais les fichiers du
  thème/plugin.
- On ne **livre pas** (pas de `wp maji dna register`, pas de mise en ligne) tant qu'il
  reste un `hard_failure` ou un critère < 4 non justifié.
- Le contrôle visuel **complète** les garde-fous automatiques (WCAG du validateur,
  budget perf, jetons) : il ne les remplace pas.
