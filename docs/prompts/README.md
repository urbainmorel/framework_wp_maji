# Prompts IA — de la matière brute au site livré

Ce dossier contient des **prompts système** prêts à coller dans une IA (Claude,
Codex, GPT…) pour accélérer la production d'un site MAJI. Ils transforment ce
que le client vous envoie (un document Word/PDF, un dossier de photos) en fichiers
directement exploitables par l'usine.

## Deux façons de travailler

### Voie 1 — Assistée (vous gardez la main)

Vous utilisez les prompts comme des outils ponctuels, chacun dans une conversation
IA classique (interface web, pas d'accès à votre ordinateur) :

1. **`01-generateur-fichiers.md`** — vous collez le brief client (texte) → l'IA
   produit `dna.json` et `content.json`.
2. **`02-classement-medias.md`** — vous montrez les photos/vidéos → l'IA propose
   un plan de renommage et les descriptions `alt`.
3. Vous rangez les fichiers, vous lancez `wp maji provision` vous-même (voir
   `docs/GUIDE-CREATION-SITE.md`).

### Voie 2 — Autonome (l'agent fait tout) ⭐ recommandée

Claude Code et Codex peuvent **piloter votre ordinateur** : lire des fichiers,
écrire des fichiers, exécuter des commandes, lire les erreurs et se corriger. On
tire parti de ça pour supprimer presque tout le travail manuel.

Vous déposez la matière brute du client dans un dossier, vous lancez l'agent avec
le prompt **`03-agent-orchestrateur.md`**, et il fait la chaîne complète :
génère les JSON, range et renomme les médias, lance la provision, lit les erreurs,
se corrige, recommence jusqu'au succès, puis vous rend un rapport avec l'URL et
les points à valider. **Vous n'êtes plus opérateur, vous êtes relecteur.**

## Quel prompt pour quel outil ?

| Fichier | Type d'IA | Ce qu'il faut lui fournir |
|---|---|---|
| `01-generateur-fichiers.md` | Chat (Claude/GPT web) | Le texte du brief client |
| `02-classement-medias.md` | Chat **multimodal** (voit les images) | Les photos/vidéos |
| `03-agent-orchestrateur.md` | Agent avec terminal (Claude Code, Codex) | Le dossier brut + accès à l'usine |

## Important — toujours relire

Ces prompts produisent un **brouillon de grande qualité**, pas une vérité absolue.
Vérifiez systématiquement : numéros de téléphone, prix, horaires, orthographe du
nom du client, et le rendu final du site. L'usine bloque déjà les erreurs
techniques (contraste, format téléphone, jetons manquants) ; à vous de valider le
sens et l'exactitude commerciale.
