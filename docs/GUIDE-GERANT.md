# Guide du gérant — administrer votre site au quotidien

Ce guide s'adresse au gérant de l'établissement. Aucune compétence technique
n'est nécessaire : chaque opération courante prend moins de 2 minutes, y compris
depuis un téléphone.

**Connexion** : `https://votre-site/wp-admin` avec l'identifiant et le mot de
passe remis à la livraison. En cas de perte : « Mot de passe oublié ? » sur la
page de connexion (un e-mail de réinitialisation vous est envoyé).

---

## 1. Restaurant — gérer la carte

### Modifier le prix d'un plat

1. Menu latéral → **Produits**.
2. Cliquez sur le plat.
3. Champ **Tarif** : saisissez le nouveau prix (en FCFA, sans espace ni point).
4. Bouton bleu **Mettre à jour**. C'est en ligne immédiatement.

### Marquer un plat en rupture (sans le supprimer)

1. **Produits** → ouvrez le plat.
2. Dans l'encadré MAJI de la fiche, **décochez « Disponible »**.
3. **Mettre à jour**. Le plat reste visible sur la carte mais ne peut plus être
   commandé. Recochez la case quand il revient.

### Ajouter un nouveau plat

1. **Produits → Ajouter un produit**.
2. Renseignez : nom, **description courte** (2 lignes maximum, c'est elle qui
   s'affiche sur la carte), **Tarif**.
3. À droite, **catégorie de menu** (Entrées, Plats, Desserts, Boissons…).
4. **Image produit** : une photo carrée, lumineuse, du plat seul.
5. Badges MAJI si pertinent : *Populaire*, *Épicé*, *Nouveau*.
6. **Publier**.

### Changer une photo de plat

Ouvrez le plat → colonne de droite → **Image produit → Supprimer** puis
**Définir l'image produit** → téléversez la nouvelle photo → **Mettre à jour**.

---

## 2. Hôtel — gérer les chambres

### Modifier un prix ou une capacité

1. Menu latéral → **Chambres**.
2. Ouvrez la chambre → colonne de droite, champs **price_from** (prix « à partir
   de », en FCFA), **capacity** (personnes), **size_sqm** (m²).
3. **Mettre à jour**.

### Ajouter une chambre

**Chambres → Ajouter une chambre** : titre, description, **image mise en avant**
(photo de la chambre), prix/capacité/surface, cochez les **équipements**
(Wi-Fi, climatisation…). **Publier** — elle apparaît aussitôt dans la grille du site.

---

## 3. Traiter les demandes de réservation

Chaque demande (chambre ou table) envoyée depuis le site apparaît dans
**MAJI → Réservations** — et vous êtes notifié sur WhatsApp si l'automatisation
n8n est branchée.

1. La liste montre : client, téléphone, dates, chambre/table, **statut**.
2. Contactez le client (son numéro est cliquable) pour confirmer la disponibilité.
3. Survolez la ligne et cliquez **Marquer : Confirmée** (ou Refusée / Annulée).
   Le client peut recevoir automatiquement un message WhatsApp de confirmation.

Le paiement des séjours reste géré comme aujourd'hui (sur place, virement…) :
le site collecte les **demandes**, vous gardez la main sur la confirmation.

## 4. Traiter les commandes (restaurant)

Les commandes arrivent dans **Commandes** (WooCommerce) et sur votre WhatsApp.

- Chaque commande indique : plats, quantités, **livraison (avec quartier et
  frais) ou retrait**, téléphone du client, note éventuelle (« sans piment »).
- Le paiement est **à la livraison** : encaissez à la remise de la commande.
- Faites évoluer le statut : **En cours** (en préparation) → **Terminée**
  (livrée/retirée). Le client peut être notifié automatiquement à chaque étape.

Hors de vos horaires d'ouverture, la commande est automatiquement bloquée (la
carte reste consultable) : rien à faire de votre côté.

## 5. Coordonnées, horaires, réseaux sociaux

**MAJI → Établissement** : téléphone, WhatsApp, e-mail, adresse, horaires
(format `08:00-12:00, 15:00-22:00`, vide = fermé), réseaux sociaux.

Une seule saisie ici met à jour **tout le site** : en-tête, pied de page,
boutons WhatsApp, page contact et données pour Google. Pensez-y avant les jours
fériés : modifiez vos horaires ici et la commande en ligne suivra.

## 6. Ce qu'il vaut mieux ne pas toucher

- **Apparence → Éditeur** : le design de votre site (couleurs, polices, mises en
  page) est géré par MAJI. Une modification hasardeuse peut casser la cohérence —
  demandez-nous, c'est inclus dans votre accompagnement.
- **Extensions / Thèmes** : n'installez et ne supprimez rien. Les mises à jour
  MAJI arrivent automatiquement et sont testées avant diffusion.
- **Réglages → Permaliens** et autres réglages techniques.

## 7. Petits problèmes courants

| Symptôme | Réflexe |
|---|---|
| « Je ne reçois plus les notifications WhatsApp » | Vérifiez votre connexion WhatsApp Business, puis contactez MAJI (l'historique complet reste dans MAJI → Réservations / Commandes — aucune demande n'est perdue) |
| « Un plat modifié ne s'affiche pas » | Rechargez la page en navigation privée (le cache de votre téléphone garde l'ancienne version quelques minutes) |
| « Je n'arrive pas à me connecter » | « Mot de passe oublié ? » sur la page de connexion |
| « Le site est lent / en erreur » | Contactez MAJI immédiatement avec une capture d'écran |

**Support MAJI** : votre interlocuteur habituel sur WhatsApp, du lundi au samedi.
