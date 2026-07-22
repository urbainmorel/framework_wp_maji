# Exploitation de l'usine MAJI

## Créer un nouveau site client

1. **Dupliquer un ADN** proche du besoin (`dna/examples/` ou `demo-content/*/dna.json`)
   et l'adapter : identité, DA, paire typo, palette, header/footer, vocabulaire de
   navigation, pages et sections, textes (`content.texts`).
2. **Valider** :
   ```bash
   wp maji dna validate client/dna.json
   ```
   Contrôles : schéma `maji-dna/1`, références (DA, paire typo, header/footer,
   patterns, vocabulaire), contrastes WCAG AA (`ink/surface`,
   `primary-contrast/primary`, `accent-contrast/accent`), E.164, ISO 4217, dates.
3. **Contrôler l'unicité** (bloquant, F-U3) :
   ```bash
   wp maji dna check client/dna.json --registry=registry/registry.json
   ```
   Similarité ≥ 0,70 avec un site livré ⇒ blocage avec le site le plus proche,
   les axes en collision et des suggestions. `--force` existe mais est tracé.
4. **Préparer le seed** : dossier `client/` avec `dna.json`, `content.json`
   (médias avec `alt` obligatoires, chambres, plats) et `media/`.
5. **Provisionner** : `scripts/install.sh` (voir INSTALLATION.md) ou
   `wp maji provision --dna=client/dna.json [--with-woo]` — idempotent, relançable.
6. **Livrer puis enregistrer l'empreinte** :
   ```bash
   wp maji dna register client/dna.json --registry=registry/registry.json
   git add registry/registry.json && git commit -m "chore: register <site>"
   ```

## Score de similarité (rappel)

`da` 0,30 · `font_pair` 0,20 · `hero` 0,20 · `palette_hue_bucket` 0,15 (teinte du
primaire par tranches de 30°) · `header` 0,10 · `section_order_hash` 0,05 (SHA-1 des
sections de l'accueil). Blocage à **≥ 0,70**.

## Branchement n8n

1. Importer les workflows `n8n/*.json` dans votre instance n8n.
2. Récupérer le secret du site : `wp option get maji_webhook_secret` (jamais affiché
   dans l'admin) et le renseigner dans les nœuds « Vérifier signature ».
3. Renseigner le `PHONE_NUMBER_ID` WhatsApp Business et le numéro du gérant dans les
   nœuds « Notifier ».
4. Copier les URLs de webhook n8n dans MAJI → Intégrations (`n8n_order_url`,
   `n8n_reservation_url`).
5. Tester : une commande/réservation test doit apparaître dans MAJI → Journal des
   webhooks avec un statut HTTP 2xx. Un échec est relancé à +1, +10 et +60 minutes ;
   le bouton « Renvoyer » reconstruit le payload depuis la commande/réservation source.

En-têtes envoyés : `X-MAJI-Event`, `X-MAJI-Site`, `X-MAJI-Delivery` (UUID),
`X-MAJI-Signature: sha256=HMAC-SHA256(corps, secret)`. Timeout 5 s.

## Mises à jour de flotte

- Publier : mettre à jour `CHANGELOG.md`, bumper les versions (`style.css`,
  `maji-core.php`), tagger `vX.Y.Z` et pousser — la CI (`release.yml`) construit
  `maji-framework.zip` et `maji-core.zip` et crée la GitHub Release.
- Chaque site vérifie les releases via plugin-update-checker : le correctif est
  proposé dans l'admin des ~30 sites en quelques minutes (< 30 min, O4).
- Canal beta : tagger `vX.Y.Z-beta.N` (pré-release) et définir
  `define( 'MAJI_UPDATE_CHANNEL', 'beta' );` sur le site interne de test.
- Dépôt privé : générer un jeton GitHub en lecture seule sur ce dépôt et l'injecter
  via le filtre `puc_request_info_options-maji-core` ; faire tourner le jeton à chaque
  départ d'un membre de l'équipe.
- Garantie : la personnalisation (ADN) vit dans la base (global styles utilisateur,
  pages, réglages) — jamais dans les fichiers du thème — une mise à jour n'écrase
  donc jamais un site client.

## Sauvegardes et registre

- `registry/registry.json` est la source de vérité anti-clones : le committer après
  chaque livraison.
- Exporter un site réussi comme modèle : `wp maji export-model <slug> --dest=demo-content`
  (compléter ensuite `media/` et les textes du `content.json`).
