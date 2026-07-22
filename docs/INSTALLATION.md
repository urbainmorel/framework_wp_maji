# Installation d'un site client MAJI

## Prérequis

- Hébergement mutualisé LAMP standard : PHP ≥ 8.1, MySQL 5.7+ / MariaDB 10.4+
- WP-CLI 2.x sur la machine d'installation (ou accès SSH à l'hébergement)
- Un fichier ADN (`dna.json`) validé pour le client — voir `docs/OPERATIONS.md`
- WooCommerce requis uniquement en mode restaurant (installé automatiquement avec `--with-woo`)

## Installation automatisée (recommandée)

Depuis le dossier racine du site (vide ou avec `wp-config.php` déjà créé) :

```bash
/chemin/vers/framework/scripts/install.sh \
  --url=https://client.example \
  --title="Hôtel Atlantique" \
  --admin-user=maji \
  --admin-email=tech@maji.digital \
  --admin-password='MOT-DE-PASSE-FORT' \
  --dna=/chemin/vers/framework/demo-content/hotel-business/dna.json \
  --with-woo
```

Le script enchaîne : téléchargement WordPress fr_FR → installation → thème + plugin →
réglages de base (fuseau, permaliens) → `wp maji provision` (validation ADN, contrôle
anti-clones, application, import des contenus, contrôle final des jetons).

## Installation manuelle

1. Installer WordPress ≥ 6.6 en français.
2. Téléverser `maji-framework.zip` (thème) et `maji-core.zip` (plugin) depuis la
   dernière release GitHub, puis activer les deux.
3. Copier le dossier seed du client (dna.json + content.json + media/) sur le serveur.
4. Lancer :
   ```bash
   wp maji provision --dna=/chemin/dna.json [--with-woo]
   ```

## Vérifications post-installation

- [ ] La page d'accueil affiche le hero choisi, sans aucun jeton `{{maji:*}}` visible.
- [ ] Basculer la DA dans Apparence → Éditeur → Styles change l'apparence sans perte de contenu.
- [ ] Une demande de réservation test apparaît dans MAJI → Réservations avec le statut « Nouvelle ».
- [ ] (Restaurant) Une commande test COD hors horaires est bloquée avec un message clair.
- [ ] MAJI → Journal des webhooks montre les envois si des URLs n8n sont configurées.
- [ ] Après livraison : `wp maji dna register <dna> --registry=registry/registry.json`
  puis commit du registre dans ce dépôt.

## Environnement de développement

```bash
composer install && npm install
npm run start        # wp-env : WordPress fr_FR + thème + plugin + WooCommerce
npm run stop
```

Provision d'un site modèle dans wp-env :

```bash
npx wp-env run cli -- wp maji provision \
  --dna=/var/www/html/demo-content/hotel-business/dna.json \
  --skip-registry
```
