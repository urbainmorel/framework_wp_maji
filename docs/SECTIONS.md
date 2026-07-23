# Catalogue des sections MAJI

Bibliothèque de patterns du thème `maji-framework` (STI Annexe C). Chaque pattern :

- utilise **exclusivement** les tokens `theme.json` (aucun style en dur) ;
- contient des jetons `{{maji:*}}` remplacés à l'import — jamais visibles en front ;
- marque ses images par `data-maji-media="clé"` (remplacées par les médias du dossier seed à l'import) ;
- est validé dans les 3 directions artistiques, mobile d'abord.

## Heros hôtel

| Slug | Composition | Jetons principaux | Médias |
|---|---|---|---|
| `maji/hotel-hero-01` | Cover plein écran, titre centré, double CTA | `identity.name`, `content.texts.hero_tagline`, `content.facts.quartier` | `hero` |
| `maji/hotel-hero-02` | Colonnes 55/45, texte gauche, image portrait droite | `identity.name`, `content.facts.annee` | `hero` |
| `maji/hotel-hero-03` | Éditorial asymétrique : titre large, image 64 % + carte décalée | `identity.name`, `content.texts.hero_tagline` | `hero` |
| `maji/hotel-hero-04` | Cover + formulaire de réservation intégré (bloc `maji/reservation-form`) | `identity.name` | `hero` |
| `maji/hotel-hero-05` | Titre centré + mosaïque 3 images (1 portrait, 2 paysage) | `identity.name` | `hero`, `gallery-1`, `gallery-2` |
| `maji/hotel-hero-06` | Cover 70vh contenu bas gauche + carte flottante à cheval | `identity.name`, `content.texts.hero_tagline` | `hero` |
| `maji/hotel-hero-07` | Colonnes texte + carte flottante avec formulaire de réservation | `identity.name`, `content.texts.hero_tagline`, `content.facts.quartier` | — |
| `maji/hotel-hero-08` | Diaporama vertical (2 images empilées) + texte à droite | `identity.name`, `content.texts.hero_tagline`, `content.facts.annee` | `hero`, `gallery-1` |
| `maji/hotel-hero-09` | Minimal centré : médaillon rond + titre + repère quartier | `identity.name`, `content.texts.hero_tagline`, `content.facts.quartier` | `hero` |
| `maji/hotel-hero-10` | Éditorial + trois cartes de repères clés (année, quartier, ville) | `identity.name`, `content.texts.hero_tagline`, `content.facts.annee`, `content.facts.quartier` | — |

## Heros restaurant

| Slug | Composition | Jetons principaux | Médias |
|---|---|---|---|
| `maji/resto-hero-01` | Cover plein écran + bouton Commander | `identity.name`, `content.texts.hero_tagline` | `hero` |
| `maji/resto-hero-02` | Split fond primaire, plat détouré rond à droite | `identity.name`, `content.facts.quartier` | `hero` |
| `maji/resto-hero-03` | Typographique sans image, menu du jour en vedette | `content.texts.menu_du_jour`, `content.facts.specialites` | — |
| `maji/resto-hero-04` | Rangée de plats signature (bloc `maji/menu-grid` badge populaire) | `identity.name` | — |
| `maji/resto-hero-05` | Cover 100vh parallaxe, ambiance immersive | `identity.name`, `content.facts.annee` | `hero` |
| `maji/resto-hero-06` | Split image gauche + carte double CTA Commander / Réserver | `identity.name`, `identity.address.city` | `hero` |
| `maji/resto-hero-07` | Deux colonnes typographiques : titre à gauche, carte « menu du jour » à droite | `identity.name`, `content.texts.hero_tagline`, `content.texts.menu_du_jour`, `content.facts.specialites` | — |
| `maji/resto-hero-08` | Cover plein cadre + double CTA Commander / Réserver superposé | `identity.name`, `content.texts.hero_tagline`, `content.facts.specialites` | `hero` |
| `maji/resto-hero-09` | Titre centré + mosaïque de trois plats (carrés) | `identity.name`, `content.texts.hero_tagline`, `content.facts.specialites` | `hero`, `gallery-1`, `gallery-2` |
| `maji/resto-hero-10` | Cover ambiance sombre, contenu bas gauche + Commander | `identity.name`, `content.texts.hero_tagline`, `content.facts.specialites` | `hero` |

## Sections

| Slug | Composition | Blocs dynamiques |
|---|---|---|
| `maji/hotel-chambres-01` | Grille de chambres centrée | `maji/rooms-grid` |
| `maji/hotel-chambres-02` | Liste éditoriale alternée (media-text gauche/droite) | — |
| `maji/hotel-equipements-01` | Grille de 4 icônes centrées | — |
| `maji/hotel-equipements-02` | Bandes horizontales bordées | — |
| `maji/hotel-services-01` | 3 cartes image + titre + description | — |
| `maji/resto-menu-01` | Grille photo 4 colonnes + CTA Commander | `maji/menu-grid` |
| `maji/resto-menu-02` | Liste typographique étroite (640px), pointillés | `maji/menu-list` |
| `maji/resto-menu-03` | Menu complet groupé par catégories | `maji/menu-categories` |
| `maji/resto-populaires-01` | Rangée 3 plats badge « populaire », entête gauche | `maji/menu-grid` |
| `maji/resto-livraison-01` | Fond primaire : texte + tableau zones/frais | — |
| `maji/resto-menu-04` | Carte encadrée : intro + liste de plats dans une carte | `maji/menu-list` |
| `maji/resto-populaires-02` | Plats signature en grandes cartes (2 colonnes, badge « populaire ») | `maji/menu-grid` |
| `maji/resto-livraison-02` | Commander en trois étapes (cartes numérotées) | — |
| `maji/resto-apropos-01` | Notre histoire : image portrait + récit (`apropos_texte`) | — |
| `maji/resto-ambiance-01` | Dans notre salle : accroche + deux photos | — |
| `maji/resto-evenements-01` | Privatisation & événements : texte + image + CTA | — |
| `maji/resto-horaires-01` | Horaires (bloc `maji/opening-hours`) + liste des services | `maji/opening-hours` |
| `maji/resto-chiffres-01` | Fond primaire : trois repères (année, cuisine, ville) | — |
| `maji/commun-avis-01` | 3 cartes d'avis étoilées | — |
| `maji/commun-avis-02` | Citation unique pleine largeur sur fond contraste | — |
| `maji/commun-galerie-01` | Mosaïque 4 images avec lightbox native | — |
| `maji/commun-localisation-01` | Carte statique 55 % + adresse + horaires | `maji/establishment-info`, `maji/opening-hours` |
| `maji/commun-faq-01` | Accordéon natif (bloc details) | — |
| `maji/commun-cta-01` | Bandeau final fond accent, WhatsApp + Réserver | `maji/whatsapp-button` |

## Blocs d'affichage du plugin

| Bloc | Rôle | Attributs |
|---|---|---|
| `maji/rooms-grid` | Grille de chambres (CPT `maji_room`) | `count`, `columns`, `amenity`, `featuredOnly` |
| `maji/menu-grid` | Plats Woo en grille photo | `count`, `columns`, `category`, `badge` |
| `maji/menu-list` | Plats Woo en liste typographique | `count`, `category` |
| `maji/menu-categories` | Menu complet par `product_cat` | `perCategory` |
| `maji/whatsapp-button` | Click-to-chat message contextualisé | `context`, `item`, `label`, `variant` |
| `maji/opening-hours` | Horaires des réglages Établissement | — |
| `maji/establishment-info` | Coordonnées / adresse / réseaux | `variant` |
| `maji/reservation-form` | Demande de réservation chambre | `showRoomPicker` |
| `maji/table-booking-form` | Demande de réservation de table | — |

## Jetons de contenu attendus

Chaque site doit fournir (via `dna.json` → `content.texts` ou le seed) au minimum :
`hero_tagline`, `chambres_intro` / `menu_intro`, `cta_titre`, les avis (`avis_N_texte`, `avis_N_auteur`),
la FAQ (`faq_N_question`, `faq_N_reponse`) et, selon les sections utilisées, les descriptions de
chambres/services et les zones de livraison. La commande `wp maji provision` échoue si un jeton
reste non résolu dans une page publiée.
