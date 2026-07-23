# Note de cadrage — Motion premium (V2-G)

> Statut : **cadrage V2** (jalon V2-G, après V2-F). Ce document fixe la cible, le
> catalogue d'effets, les niveaux, les tokens et les garde-fous. Il complète
> `docs/DIVERSITE.md` : le **motion devient un 7ᵉ levier** de qualité *et* de
> diversité.

## 1. Objectif

Doter **tout site produit** d'animations dignes d'un studio haut de gamme —
**sans jamais** dégrader la performance, l'accessibilité ni la cohérence — et faire
du mouvement une **signature de chaque direction artistique**, donc un axe de plus
qui distingue les sites entre eux.

Principe fondateur (identique aux couleurs) : **le premium vient de la retenue.**
On n'ajoute pas des effets, on *chorégraphie* discrètement. Un intégrateur ne *peut
pas* produire un site qui clignote : le mouvement est curé, tokenisé, plafonné.

## 2. Les 3 exigences non négociables

1. **Performance** — on n'anime que `transform` et `opacity` (couches GPU, 60 fps).
   Jamais `top/left/width/height/box-shadow` animés en continu. Budget JS **< 3 Ko
   gzip** au total, vanilla, sans build (comme les blocs actuels). Aucune dépendance,
   aucun CDN (GSAP/AOS/Framer interdits).
2. **Accessibilité** — **tout** est neutralisé sous
   `@media (prefers-reduced-motion: reduce)` (WCAG 2.3.3). Et le contenu n'est
   **jamais** masqué si l'animation ne peut pas s'exécuter : les états « cachés »
   ne s'appliquent que si le support ET la préférence utilisateur le permettent
   (amélioration progressive stricte).
3. **Cohérence par tokens & DA** — toutes les durées/distances/courbes viennent des
   tokens `--wp--custom--maji--motion--*`. Chaque DA fixe sa personnalité de
   mouvement (lent et dramatique vs vif et discret).

## 3. Stack 100 % native (zéro dépendance)

| Effet | Technologie | JS |
|---|---|---|
| Révélation au scroll (fade + slide-up, cascade) | **CSS Scroll-driven Animations** (`animation-timeline: view()`) | 0 |
| Entrées à l'affichage (menus, cartes) | **`@starting-style`** + transitions | 0 |
| Transitions de page fluides (effet « app ») | **View Transitions API** cross-document (MPA) | 0 |
| Micro-interactions (survol image/bouton/carte, lien) | transitions CSS sur tokens | 0 |
| Header compacté au scroll, compteurs de chiffres | module vanilla `defer` (IntersectionObserver) | < 3 Ko |
| Fallback navigateurs sans scroll-driven | même IntersectionObserver → pose `.is-visible` | inclus |

## 4. Niveaux de motion (`design.motion`)

Nouveau champ d'ADN **optionnel** `design.motion`, plafonné et curé. Défaut : selon
la DA (jamais « expressive » par défaut).

| Niveau | Ce qui bouge | Pour qui |
|---|---|---|
| `none` | Rien (hors micro-interactions de survol) | Sobriété maximale, sites institutionnels |
| `subtle` | Fade des sections uniquement | Élégant, discret (**recommandé par défaut**) |
| `standard` | Fade + slide-up en cascade + zoom d'images au survol | La plupart des sites |
| `expressive` | + Ken Burns des heros + transitions de page + header compacté | Vitrines premium, gastro/hôtel de charme |

Appliqué **en base**, jamais dans les fichiers du thème : `body_class`
`maji-motion-{niveau}` + marqueurs `data-maji-animate` posés à l'import (classe
`MotionStyler`, jumelle de `SectionStyler`). Rétrocompatible : absent ⇒ `subtle`.

## 5. Catalogue d'effets (curé)

- **Reveal de section** : chaque section monte de `--...--motion--distance` (16–24 px)
  + fade, à l'entrée dans le viewport. Durée `--...--reveal`.
- **Cascade (stagger)** : les enfants directs (colonnes, cartes) s'enchaînent via
  `animation-delay: calc(var(--maji-i, 0) * --...--stagger)` ou `:nth-child`.
- **Hero Ken Burns** : cover en `scale(1 → 1.06)` très lent (niveau `expressive`).
- **Images** : `scale(1.04)` au survol dans un cadre `overflow:hidden` (rayon = token).
- **Boutons / cartes** : lift `translateY(-2px)` + ombre token au survol.
- **Liens** : soulignement qui se déploie (`transform: scaleX`).
- **Header** : transparent → solide + compactage à l'ancrage.
- **Chiffres** (`*-chiffres-01`) : compteurs animés à l'entrée.
- **Transitions de page** : fondu/glissement natif entre accueil, menu, contact.

## 6. Tokens de mouvement (`theme.json` → `custom.maji.motion`)

Étend l'existant (`duration`, `easing`) :

```jsonc
"motion": {
  "duration": "200ms",          // micro-interactions (existant)
  "duration-fast": "140ms",
  "reveal": "600ms",            // révélations au scroll
  "easing": "cubic-bezier(.2,.8,.2,1)",        // existant
  "easing-emphasized": "cubic-bezier(.16,1,.3,1)",
  "distance": "20px",           // amplitude du slide-up
  "stagger": "70ms"             // décalage entre enfants
}
```

Chaque **DA** surcharge ces valeurs pour sa personnalité (ex. `da-onyx-emeraude` :
`reveal 800ms`, `distance 28px` ; `da-solaire-minimal` : `reveal 420ms`,
`distance 12px`). C'est **l'axe de diversité** apporté par le motion.

## 7. Garde-fous de qualité

- **`prefers-reduced-motion: reduce`** coupe tout ; états cachés jamais appliqués
  sans support (`@supports (animation-timeline: view())`) ni sans préférence.
- **Compositor-only** : uniquement `transform`/`opacity` animés (test automatique
  qui interdit d'autres propriétés dans le CSS de motion).
- **Budget** : ≤ 3 Ko JS gzip, CSS de motion ≤ ~4 Ko ; chargés par site.
- **Curé par DA** : toute animation doit rester belle dans les 10 DA (critère de
  recette, comme les sections).
- **Pas de scroll-jacking**, pas d'autoplay bruyant, pas de parallaxe agressive.

## 8. Jalons V2-G (ordre)

1. **V2-G1 — Socle CSS (0 JS).** Tokens de motion étendus (base + 10 DA), stylesheet
   de motion du thème : reveal de section au scroll (scroll-driven), cascade
   `:nth-child`, micro-interactions de survol (image/bouton/carte/lien), gating
   `prefers-reduced-motion` + `@supports`. Effet immédiat sur les 40 sections, **sans
   toucher au contenu**. *Prérequis des suivants.*
2. **V2-G2 — Pilotage par l'ADN (L7).** Champ `design.motion` + `MotionStyler`
   (marqueurs + body class), petit module vanilla (header compacté, compteurs),
   View Transitions API, Ken Burns des heros ; schéma + validateur + prompt + tests.

Chaque jalon respecte la *Definition of Done* (lint, analyse, tests, build, budget
perf, doc à jour) et se livre en release auto (release-please).

## 9. Ce qui ne change pas

- Aucune bibliothèque tierce, aucun CDN front, aucun jQuery — **web natif** uniquement.
- Le CSS de motion est **livré par le thème** (couche présentation, statique,
  tokenisée) ; le pilotage par l'ADN écrit dans les **global styles utilisateur** et
  des **classes/marqueurs**, jamais dans les fichiers du thème.
- Le contenu reste **toujours lisible** sans JavaScript et sans animation.
- Seuil anti-clones et budget de performance inchangés.
