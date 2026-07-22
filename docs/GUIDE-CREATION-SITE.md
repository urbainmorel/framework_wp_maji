# Guide de création d'un site — pas à pas, sans rien supposer

Ce guide vous accompagne pour fabriquer un site client complet, même si vous
n'avez **jamais utilisé de terminal ni écrit de code**. On avance lentement,
étape par étape, avec un exemple réel que vous pouvez recopier.

Prenez votre temps. Aucune étape n'est « évidente » : chacune est expliquée.

---

## Partie 1 — Comprendre le principe (5 minutes de lecture)

### Ce que fait cet outil, en une phrase

C'est une **machine à fabriquer des sites**. Vous lui donnez deux choses :

1. **La fiche d'identité du client** (couleurs, nom, téléphone, quelles pages…) —
   c'est un petit fichier texte.
2. **Les contenus du client** (photos, plats ou chambres, textes) — un dossier
   avec des fichiers.

Vous lancez ensuite **une seule commande**, et la machine construit le site fini.

> Image mentale : c'est comme une machine à café à capsules. La **capsule**
> (fiche d'identité + contenus), vous la préparez. Le **bouton** (la commande),
> vous appuyez dessus. Le **café** (le site fini) sort tout seul.

### Les deux fichiers que VOUS préparez

| Nom | À quoi ça sert | C'est quoi concrètement |
|---|---|---|
| **La fiche d'identité** (`dna.json`) | Nom, couleurs, police, pages du site | Un fichier texte |
| **Le dossier de contenus** | Les photos, les plats/chambres, les textes | Un dossier avec un fichier `content.json` et un sous-dossier `media/` pour les photos |

Ne vous inquiétez pas des extensions bizarres (`.json`) : ce sont **juste des
fichiers texte** avec des règles de présentation. On y revient plus loin.

### Ce que vous n'avez PAS à faire

- ❌ Écrire du code (PHP, HTML, CSS…). Jamais.
- ❌ Toucher au design dans WordPress. Les couleurs et polices viennent de la fiche d'identité.
- ❌ Créer les pages à la main. La machine les crée pour vous.

Votre travail se résume à : **remplir 2 fichiers, appuyer sur le bouton, vérifier
le résultat.**

---

## Partie 2 — Préparer votre ordinateur (à faire une seule fois)

Vous avez besoin de deux outils. Un collègue technique a peut-être déjà tout
installé sur une machine dédiée — si c'est le cas, sautez à la Partie 3. Sinon,
suivez ceci.

### Outil 1 — Un éditeur de texte correct

N'utilisez PAS Word ni le Bloc-notes basique : ils abîment les fichiers.
Installez **Visual Studio Code** (gratuit) : https://code.visualstudio.com/

C'est un éditeur qui colore le texte et **vous prévient quand vous faites une
faute de frappe** dans un fichier `.json`. Très pratique.

### Outil 2 — Le « terminal »

Le **terminal** (aussi appelé « ligne de commande » ou « console ») est une
fenêtre où l'on **tape des ordres au clavier** au lieu de cliquer. Ça fait peur
la première fois, mais vous n'aurez qu'**une seule commande à taper**. Promis.

Comment l'ouvrir :

- **Sur Mac** : appuyez sur `Cmd (⌘) + Espace`, tapez `Terminal`, appuyez sur Entrée.
- **Sur Windows** : dans le menu Démarrer, tapez `PowerShell`, cliquez dessus.
- **Sur Linux** : cherchez « Terminal » dans vos applications.

Une fenêtre noire (ou blanche) s'ouvre avec du texte et un curseur qui clignote.
C'est normal. On l'utilisera à la Partie 6.

### L'environnement WordPress

Pour que la machine fonctionne, il faut un WordPress installé avec le thème et le
plugin MAJI. **Cette installation se fait une seule fois.** Elle est décrite dans
un autre document, `INSTALLATION.md`. Si c'est un collègue qui gère la partie
technique, demandez-lui simplement : « Est-ce que l'environnement MAJI est prêt et
où dois-je mettre les fichiers des clients ? »

> Le reste de ce guide suppose que cet environnement existe déjà et que vous savez
> où sont rangés les fichiers du projet MAJI sur l'ordinateur.

---

## Partie 3 — Comprendre un fichier `.json` sans stress

Un fichier `.json` est un **fichier texte structuré**. Il sert à ranger des
informations de façon que la machine les comprenne. Voici tout ce qu'il faut savoir.

Exemple minuscule :

```json
{
  "nom": "Chez Fatou",
  "ville": "Cotonou",
  "ouvert": true
}
```

**Les 5 règles à ne jamais oublier :**

1. Tout est entouré d'**accolades** `{ }` (le grand contenant).
2. Chaque information est une **paire** : `"étiquette": valeur`.
3. Le texte est **toujours entre guillemets droits** : `"Cotonou"` ✅ — pas `'Cotonou'` ni `«Cotonou»`.
4. On sépare les paires par une **virgule** `,` … **mais pas après la dernière** d'un groupe.
5. Les nombres (`2016`) et les vrai/faux (`true`, `false`) s'écrivent **sans guillemets**.

**Les erreurs classiques du débutant :**

| Erreur | Mauvais | Bon |
|---|---|---|
| Virgule en trop à la fin | `"ville": "Cotonou",` (juste avant `}`) | `"ville": "Cotonou"` |
| Guillemets « courbes » | `"nom": "Chez Fatou"` (avec ” ”) | `"nom": "Chez Fatou"` (droits) |
| Virgule oubliée | `"nom": "X" "ville": "Y"` | `"nom": "X", "ville": "Y"` |

**Bonne nouvelle** : si vous ouvrez le fichier dans VS Code (Outil 1) et qu'une
règle est cassée, une **ligne ondulée rouge** apparaît à l'endroit du problème.
La machine vous préviendra aussi clairement au moment de lancer la commande.

> Astuce : ne partez jamais d'une page blanche. **Copiez toujours un fichier
> d'exemple existant** et modifiez seulement les valeurs. C'est ce qu'on fait
> ci-dessous.

---

## Partie 4 — La méthode : copier un exemple et le modifier

Dans le projet MAJI, il y a déjà des fichiers d'exemple tout prêts, dans le
dossier `dna/examples/` :

- `hotel-atlantique.json` → si votre client est un **hôtel**
- `saveurs-du-benin.json` → si votre client est un **restaurant**

Votre méthode pour chaque nouveau client :

1. **Copier** l'exemple qui correspond au type du client.
2. **Renommer** la copie avec le nom du client.
3. **Modifier** les valeurs à l'intérieur.

On va faire exactement ça, ensemble, avec un restaurant fictif : **« Chez Fatou »,
un maquis à Cotonou**.

---

## Partie 5 — Créer le site « Chez Fatou » (exemple complet)

### Étape 5.1 — Créer le dossier du client

Dans le projet MAJI, créez un dossier pour votre client. Créez un dossier
`clients/`, et dedans un dossier `chez-fatou/`. Vous pouvez le faire à la souris,
dans votre explorateur de fichiers habituel (Finder sur Mac, Explorateur sur
Windows). Au final vous devez avoir :

```
clients/
└── chez-fatou/
```

> **Règle de nommage** : utilisez uniquement des minuscules, des chiffres et des
> tirets. Pas d'espaces, pas d'accents. `chez-fatou` ✅ — `Chez Fatou` ❌.

### Étape 5.2 — Copier la fiche d'identité d'exemple

Copiez le fichier `dna/examples/saveurs-du-benin.json` (c'est un restaurant, comme
notre exemple) dans votre nouveau dossier, et renommez-le `dna.json`. Résultat :

```
clients/
└── chez-fatou/
    └── dna.json
```

### Étape 5.3 — Ouvrir et modifier la fiche d'identité

Ouvrez `clients/chez-fatou/dna.json` dans VS Code. Vous voyez beaucoup de texte
organisé en sections. **On va les parcourir une par une.** Pour chaque section,
je vous montre : ce que ça veut dire, et ce que vous devez changer.

---

#### Section « meta » — la carte d'identité administrative

```json
"meta": {
  "site_slug": "chez-fatou",
  "client": "Chez Fatou",
  "created_at": "2026-07-22"
}
```

| Étiquette | Ce que ça veut dire | Ce que vous mettez |
|---|---|---|
| `site_slug` | Le nom-code unique du site (minuscules-tirets) | `chez-fatou` |
| `client` | Le vrai nom, tel qu'écrit sur l'enseigne | `Chez Fatou` |
| `created_at` | La date du jour, format année-mois-jour | `2026-07-22` |

> ⚠️ Une fois le site livré, **ne changez plus jamais** `site_slug`. C'est son
> numéro d'immatriculation.

---

#### Section « identity » — les informations du commerce

C'est la partie la plus longue mais la plus simple : ce sont juste les
coordonnées du client. Remplissez avec ses vraies infos.

```json
"identity": {
  "name": "Chez Fatou",
  "type": "restaurant",
  "phone": "+22921000000",
  "whatsapp": "+22997000000",
  "email": "bonjour@chezfatou.bj",
  "address": {
    "street": "Rue 12",
    "district": "Akpakpa",
    "city": "Cotonou",
    "country": "BJ"
  },
  "hours": {
    "mon": [],
    "tue": [["11:00","15:00"],["18:30","22:30"]],
    "wed": [["11:00","15:00"],["18:30","22:30"]],
    "thu": [["11:00","15:00"],["18:30","22:30"]],
    "fri": [["11:00","15:00"],["18:30","23:00"]],
    "sat": [["11:00","23:00"]],
    "sun": [["11:00","16:00"]]
  },
  "currency": "XOF",
  "locale": "fr_FR",
  "socials": { "facebook": "", "instagram": "https://instagram.com/chezfatou", "tiktok": "" },
  "features": { "ordering": true, "table_booking": true, "delivery": true, "pickup": true },
  "delivery_zones": [
    { "name": "Akpakpa", "fee": 500 },
    { "name": "Cadjehoun", "fee": 1000 }
  ]
}
```

Le détail de chaque champ :

- **`name`** : le nom du commerce.
- **`type`** : écrivez `hotel`, `restaurant`, ou `mixte` (si c'est les deux).
- **`phone`** et **`whatsapp`** : le numéro **doit** commencer par `+` suivi de
  l'indicatif pays (Bénin = 229, Côte d'Ivoire = 225…), **sans espaces**.
  Exemple : `+22997000000`. Si vous oubliez le `+`, la machine refusera (elle vous
  le dira clairement).
- **`address`** : rue, quartier (`district`), ville, et `country` = code pays à
  2 lettres majuscules (`BJ` = Bénin, `CI` = Côte d'Ivoire, `SN` = Sénégal).
- **`hours`** : les horaires. C'est le plus délicat, lisez bien l'encadré ci-dessous.
- **`currency`** : la monnaie. Laissez `XOF` (le franc CFA).
- **`socials`** : les liens réseaux sociaux. Laissez `""` (vide) si le client n'en a pas.
- **`features`** : ce que le site propose. `true` = activé, `false` = désactivé.
  Pour un restaurant qui livre et prend les commandes en ligne, laissez tout à `true`.
- **`delivery_zones`** : les quartiers livrés et leur prix de livraison en francs CFA.

> **Comment écrire les horaires**
>
> Chaque jour a une liste de créneaux. Un créneau = `["ouverture","fermeture"]`.
> - Fermé toute la journée : `"mon": []` (crochets vides).
> - Ouvert en continu : `"sat": [["11:00","23:00"]]` (un seul créneau).
> - Avec une coupure midi/soir : `"tue": [["11:00","15:00"],["18:30","22:30"]]` (deux créneaux).
>
> Les jours : `mon` lundi, `tue` mardi, `wed` mercredi, `thu` jeudi, `fri`
> vendredi, `sat` samedi, `sun` dimanche.
>
> Ces horaires servent à deux choses : les afficher sur le site, ET **bloquer
> automatiquement les commandes en dehors des heures d'ouverture**.

---

#### Section « integrations » — les notifications WhatsApp (optionnel)

```json
"integrations": {
  "n8n_order_url": "",
  "n8n_reservation_url": ""
}
```

Laissez ces deux valeurs **vides** (`""`) pour l'instant. C'est le branchement
des notifications WhatsApp automatiques : il se configure plus tard (c'est
expliqué dans `OPERATIONS.md`). Le site fonctionne parfaitement sans.

---

#### Section « design » — l'apparence (le cœur de l'unicité)

C'est ici que vous choisissez à quoi ressemble le site. Vous avez **4 choix à faire**.

```json
"design": {
  "da": "da-artisanal-texture",
  "font_pair": "fp-05",
  "palette": {
    "primary": "#8A3B12",
    "accent": "#3F5E3A"
  },
  "radius_scale": "sm",
  "spacing_mood": "normal",
  "image_treatment": {
    "hero_ratio": "4:3",
    "card_ratio": "1:1",
    "overlay": "primary",
    "duotone": false
  }
}
```

**Choix 1 — `da` : l'ambiance visuelle** (choisissez UNE valeur dans la liste) :

| Écrivez… | Style obtenu | Idéal pour |
|---|---|---|
| `da-editorial-sombre` | Fond sombre, chic, touches dorées | Hôtel haut de gamme, restaurant gastronomique |
| `da-solaire-minimal` | Fond clair, épuré, moderne, coloré | Restaurant moderne, hôtel d'affaires |
| `da-artisanal-texture` | Tons terre, chaleureux, authentique | Maquis, cuisine locale, maison d'hôtes |

**Choix 2 — `font_pair` : le duo de polices** (choisissez UNE valeur) :

| Écrivez… | Ambiance de l'écriture |
|---|---|
| `fp-01` | Élégant et contemporain |
| `fp-02` | Classique et chic |
| `fp-03` | Moderne et géométrique |
| `fp-04` | Raffiné et artisanal |
| `fp-05` | Chaleureux et facile à lire |
| `fp-06` | Affirmé et technique |

**Choix 3 — `palette` : les 2 couleurs principales.** Mettez le code couleur de
la couleur principale du client (`primary`) et d'une couleur secondaire (`accent`).
Un code couleur commence par `#` suivi de 6 caractères (ex. `#8A3B12` = un brun
terre cuite). Pour trouver le code d'une couleur, utilisez https://www.google.com/search?q=color+picker
(tapez « color picker » dans Google, un sélecteur apparaît).

> ⚠️ **Attention au contraste (très important)** : le texte doit rester lisible
> sur son fond. La machine **vérifie automatiquement** que vos couleurs sont assez
> contrastées, et **refuse** le fichier sinon, avec un message du genre :
> `design.palette : contraste accent-contrast/accent insuffisant (3.48:1, minimum
> 4.5:1)`. Si ça arrive, choisissez une couleur `accent` plus foncée (pour du
> texte blanc dessus) ou plus claire, et réessayez. En cas de doute, vérifiez sur
> https://webaim.org/resources/contrastchecker/ : le ratio doit être **≥ 4.5**.

**Choix 4 — les réglages fins** (vous pouvez garder les valeurs de l'exemple) :

- `radius_scale` : arrondi des coins. `sm` = coins nets, `md` = doux, `lg` = très arrondi.
- `spacing_mood` : aération. `compact` = dense, `normal` = équilibré, `aere` = très aéré.
- `image_treatment` : format des photos. Gardez les valeurs de l'exemple si vous ne savez pas.

---

#### Section « structure » — les pages et ce qu'il y a dedans

```json
"structure": {
  "header": "header-02",
  "footer": "footer-03",
  "nav_vocabulary": "resto-convivial",
  "pages": [
    {
      "slug": "accueil",
      "title": "Accueil",
      "sections": [
        "maji/resto-hero-02",
        "maji/resto-populaires-01",
        "maji/resto-menu-02",
        "maji/resto-livraison-01",
        "maji/commun-avis-01",
        "maji/commun-cta-01"
      ]
    },
    {
      "slug": "menu",
      "title": "La Carte",
      "sections": [ "maji/resto-menu-03", "maji/commun-cta-01" ]
    },
    {
      "slug": "contact",
      "title": "Venir nous voir",
      "sections": [ "maji/commun-localisation-01", "maji/commun-faq-01", "maji/commun-cta-01" ]
    }
  ]
}
```

- **`header`** : le bandeau du haut. `header-01` transparent, `header-02` solide,
  `header-03` logo centré, `header-04` séparé gauche/droite.
- **`footer`** : le bas de page. `footer-01` complet, `footer-02` minimal,
  `footer-03` éditorial.
- **`nav_vocabulary`** : le style des noms de menu. Pour un restaurant :
  `resto-classique` (« Menu », « Commander ») ou `resto-convivial` (« La Carte »,
  « Se régaler »). Pour un hôtel : `hotel-classique` ou `hotel-experientiel`.
- **`pages`** : la liste des pages. Chaque page a :
  - `slug` : son nom-code (`accueil`, `menu`, `contact`). **Gardez `accueil`** pour
    la page d'accueil, c'est un mot magique reconnu par la machine.
  - `title` : le titre affiché.
  - `sections` : la liste des **blocs** empilés sur la page, **de haut en bas**.

**Les sections, c'est quoi ?** Ce sont des morceaux de page tout prêts : un
bandeau d'accueil, une grille de plats, des avis clients, etc. Vous les
choisissez et les rangez dans l'ordre voulu. La liste complète des sections
disponibles (avec une description de chacune) est dans le fichier
**`docs/SECTIONS.md`**. Piochez dedans.

> Pour un premier site, **gardez les sections de l'exemple**. Elles forment déjà
> une page cohérente. Vous personnaliserez l'ordre plus tard, quand vous serez à
> l'aise.

---

#### Section « content » — les textes du site

```json
"content": {
  "tone": "local",
  "facts": {
    "quartier": "Akpakpa",
    "annee": 2018,
    "specialites": ["poisson braisé", "riz au gras"]
  },
  "texts": {
    "content.texts.hero_tagline": "Le maquis chaleureux d'Akpakpa, braisé au feu de bois.",
    "content.texts.menu_intro": "Des recettes de famille, des produits frais du marché.",
    "content.texts.cta_titre": "Un petit creux ?",
    "content.texts.avis_1_texte": "Le meilleur poisson braisé du quartier !",
    "content.texts.avis_1_auteur": "Koffi A., Cotonou",
    "content.texts.avis_2_texte": "Ambiance conviviale et service rapide.",
    "content.texts.avis_2_auteur": "Awa D., Akpakpa",
    "content.texts.avis_3_texte": "On y revient chaque week-end en famille.",
    "content.texts.avis_3_auteur": "Marc T., Calavi",
    "content.texts.faq_1_question": "Livrez-vous à domicile ?",
    "content.texts.faq_1_reponse": "Oui, à Akpakpa et Cadjehoun. Écrivez-nous sur WhatsApp.",
    "content.texts.faq_2_question": "Comment régler ?",
    "content.texts.faq_2_reponse": "Le paiement se fait à la livraison, en espèces ou Mobile Money.",
    "content.texts.faq_3_question": "Peut-on réserver une table ?",
    "content.texts.faq_3_reponse": "Bien sûr, en ligne ou sur WhatsApp."
  },
  "seed": "clients/chez-fatou"
}
```

- **`tone`** : l'esprit des textes (`premium`, `familial`, `local`, `minimal`) —
  indicatif, ça aide juste à garder une cohérence de rédaction.
- **`facts`** : quelques faits sur l'établissement, réutilisés dans les textes.
- **`texts`** : **c'est ici que vous écrivez les vrais textes du client.** Chaque
  ligne remplace un « trou » dans les sections. Par exemple,
  `content.texts.hero_tagline` est la phrase d'accroche affichée sur le grand
  bandeau d'accueil.
- **`seed`** : le chemin vers le dossier de contenus du client. Mettez le chemin
  de votre dossier : `clients/chez-fatou`.

> **Comment savoir quels textes remplir ?** Chaque section utilise certains
> « trous ». Le fichier `docs/SECTIONS.md` liste, pour chaque section, les textes
> attendus. Bonne nouvelle : si vous en oubliez un, la machine vous le dira
> **précisément** au moment de fabriquer le site (voir Partie 7). Vous n'avez donc
> pas besoin d'être parfait du premier coup.

Enregistrez le fichier (`Cmd/Ctrl + S`). Votre fiche d'identité est prête.

---

### Étape 5.4 — Préparer le dossier de contenus

Maintenant, les photos, les plats et les descriptions détaillées. Créez un fichier
`content.json` dans le dossier du client, et un sous-dossier `media/` pour les
photos :

```
clients/
└── chez-fatou/
    ├── dna.json          ← déjà fait
    ├── content.json      ← à créer maintenant
    └── media/            ← à créer, on y met les photos
```

**Le plus simple** : copiez le fichier `demo-content/restaurant-africain/content.json`
comme point de départ, puis adaptez-le.

Voici à quoi il ressemble (version raccourcie) :

```json
{
  "media": {
    "hero":  { "file": "hero.webp",  "alt": "Poisson braisé de Chez Fatou" },
    "plat-1":{ "file": "plat-1.webp","alt": "Poisson braisé entier" },
    "plat-2":{ "file": "plat-2.webp","alt": "Riz au gras" },
    "map":   { "file": "map.webp",   "alt": "Plan d'accès à Chez Fatou" }
  },
  "rooms": [],
  "dishes": [
    {
      "name": "Poisson braisé",
      "description": "Dorade entière braisée au feu de bois, sauce tomate maison.",
      "price": "5000",
      "category": "Plats",
      "badges": ["populaire"],
      "available": true,
      "image": "plat-1"
    },
    {
      "name": "Riz au gras",
      "description": "Riz mijoté, viande tendre, légumes du marché.",
      "price": "2500",
      "category": "Plats",
      "badges": [],
      "available": true,
      "image": "plat-2"
    }
  ]
}
```

Ce que contient ce fichier :

- **`media`** : la liste des photos. Chaque photo a :
  - une **étiquette** (`hero`, `plat-1`, `map`…) — c'est le nom que les sections
    reconnaissent ;
  - `file` : le nom du fichier photo (dans le dossier `media/`) ;
  - `alt` : une **description courte de la photo** (pour les malvoyants et Google).
    **Elle est obligatoire** — si vous l'oubliez, la machine refuse la photo avec
    le message `media.X : attribut alt requis`.
- **`rooms`** : les chambres (pour un hôtel). Vide `[]` pour un restaurant.
- **`dishes`** : les plats (pour un restaurant). Chaque plat a un nom, une
  description, un prix (en CFA, sans espace), une catégorie (Entrées, Plats,
  Desserts, Boissons), des badges éventuels (`populaire`, `epice`, `nouveau`), s'il
  est disponible, et quelle photo lui associer (l'étiquette d'un média).

> **Quelles étiquettes de photos utiliser ?** Les sections attendent des noms
> précis : `hero` (grande photo d'accueil), `plat-1`, `plat-2`, `plat-3`,
> `gallery-1` à `gallery-4` (galerie), `map` (plan d'accès), et pour un hôtel
> `chambre-1`, `chambre-2`, `service-1` à `service-3`. La liste exacte par section
> est dans `docs/SECTIONS.md`, colonne « Médias ».

### Étape 5.5 — Déposer les photos

Mettez les vraies photos du client dans le dossier `media/`, en les nommant
**exactement** comme indiqué dans `content.json` (`hero.webp`, `plat-1.webp`…).

Conseils photos :

- Format **WebP** de préférence (plus léger), sinon JPEG. Pour convertir une photo
  en WebP gratuitement : https://squoosh.app/
- **Poids maximum ~200 Ko par photo** (sinon le site devient lent sur mobile).
- La photo d'accueil (`hero`) : format paysage, sujet fort, un peu sombre supporte
  mieux le texte blanc par-dessus.
- Vérifiez que le client vous a **autorisé** à utiliser ses photos.

Enregistrez tout. Vos deux « capsules » sont prêtes. Passons au bouton.

---

## Partie 6 — Fabriquer le site (le fameux terminal)

On y est. Ouvrez le **terminal** (revoir la Partie 2 si besoin).

### Étape 6.1 — Aller dans le dossier du projet

Dans le terminal, il faut d'abord « se placer » dans le dossier du projet MAJI.
On utilise la commande `cd` (pour *change directory*, « changer de dossier »).

Tapez `cd `, puis **glissez-déposez le dossier du projet MAJI** depuis votre
explorateur directement dans la fenêtre du terminal : son chemin s'écrit tout
seul. Appuyez sur Entrée. Par exemple, ça ressemblera à :

```bash
cd /Users/vous/projets/framework_wp_maji
```

> Astuce : la commande `ls` (puis Entrée) affiche la liste des dossiers là où vous
> êtes. Si vous voyez `dna`, `demo-content`, `plugins`, `themes`… vous êtes au bon
> endroit.

### Étape 6.2 — Lancer la fabrication

Tapez **cette seule commande** (adaptez le chemin vers votre `dna.json`) et
appuyez sur Entrée :

```bash
wp maji provision --dna=clients/chez-fatou/dna.json --with-woo
```

Décodons-la, pour ne pas taper « au hasard » :

- `wp maji provision` = « fabrique le site ».
- `--dna=clients/chez-fatou/dna.json` = « voici la fiche d'identité à utiliser ».
- `--with-woo` = « installe la boutique en ligne » (à mettre **uniquement pour un
  restaurant** qui prend des commandes ; inutile pour un hôtel).

C'est tout. La machine travaille et affiche sa progression en 5 étapes :

```
1/5 Validation de l'ADN…
2/5 Contrôle du registre anti-clones…
3/5 WooCommerce…
4/5 Application de l'ADN…
5/5 Import des contenus…
Importé : 4 média(s), 0 chambre(s), 2 plat(s).
Success: Site provisionné.
```

Quand vous voyez **`Success: Site provisionné`** en vert, c'est gagné : le site
est construit. Ouvrez son adresse dans un navigateur pour l'admirer.

> Vous pouvez relancer cette commande autant de fois que vous voulez (après avoir
> corrigé un texte, ajouté un plat…). Elle **met à jour** le site sans rien casser.

---

## Partie 7 — Quand la machine affiche une erreur (c'est normal)

La machine est **exigeante mais bavarde** : quand quelque chose ne va pas, elle
**s'arrête et vous explique précisément** le problème, en français. Ce n'est pas
une panne : c'est un garde-fou. Voici les messages les plus courants et quoi faire.

| Message affiché | Ce que ça veut dire | Ce que vous faites |
|---|---|---|
| `JSON invalide : …` | Une faute de frappe dans un fichier (virgule, guillemet…) | Ouvrez le fichier dans VS Code, cherchez la **ligne ondulée rouge**, corrigez |
| `identity.phone : « … » n'est pas au format E.164` | Le numéro n'a pas le bon format | Écrivez-le avec `+` et l'indicatif, sans espaces : `+22997000000` |
| `design.da : « … » inconnue` | Vous avez mal orthographié l'ambiance visuelle | Recopiez exactement une valeur de la liste (Choix 1) |
| `design.palette : contraste … insuffisant (3.48:1…)` | Vos couleurs ne sont pas assez lisibles | Choisissez une couleur plus foncée/claire (voir Choix 3) |
| `media.hero : attribut alt requis` | Une photo n'a pas de description | Ajoutez le `"alt": "…"` à cette photo dans `content.json` |
| `Jetons non résolus sur la page « accueil » : {{maji:content.texts.hero_tagline}}` | Un texte attendu par une section n'a pas été rempli | Ajoutez la ligne correspondante dans la section `texts` de `dna.json` |
| `Similarité 0.75 ≥ 0.70 avec « … »` | Le site ressemble trop à un autre déjà livré | Voir l'encadré ci-dessous |

Après correction, **relancez simplement la même commande**. Répétez jusqu'au
`Success`. C'est un aller-retour normal, même les pros font plusieurs essais.

> **Le garde-fou anti-copie**
>
> Pour éviter que vos 30 sites se ressemblent, la machine compare chaque nouveau
> site aux précédents. S'ils sont trop proches, elle bloque et vous suggère quoi
> changer :
>
> ```
> Site le plus proche : saveurs-du-benin (0.75).
> Axes en collision : da, font_pair, hero.
>   → changez la direction artistique (design.da)
>   → changez la paire typographique (design.font_pair)
>   → changez le hero de l'accueil (première section)
> ```
>
> Il suffit en général de **changer une ou deux choses** (par exemple l'ambiance
> `da` et la première section de l'accueil), puis de relancer. Le site redevient
> unique.

---

## Partie 8 — Vérifier le site avant de le livrer

Ouvrez le site dans un navigateur et cochez cette liste :

- [ ] La page d'accueil s'affiche avec la bonne grande photo.
- [ ] **Aucun texte bizarre** entre doubles accolades (comme `{{maji:...}}`) n'est visible.
- [ ] Les photos du client sont bien là (pas les images grises de démo).
- [ ] Testez sur un **téléphone** : tout doit être lisible et bien rangé.
- [ ] Le numéro WhatsApp et les horaires sont corrects.
- [ ] (Restaurant) Faites une commande test : ajoutez un plat, choisissez livraison
      + un quartier, allez jusqu'au paiement à la livraison.
- [ ] (Hôtel) Envoyez une demande de réservation test.

Pour former le client à gérer son site lui-même (changer un prix, une photo,
traiter les réservations), remettez-lui le **`docs/GUIDE-GERANT.md`** : il est
écrit pour lui, en langage simple.

---

## Partie 9 — Enregistrer le site (dernière étape)

Une fois le site validé et livré, il faut « déclarer » son apparence pour que le
garde-fou anti-copie en tienne compte pour les prochains clients. Une commande :

```bash
wp maji dna register clients/chez-fatou/dna.json --registry=registry/registry.json
```

Message attendu : `Success: Empreinte de « chez-fatou » enregistrée`. C'est fini.

> Si votre équipe utilise le partage de fichiers (git), pensez à **envoyer** ce
> changement pour que vos collègues aient le registre à jour. Si vous ne savez pas
> faire, demandez à la personne technique de l'équipe — c'est une opération rapide.

---

## Récapitulatif express (à garder sous les yeux)

1. **Créer** un dossier `clients/<nom-du-client>/`.
2. **Copier** un exemple de `dna/examples/`, le renommer `dna.json`, le **modifier**.
3. **Copier** un `content.json` d'exemple, le modifier, **déposer les photos** dans `media/`.
4. Ouvrir le **terminal**, se placer dans le projet (`cd …`).
5. Lancer : `wp maji provision --dna=clients/<nom>/dna.json --with-woo`
6. **Lire les erreurs**, corriger, **relancer** jusqu'au `Success`.
7. **Vérifier** le site (surtout sur mobile).
8. **Enregistrer** : `wp maji dna register …`.

---

## Petit glossaire

| Mot | Traduction en clair |
|---|---|
| **Terminal** / ligne de commande / console | La fenêtre où l'on tape des ordres au clavier |
| **Commande** | Une phrase qu'on tape dans le terminal pour demander une action |
| **`.json`** | Un fichier texte structuré (règles : accolades, guillemets, virgules) |
| **ADN** / `dna.json` | La fiche d'identité du site (couleurs, nom, pages…) |
| **Seed** / dossier de contenus | Le dossier avec les photos, plats/chambres et textes |
| **Provision** | L'action de fabriquer le site à partir de la fiche + des contenus |
| **DA** (direction artistique) | L'ambiance visuelle globale (sombre, clair, terre…) |
| **Section** | Un morceau de page tout prêt (bandeau, grille de plats, avis…) |
| **Jeton** (`{{maji:…}}`) | Un « trou » dans une section, rempli par vos textes |
| **Registre anti-clones** | La liste des sites déjà faits, pour éviter qu'ils se ressemblent |
| **`cd`** | Commande pour « entrer » dans un dossier |
| **`ls`** | Commande pour « lister » ce qu'il y a dans le dossier courant |
| **WebP** | Un format d'image léger, idéal pour le web |
| **WooCommerce** | Le module de boutique/commande en ligne (pour les restaurants) |
