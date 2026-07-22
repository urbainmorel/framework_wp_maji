# PRD — MAJI Framework & MAJI Core

**Usine à sites WordPress pour hôtels et restaurants — marchés d'Afrique de l'Ouest francophone**

| Champ | Valeur |
|---|---|
| Version | 1.0 |
| Date | 21 juillet 2026 |
| Propriétaire produit | MAJI — Solution Digitale 360° |
| Destinataire | Agent de codage IA (OpenAI Codex ou Claude Code) |
| Document compagnon | `STI-MAJI-Framework.md` — spécifications techniques d'implémentation |

> **Note à l'agent** : ce PRD définit le *quoi* et le *pourquoi*. Le *comment* (architecture, schémas, commandes, jalons, critères d'acceptation techniques) est dans la STI. Lire les deux intégralement avant d'écrire la moindre ligne de code. En cas de conflit, la STI prime sur le PRD pour les choix techniques ; le PRD prime pour le périmètre.

---

## 1. Contexte et vision

MAJI est une agence digitale opérant sur les marchés francophones d'Afrique de l'Ouest (Bénin, Côte d'Ivoire, Sénégal). Elle veut vendre des sites vitrines transactionnels à des hôtels et restaurants locaux, avec un objectif d'environ **30 sites clients**.

Produire 30 sites artisanalement n'est pas viable. Produire 30 clones d'un même template détruit la valeur perçue. Le produit à construire n'est donc **pas un thème** : c'est une **usine à sites** — une base technique commune qui produit rapidement des sites **perçus comme uniques** (design, structure, contenus), tout en restant maintenable de manière centralisée.

**Vision** : « Base technique commune + modèle sectoriel + direction artistique + ADN de site + contenus réels du client = site client unique, livré en 3 à 6 heures de travail humain. »

## 2. Problème à résoudre

1. **Vitesse** : partir d'une page blanche coûte 40–80 h par site. Cible : 3–6 h de travail humain par site après provision automatisée.
2. **Unicité** : les frameworks produisent naturellement des clones (mêmes compositions, même rythme, mêmes cadrages). L'unicité perçue vient de la *composition, des images et du texte*, pas seulement des couleurs. Le système doit **imposer** la divergence, pas seulement la permettre.
3. **Maintenance de flotte** : un correctif de sécurité ou une amélioration doit se déployer sur les ~30 sites sans intervention site par site.
4. **Contexte marché** : visiteurs majoritairement sur Android d'entrée/milieu de gamme, en 3G/4G instable ; canal de conversion dominant = **WhatsApp** ; devise **FCFA (XOF)** ; paiement à la livraison courant, Mobile Money en croissance ; hébergements mutualisés LAMP classiques.

## 3. Objectifs mesurables

| ID | Objectif | Cible | Mesure |
|---|---|---|---|
| O1 | Temps de production d'un site (après usine en place) | ≤ 6 h humaines ; provision machine ≤ 15 min | Chronométrage sur les 4 sites modèles |
| O2 | Unicité inter-sites | Score de similarité < 0,70 entre tout nouveau site et chaque site du registre (règle STI §5.3) | `wp maji dna check` bloquant |
| O3 | Performance mobile | Lighthouse Perf ≥ 90 ; LCP ≤ 2,5 s (profil mobile milieu de gamme / Fast 3G) ; CLS < 0,1 | Audit sur les 4 sites modèles |
| O4 | Déploiement de flotte | Correctif thème/plugin propagé à tous les sites en < 30 min | Mécanisme de mise à jour STI §7 |
| O5 | Autonomie client | Un gérant non technique modifie un prix ou un plat en < 2 min | Test utilisateur sur l'admin |
| O6 | Fiabilité des notifications | 100 % des commandes/réservations génèrent un webhook livré ou journalisé avec relance | Journal webhooks + tests e2e |

## 4. Utilisateurs

**P1 — Intégrateur MAJI (utilisateur principal du framework).** Provisionne un site via CLI, choisit/ajuste l'ADN, importe les contenus, contrôle la qualité, livre. Ce que l'usine doit lui donner : vitesse, garde-fous anti-clones, zéro tâche répétitive manuelle.

**P2 — Gérant d'établissement (client final, admin WordPress).** Non technique, souvent sur mobile. Met à jour plats, prix, photos, chambres, horaires ; traite les demandes de réservation ; reçoit les notifications sur WhatsApp. Ce que le produit doit lui donner : une admin en français, simple, sans réglages dangereux exposés.

**P3 — Visiteur final.** Mobile d'abord, connexion médiocre, veut voir le menu/les chambres, les prix en FCFA, commander ou réserver, et basculer sur WhatsApp en un tap.

## 5. Périmètre produit — composants livrables

| ID | Composant | Description courte |
|---|---|---|
| C1 | Thème `maji-framework` | Block theme (FSE) entièrement piloté par tokens `theme.json`, multi-identités |
| C2 | Plugin `maji-core` | Fonctionnalités métier : modes hôtel/restaurant, réservations, commandes, WhatsApp, webhooks, SEO de base |
| C3 | Bibliothèque de sections | ≥ 15 sections + 6 heros hôtel + 6 heros restaurant, en variantes de **composition** (DOM différent, pas seulement styles) |
| C4 | Directions artistiques (DA) | ≥ 3 en V1 (cible 10 en V2), livrées comme *style variations* cohérentes (tokens + styles de blocs + règles photo) |
| C5 | Système « ADN de site » | Fichier `dna.json` par client + validateur + **registre anti-clones bloquant** |
| C6 | Outillage CLI | Commandes `wp maji …` : validate, check, apply-dna, import-content, provision, export-model |
| C7 | Mises à jour de flotte | Thème et plugin auto-mis à jour depuis les releases GitHub sur tous les sites |
| C8 | Workflows n8n | Fichiers JSON importables : commande restaurant, réservation hôtel, changement de statut |
| C9 | Sites modèles | 2 hôtels + 2 restaurants, construits **avec l'usine elle-même** (preuve de fonctionnement) |
| C10 | Documentation | Installation, exploitation, catalogue des sections, guide de création d'un site |

## 6. Exigences fonctionnelles

Priorités : **P0** = indispensable V1 ; **P1** = V1 si le budget temps le permet ; **P2** = V2.

### 6.1 Thème (C1)

| ID | Exigence | Prio |
|---|---|---|
| F-T1 | Tous les choix visuels (couleurs sémantiques, typographies fluides, espacements, rayons, ombres, mouvement, largeurs) passent par des tokens `theme.json` ; **aucune valeur codée en dur** dans les patterns | P0 |
| F-T2 | Chaque DA est une *style variation* qui change palette par défaut, paire typographique, rayons, ombres, presets duotone et styles de sections — basculer de DA transforme visiblement tout le site sans toucher au contenu | P0 |
| F-T3 | 4 variantes de header (transparent, solide, centré, split) et 3 de footer, sélectionnables par l'ADN | P0 |
| F-T4 | Patterns nommés, catégorisés (hôtel / restaurant / commun) et versionnés ; contenus de démonstration via jetons `{{maji:*}}` remplacés à l'import | P0 |
| F-T5 | Polices auto-hébergées (WOFF2, licences libres), catalogue de ≥ 6 paires typographiques référencées par identifiant dans l'ADN ; **aucun CDN tiers** (polices, scripts, CSS) | P0 |
| F-T6 | Aucune dépendance jQuery en front ; interactivité légère (menu mobile, galerie) en natif | P0 |
| F-T7 | Compatible WooCommerce (gabarits boutique/produit/panier/commande stylés par les tokens) | P0 |

### 6.2 Plugin — socle commun (C2)

| ID | Exigence | Prio |
|---|---|---|
| F-C1 | Page de réglages « Établissement » : nom, type, téléphone, WhatsApp, e-mail, adresse, horaires, devise (XOF par défaut), réseaux sociaux. Ces données alimentent automatiquement header, footer, boutons WhatsApp, formulaires, page contact et données structurées — **jamais de double saisie** | P0 |
| F-C2 | Modes **Hôtel** / **Restaurant** activant les modules correspondants (un site peut activer les deux) | P0 |
| F-C3 | Boutons/bloc WhatsApp « click-to-chat » avec message pré-rempli contextualisé (page, plat, chambre) | P0 |
| F-C4 | Webhooks sortants vers n8n, signés, avec relance automatique en cas d'échec et journal consultable en admin (détail STI §4.5) | P0 |
| F-C5 | Consentement WhatsApp (case explicite) requis et stocké avec chaque commande/réservation | P0 |
| F-C6 | SEO technique de base : titres/meta, Open Graph, JSON-LD `Hotel` / `Restaurant` / `LocalBusiness` généré depuis les réglages ; se désactive automatiquement si un plugin SEO majeur est actif | P1 |

### 6.3 Mode hôtel

| ID | Exigence | Prio |
|---|---|---|
| F-H1 | Chambres : type de contenu dédié avec galerie, prix « à partir de » en FCFA, capacité, superficie, équipements (taxonomie) | P0 |
| F-H2 | Bloc « Demande de réservation » : dates d'arrivée/départ, nombre de personnes, chambre, coordonnées, message, consentement WhatsApp ; anti-spam sans CAPTCHA visuel | P0 |
| F-H3 | Gestion des demandes en admin : liste filtrable, statuts (nouvelle → confirmée / refusée / annulée), actions rapides | P0 |
| F-H4 | Webhook à chaque nouvelle demande et à chaque changement de statut | P0 |
| F-H5 | **Explicitement hors V1** : calendrier de disponibilités et paiement de réservation. V1 = demande traitée manuellement (confirmation par WhatsApp/téléphone) | — |

### 6.4 Mode restaurant

| ID | Exigence | Prio |
|---|---|---|
| F-R1 | Plats = produits WooCommerce (simples ou variables) + champs MAJI : disponibilité, badges (populaire, épicé, nouveau) ; catégories de menu = catégories produit | P0 |
| F-R2 | Blocs d'affichage du menu en variantes de composition : grille photo, liste typographique, par catégories | P0 |
| F-R3 | Tunnel de commande allégé : choix livraison / retrait sur place ; zones de livraison avec frais par zone ; **paiement à la livraison (COD)** en V1 | P0 |
| F-R4 | Horaires d'ouverture : hors horaires, la commande est bloquée avec message clair (le menu reste consultable) | P0 |
| F-R5 | Webhook à chaque commande et changement de statut | P0 |
| F-R6 | Bloc « Réservation de table » (date, heure, couverts, coordonnées) traité comme une demande (cf. F-H2/F-H3) | P1 |
| F-R7 | Paiement Mobile Money via agrégateurs régionaux (FedaPay, KkiaPay, PayDunya…) | P2 |

### 6.5 Système d'unicité (C4, C5) — cœur différenciant

| ID | Exigence | Prio |
|---|---|---|
| F-U1 | Un fichier **ADN** (`dna.json`) par site fixe : identité, DA, paire typo, palette, humeur d'espacement, rayons, traitement d'image, header/footer, vocabulaire de navigation, pages et ordre des sections avec leurs variantes (schéma exact : STI §5.1) | P0 |
| F-U2 | `wp maji apply-dna` applique l'ADN **sans modifier les fichiers du thème** (via les global styles utilisateur), garantissant que les mises à jour de flotte n'écrasent jamais la personnalisation | P0 |
| F-U3 | **Registre anti-clones** : avant livraison, l'empreinte du nouveau site est comparée à celles de tous les sites livrés ; similarité ≥ 0,70 ⇒ blocage avec explication et suggestions d'axes à changer | P0 |
| F-U4 | Vocabulaire de navigation variable par site (ex. « Chambres/Services » vs « Séjourner/Expériences »), ≥ 2 jeux par secteur en V1 | P0 |
| F-U5 | Traitement d'image par DA : ratios de cadrage imposés, presets duotone/overlay natifs Gutenberg. V2 : pipeline de génération d'images traitées | P1 |
| F-U6 | Le validateur d'ADN rejette toute palette dont les paires critiques (texte/fond, bouton/texte de bouton) ne respectent pas le contraste WCAG AA | P0 |

### 6.6 Outillage et exploitation (C6–C9)

| ID | Exigence | Prio |
|---|---|---|
| F-O1 | `wp maji provision` : enchaîne validation ADN → contrôle registre → application → import contenus → réglages ; **idempotent et relançable** | P0 |
| F-O2 | Import de contenus depuis JSON + dossier de médias : pages, sections choisies, chambres/plats, textes, coordonnées | P0 |
| F-O3 | Export d'un site en « modèle » réutilisable (pages + réglages + contenus de démonstration) | P1 |
| F-O4 | Mise à jour de flotte : thème et plugin vérifient et installent les nouvelles versions depuis les releases GitHub | P0 |
| F-O5 | Workflows n8n livrés en JSON importables + documentation de branchement (URL, secret, WhatsApp) | P0 |

## 7. Exigences non fonctionnelles

| ID | Exigence |
|---|---|
| N1 | **Performance** : budget chiffré bloquant défini en STI §10 (≈ 300 KB hors images, LCP ≤ 2,5 s mobile 3G rapide, CLS < 0,1) |
| N2 | **Hébergement** : fonctionne sur mutualisé LAMP standard — PHP ≥ 8.1, MySQL 5.7+/MariaDB 10.4+, aucune dépendance serveur exotique, compatible cache de pages |
| N3 | **Accessibilité** : socle AA — contrastes garantis par F-U6, navigation clavier, `alt` requis à l'import, HTML sémantique |
| N4 | **Langue** : interface admin et front en français (`fr_FR`) ; toutes les chaînes traduisibles (text domains dédiés) |
| N5 | **Sécurité** : WordPress Coding Standards, échappement/sanitisation systématiques, nonces + vérifications de capacités, secrets jamais journalisés (détail STI §11) |
| N6 | **Vie privée** : aucun appel front vers des CDN/traqueurs tiers ; données personnelles limitées au nécessaire ; consentement WhatsApp explicite |
| N7 | **Compatibilité** : WordPress ≥ 6.6, deux dernières versions majeures testées en CI ; WooCommerce version stable courante |

## 8. Contraintes de conception

1. **Gutenberg natif uniquement.** Aucun page builder tiers (Elementor, Divi…) — pour la performance, la maintenance et la pérennité.
2. **Les patterns ne contiennent jamais de données client.** Toute donnée d'établissement vient des réglages ou de l'ADN.
3. **Le thème ne contient aucune logique métier ; le plugin ne contient aucune décision de style.** La frontière est stricte (un site doit survivre à un changement de thème pour ses données).
4. **L'usine se mange elle-même** : les 4 sites modèles (C9) doivent être produits via `wp maji provision`, pas à la main.

## 9. Phasage

**V1 (ce projet, jalons M1–M6 de la STI §13)** : tout le périmètre P0 + P1 ci-dessus, 3 DA, 4 sites modèles.

**V2 (hors périmètre de ce dépôt de travail, à ne pas commencer)** : Mobile Money (F-R7), calendrier de disponibilités hôtel, suppléments/options avancés sur les plats, 10 DA, pipeline d'images génératif, comparaison visuelle automatique des accueils (captures + hash perceptuel), multilingue.

## 10. Hors périmètre V1 (rappel explicite)

Moteur de réservation avec paiement et disponibilités ; channel manager ; application mobile ; envoi de messages WhatsApp *depuis WordPress* (c'est n8n qui parle à l'API WhatsApp — WordPress ne fait qu'émettre des webhooks) ; éditeur graphique de DA (les DA sont des fichiers) ; multisite WordPress (chaque client = installation isolée).

## 11. Risques et parades

| Risque | Parade |
|---|---|
| Effet « clones » malgré le framework | Registre anti-clones **bloquant** (F-U3) + DA conçues comme ensembles cohérents, pas comme curseurs libres |
| Dérive de performance au fil des sections | Budget de performance en intégration continue (STI §10 et §12) |
| Hébergeurs faibles / configurations variées | N2 : zéro dépendance exotique, scripts d'installation tolérants, cache compatible |
| Casse lors des mises à jour de flotte | F-U2 : personnalisation hors des fichiers du thème ; versionnage sémantique ; canal beta testé sur un site interne |
| Montée de version WordPress/Woo | CI sur les deux dernières majeures (N7) |
| Dérive de périmètre par l'agent IA | STI §0 : toute ambiguïté ⇒ option la plus simple + trace dans `docs/DECISIONS.md` ; V2 interdit |

## 12. Critères d'acceptation produit (Definition of Done V1)

- [ ] Les 4 sites modèles sont produits de bout en bout par `wp maji provision` en ≤ 15 min machine chacun.
- [ ] Les 4 accueils obtiennent Lighthouse Perf ≥ 90 (mobile) et respectent le budget STI §10.
- [ ] `wp maji dna check` bloque effectivement un ADN trop proche d'un site du registre, avec message actionnable.
- [ ] Basculer la DA d'un site modèle transforme visiblement son apparence sans perte de contenu.
- [ ] Une commande restaurant test (COD, zone de livraison, hors/dans horaires) et une demande de réservation hôtel test déclenchent chacune un webhook signé, reçu par un n8n de test, au format exact de la STI Annexe A ; un échec simulé est relancé et journalisé.
- [ ] Un correctif publié en release GitHub est proposé et installable depuis l'admin des 4 sites modèles.
- [ ] Aucune chaîne en dur non traduisible ; admin 100 % en français.
- [ ] `composer lint`, analyse statique, build et tests passent en CI ; documentation C10 livrée.
