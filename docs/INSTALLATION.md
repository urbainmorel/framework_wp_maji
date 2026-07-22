# Installation & mise en place de l'environnement

Ce document couvre **deux choses** :

1. **Monter l'environnement WordPress** qui fait tourner le thème + le plugin MAJI
   (à faire une fois par machine/serveur) — sections 1 à 5.
2. **Produire un site client** dessus (`wp maji provision`) — section 6.

Vocabulaire : on appelle **« usine »** votre copie du dépôt `framework_wp_maji`
(le thème, le plugin, les exemples, le registre, les scripts) et **« produit »**
l'installation WordPress d'un client. L'usine fabrique le produit.

---

## 1. Prérequis communs (toutes les cibles)

| Composant | Version | Vérifier avec |
|---|---|---|
| PHP | ≥ 8.1 (dév en 8.2) | `php -v` |
| MySQL / MariaDB | 5.7+ / 10.4+ | `mysql --version` |
| Composer 2 | dernière | `composer --version` |
| Node.js | 20 LTS + npm | `node -v && npm -v` |
| WP-CLI 2.x | dernière | `wp --version` |
| Git | — | `git --version` |

Extensions PHP requises : `mysqli` (ou `pdo_mysql`), `gd` **ou** `imagick`
(traitement des images à l'import), `curl`, `mbstring`, `zip`, `dom`, `json`.

Récupérer l'usine sur la machine :

```bash
git clone https://github.com/urbainmorel/framework_wp_maji.git
cd framework_wp_maji
composer install          # dépendances PHP (lint, tests, stubs)
npm install               # dépendances Node (wp-env, wp-scripts)
```

---

## 2. Cible A — Environnement de développement local (Docker / wp-env)

C'est la voie **recommandée pour développer et pour produire les 4 sites modèles**.
`wp-env` est fourni : il monte un WordPress complet en `fr_FR` avec le thème, le
plugin et WooCommerce, sans rien installer d'autre que Docker.

### 2.1 Installer Docker

- **Mac/Windows** : Docker Desktop → https://www.docker.com/products/docker-desktop/
- **Linux** : `sudo apt install docker.io docker-compose-plugin` puis
  `sudo usermod -aG docker $USER` (déconnexion/reconnexion) et
  `sudo systemctl enable --now docker`.

Vérifier : `docker info` doit répondre sans erreur.

### 2.2 Démarrer l'environnement

```bash
npm run start        # équivaut à : npx wp-env start
```

Au premier lancement, Docker télécharge les images (quelques minutes). À la fin,
l'URL et les identifiants s'affichent :

```
WordPress development site started at http://localhost:8888
Administrator username: admin
Administrator password: password
```

- Front : http://localhost:8888
- Admin : http://localhost:8888/wp-admin (`admin` / `password`)

La configuration (`.wp-env.json`) charge déjà : WordPress dernière stable en
`fr_FR`, le thème `maji-framework`, le plugin `maji-core` et WooCommerce.

### 2.3 Lancer une commande WP-CLI dans l'environnement

Toutes les commandes `wp …` passent par `wp-env run cli` :

```bash
npx wp-env run cli -- wp theme activate maji-framework
npx wp-env run cli -- wp plugin activate maji-core
```

⚠️ **Chemins** : à l'intérieur du conteneur, le dépôt est monté sous
`/var/www/html/wp-content/plugins/maji-core`. Pour référencer un fichier ADN du
dépôt, le plus simple est de le copier dans le dossier du plugin (mappé) ou
d'utiliser le chemin conteneur. Exemple avec un des sites modèles :

```bash
npx wp-env run cli -- wp maji provision \
  --dna=wp-content/plugins/maji-core/../../../demo-content/hotel-business/dna.json \
  --skip-registry
```

Astuce plus robuste : mappez un dossier de travail dans `.wp-env.json`
(`"mappings": { "wp-content/maji": "./" }`) puis référencez
`wp-content/maji/demo-content/hotel-business/dna.json`.

### 2.4 Arrêter / réinitialiser

```bash
npm run stop                 # arrêter
npx wp-env clean all         # réinitialiser la base
npx wp-env destroy           # tout supprimer
```

---

## 3. Cible B — Local « classique » (XAMPP, MAMP, Local, Laragon)

Si vous préférez un stack local sans Docker.

1. Installez XAMPP/MAMP/Laragon (ou **Local by Flywheel**, le plus simple) et
   créez un site WordPress vide **en français** (`fr_FR`).
2. Assurez-vous que **WP-CLI** est disponible dans le terminal (Local fournit un
   « Open site shell » qui l'inclut ; sinon installez WP-CLI globalement).
3. Copiez le thème et le plugin depuis l'usine dans le WordPress :
   ```bash
   cp -R /chemin/usine/themes/maji-framework  /chemin/wp/wp-content/themes/
   cp -R /chemin/usine/plugins/maji-core       /chemin/wp/wp-content/plugins/
   ( cd /chemin/wp/wp-content/plugins/maji-core && composer install --no-dev )
   ```
4. Activez :
   ```bash
   cd /chemin/wp
   wp theme activate maji-framework
   wp plugin activate maji-core
   wp plugin install woocommerce --activate     # si mode restaurant
   ```
5. Provisionnez (section 6). Gardez l'usine à côté pour référencer les ADN et le
   registre : `wp maji provision --dna=/chemin/usine/demo-content/…/dna.json`.

---

## 4. Cible C — VPS (Ubuntu 22.04, stack LEMP)

Pour un serveur que vous administrez (root/sudo). Exemple Nginx + PHP-FPM + MariaDB.

### 4.1 Installer le stack

```bash
sudo apt update
sudo apt install -y nginx mariadb-server \
  php8.2-fpm php8.2-mysql php8.2-gd php8.2-curl php8.2-mbstring \
  php8.2-xml php8.2-zip php8.2-intl unzip curl git

# Sécuriser MariaDB
sudo mysql_secure_installation

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# WP-CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar && sudo mv wp-cli.phar /usr/local/bin/wp
```

### 4.2 Base de données

```bash
sudo mysql -e "CREATE DATABASE maji CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'maji'@'localhost' IDENTIFIED BY 'MOT_DE_PASSE_FORT';"
sudo mysql -e "GRANT ALL ON maji.* TO 'maji'@'localhost'; FLUSH PRIVILEGES;"
```

### 4.3 WordPress + MAJI

```bash
sudo mkdir -p /var/www/client && sudo chown -R $USER:www-data /var/www/client
cd /var/www/client

wp core download --locale=fr_FR
wp config create --dbname=maji --dbuser=maji --dbpass='MOT_DE_PASSE_FORT' --locale=fr_FR
wp core install --url=https://client.example --title="Site" \
  --admin_user=maji --admin_password='ADMIN_FORT' --admin_email=tech@maji.digital

# Récupérer l'usine puis copier thème + plugin
git clone https://github.com/urbainmorel/framework_wp_maji.git ~/usine
cp -R ~/usine/themes/maji-framework wp-content/themes/
cp -R ~/usine/plugins/maji-core wp-content/plugins/
( cd wp-content/plugins/maji-core && composer install --no-dev )
wp theme activate maji-framework
wp plugin activate maji-core
wp plugin install woocommerce --activate       # si restaurant
```

Configurez ensuite le vhost Nginx (racine `/var/www/client`, `try_files … index.php`,
passe `.php` au socket `php8.2-fpm`), un certificat Let's Encrypt (`certbot`), puis
provisionnez (section 6). Le script `scripts/install.sh` de l'usine automatise les
étapes WP + provision (voir 6.3).

---

## 5. Cible D — Hébergement mutualisé (cPanel, N0C/o2switch, Plesk)

Deux cas selon que vous ayez **SSH** ou non.

### 5.1 Avec accès SSH (recommandé — la plupart des mutualisés modernes)

1. **Base de données** : dans cPanel → *MySQL Databases* (ou N0C → *Bases de
   données*), créez une base + un utilisateur, notez les identifiants.
2. **Version PHP** : cPanel → *MultiPHP Manager* / N0C → *Sélecteur PHP* → passez
   le domaine en **PHP 8.1 ou 8.2**, activez les extensions `gd`, `mbstring`,
   `intl`, `zip`, `curl`.
3. Connectez-vous en SSH (`ssh user@serveur -p PORT`). WP-CLI est souvent déjà
   présent (`wp --version`) ; sinon téléchargez `wp-cli.phar` dans votre home et
   utilisez `php wp-cli.phar …`.
4. Placez-vous dans le dossier du domaine (souvent `~/public_html` ou
   `~/sites/client.example`) et installez :
   ```bash
   cd ~/public_html
   wp core download --locale=fr_FR
   wp config create --dbname=NOM --dbuser=USER --dbpass='PASS' --locale=fr_FR
   wp core install --url=https://client.example --title="Site" \
     --admin_user=maji --admin_password='ADMIN_FORT' --admin_email=tech@maji.digital
   ```
5. **Thème + plugin** : si Composer est indisponible sur le mutualisé, buildez les
   zips **sur votre machine** (section 5.3) et téléversez-les :
   ```bash
   # en local, sur l'usine :
   bash scripts/release.sh          # crée dist/maji-framework.zip et dist/maji-core.zip
   ```
   Puis, via SFTP ou l'upload de cPanel, déposez les deux zips et, en SSH :
   ```bash
   wp theme install ~/maji-framework.zip --activate
   wp plugin install ~/maji-core.zip --activate
   wp plugin install woocommerce --activate      # si restaurant
   ```
6. Téléversez le **dossier du client** (`clients/<slug>/` avec `dna.json`,
   `content.json`, `media/`) dans votre home, puis provisionnez (section 6). Le
   registre `registry/registry.json` doit être accessible : téléversez aussi le
   dossier `registry/` et `dna/` de l'usine, ou passez `--skip-registry` et gérez
   l'unicité en amont sur votre poste.

### 5.2 Sans SSH (mutualisé d'entrée de gamme)

Le mutualisé sans SSH ne permet pas `wp maji provision` à distance. Deux options :

- **Option recommandée — produire en local, puis migrer.** Fabriquez le site
  complet en local (Cible A ou B), puis migrez-le vers le mutualisé avec un outil
  de migration (All-in-One WP Migration, Duplicator, ou export/import DB + `wp
  search-replace` de l'URL). La provision (JSON, CLI) reste entièrement sur votre
  poste ; l'hébergement ne reçoit qu'un site fini.
- **Option manuelle.** Installez WordPress via l'installeur en 1 clic de l'hébergeur,
  téléversez les zips thème/plugin depuis l'admin (*Apparence → Thèmes → Ajouter*,
  *Extensions → Ajouter*), puis recréez les réglages/pages à la main — long et
  déconseillé (on perd le bénéfice de l'usine).

### 5.3 Construire les zips de release

Sur l'usine (machine avec Composer) :

```bash
bash scripts/release.sh
# → dist/maji-framework.zip  (thème)
# → dist/maji-core.zip       (plugin, vendor de prod inclus : autoload + updates)
```

Ces zips sont aussi ceux publiés automatiquement par la CI GitHub à chaque tag
`vX.Y.Z` (voir `docs/OPERATIONS.md` pour les mises à jour de flotte).

---

## 6. Produire un site sur l'environnement

Une fois l'environnement prêt (sections 2 à 5), la fabrication d'un site est
identique partout.

### 6.1 Provision en une commande

```bash
# Hôtel
wp maji provision --dna=clients/hotel-atlantique/dna.json

# Restaurant (installe WooCommerce + COD si absent)
wp maji provision --dna=clients/saveurs/dna.json --with-woo
```

Options utiles :

| Option | Effet |
|---|---|
| `--with-woo` | Installe/active WooCommerce et configure la devise XOF + paiement à la livraison |
| `--model=<dir>` | Force le dossier seed (par défaut = `content.seed` de l'ADN) |
| `--registry=<path>` | Registre anti-clones à utiliser (défaut `registry/registry.json`) |
| `--skip-registry` | Ignore le contrôle anti-clones (dév uniquement) |

La commande enchaîne : validation ADN → contrôle registre → WooCommerce →
application (réglages, styles, pages, menu) → import contenus → contrôle final des
jetons. Elle est **idempotente** : relançable sans effet de bord.

### 6.2 Vérifications post-provision

```bash
wp option get blogname                          # nom du site appliqué
wp post list --post_type=page --fields=post_name,post_status
wp option get maji_settings --format=json       # réglages issus de l'ADN
wp maji dna register clients/<slug>/dna.json --registry=registry/registry.json
```

Contrôlez ensuite l'accueil dans le navigateur : hero correct, aucun jeton
`{{maji:*}}` visible, photos du client en place, rendu mobile. Détail du contrôle
qualité et de la livraison : `docs/GUIDE-CREATION-SITE.md` (parties 8–9).

### 6.3 Installation « from scratch » scriptée

Sur une base vide (local ou VPS), `scripts/install.sh` fait tout d'un coup :

```bash
./scripts/install.sh \
  --url=https://client.example \
  --title="Hôtel Atlantique" \
  --admin-user=maji --admin-email=tech@maji.digital \
  --admin-password='MOT-DE-PASSE-FORT' \
  --dna=demo-content/hotel-business/dna.json \
  --with-woo
```

Il télécharge WordPress fr_FR, installe, copie thème + plugin (ou les zips de
`dist/` s'ils existent), active, règle fuseau/permaliens et lance `wp maji
provision`.

---

## 7. Dépannage

| Symptôme | Cause probable | Solution |
|---|---|---|
| `wp-env` : `docker API … no such file` | Docker n'est pas démarré | Lancez Docker Desktop / `systemctl start docker` |
| `Error: '…dna.json' introuvable` | Mauvais chemin (surtout dans wp-env) | Utilisez le chemin conteneur ou un mapping `.wp-env.json` |
| `Des jetons {{maji:*}} restent visibles` | Un texte attendu manque dans `dna.json` | La sortie liste les jetons ; complétez `content.texts` (voir §6 du guide de création) |
| `media.X : attribut alt requis` | Photo sans `alt` dans `content.json` | Ajoutez `"alt": "…"` |
| `contraste … insuffisant` | Couleurs de l'ADN peu lisibles | Ajustez la palette (ratio ≥ 4.5:1) |
| Images SVG rejetées à l'import | WordPress bloque le SVG par défaut | Utilisez WebP/JPEG, ou autorisez le SVG via un plugin/mu-plugin de confiance |
| `plugin-update-checker` introuvable | `composer install` non lancé dans le plugin | `cd wp-content/plugins/maji-core && composer install --no-dev` |
| Mémoire PHP insuffisante à l'import | Limite trop basse sur mutualisé | Augmentez `memory_limit` (php.ini / `.user.ini` / MultiPHP INI Editor) |

Pour l'exploitation continue (branchement n8n/WhatsApp, mises à jour de flotte,
registre anti-clones) : **`docs/OPERATIONS.md`**.
