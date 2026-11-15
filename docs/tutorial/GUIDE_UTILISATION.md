# Guide d'utilisation — Gestion du Patrimoine de l'État

**République Togolaise** — *Travail · Liberté · Patrie*
**Version** : 1.0 — Document officiel

---

## Table des matières

1. [Présentation](#1-présentation)
2. [Connexion et déconnexion](#2-connexion-et-déconnexion)
3. [Tableau de bord](#3-tableau-de-bord)
4. [Registre des biens](#4-registre-des-biens)
5. [Transferts et affectations](#5-transferts-et-affectations)
6. [Inventaires terrain](#6-inventaires-terrain)
7. [Rapports et exports](#7-rapports-et-exports)
8. [Administration](#8-administration)
9. [Mode hors ligne (PWA)](#9-mode-hors-ligne-pwa)
10. [Dépannage](#10-dépannage)

---

## 1. Présentation

Le système de Gestion du Patrimoine de l'État permet de :

- **Recenser** l'ensemble des biens publics dans un registre unique
- **Suivre** chaque bien : état, localisation, responsable, valeur
- **Tracer** chaque mouvement (transferts entre services)
- **Inventorier** physiquement les biens sur le terrain
- **Valoriser** le patrimoine (dépréciation, valeur résiduelle)
- **Documenter** (PDF officiel, étiquettes QR, photos)

### Profils utilisateurs

| Profil | Droits |
|---|---|
| **Administrateur national** | Accès complet : tous services, paramétrage, audit |
| **Gestionnaire de service** | Biens de son service, campagnes d'inventaire |
| **Agent** | Saisie des constats d'inventaire terrain |

---

## 2. Connexion et déconnexion

1. Ouvrez l'adresse de l'application dans votre navigateur
2. Saisissez votre **adresse e-mail** et votre **mot de passe**
3. Cliquez sur **Se connecter**

> **Mot de passe oublié ?** Cliquez sur le lien « Mot de passe oublié » sur la page de connexion, saisissez votre e-mail, puis utilisez le lien reçu pour définir un nouveau mot de passe.

Pour vous déconnecter : cliquez sur **Déconnexion** en bas du menu latéral.

---

## 3. Tableau de bord

Page d'accueil après connexion.

- **Indicateurs clés** : total des biens, valeur d'acquisition cumulée, répartition par état
- **Widget dépréciation** : valeur résiduelle totale du parc
- **Actions rapides** : raccourcis vers les opérations courantes
- **Biens récents** : derniers enregistrements

### Personnaliser le tableau de bord

1. Cliquez sur **Personnaliser**
2. Utilisez les flèches **↑ ↓** pour réordonner les blocs
3. Utilisez l'**œil** pour masquer/afficher un widget
4. Cliquez **Enregistrer** — vos préférences sont sauvegardées
5. **Réinitialiser** pour revenir à l'affichage par défaut

> Un gestionnaire ne voit que les biens de son service ; l'administrateur national voit tout.

---

## 4. Registre des biens

### 4.1 Consulter la liste (`/biens`)

La liste affiche tous les biens avec recherche et filtres. Cliquez sur un bien pour ouvrir sa **fiche patrimoniale**.

### 4.2 La fiche d'un bien

| Section | Contenu |
|---|---|
| Identification | Numéro d'inventaire, désignation, description, catégorie |
| Affectation | Service responsable, localisation, personne responsable |
| Valorisation | Valeur d'acquisition, **valeur résiduelle** (dépréciation auto) |
| **QR code** | Code unique du bien + bouton « Étiquette » imprimable |
| **Photos** | Galerie : ajout, consultation, suppression |
| Historique | Tous les changements d'état avec justificatifs |

### 4.3 Télécharger la fiche PDF

Cliquez sur **« Télécharger la fiche PDF »** : document officiel A4 aux couleurs de la République, avec en-tête institutionnel, QR code et photos.

### 4.4 Étiquette QR

Cliquez sur **« Étiquette »** : page imprimable au format étiquette avec le QR code du bien — à coller physiquement sur le bien pour l'identification terrain.

### 4.5 Valeur résiduelle

La dépréciation est **automatique** : chaque catégorie a une durée de vie (années) ; la valeur résiduelle = valeur d'acquisition × (1 − âge/durée de vie). Configurez-la dans *Catégories → Durée de vie*.

---

## 5. Transferts et affectations

### Demander un transfert

1. Ouvrez la fiche du bien → **Demander un transfert**
ou menu **Transferts → Nouveau**
2. Choisissez le service de destination, la localisation, le motif
3. Joignez le **justificatif** (scan, PDF…)
4. Soumettez — la demande passe en attente de validation

### Approuver / rejeter (gestionnaire habilité)

- La demande apparaît dans **Transferts → En attente**
- **Approuver** : le bien change de service, l'historique est mis à jour
- **Rejeter** : le bien reste en place, le motif du rejet est enregistré

> Aucun transfert n'est effectif sans décision enregistrée — traçabilité complète.

---

## 6. Inventaires terrain

### Créer une campagne

1. Menu **Inventaires → Nouvelle campagne**
2. Nommez-la, fixez les dates, choisissez le périmètre (service ou national)
3. La campagne passe au statut **Ouverte**

### Enregistrer un constat

Sur la page de la campagne, pour chaque bien :
- **État observé** (conforme, endommagé, manquant…)
- **Commentaire** libre
- **Photo** du bien (recommandé)

> Scannez le QR code de l'étiquette pour retrouver directement le bien.

### Clôturer

Quand tous les biens sont vérifiés : **Clôturer la campagne** — les états des biens sont mis à jour automatiquement et un rapport est généré.

---

## 7. Rapports et exports

Page **Rapports** :

| Export | Format | Usage |
|---|---|---|
| **Fiche patrimoniale** | PDF A4 | Document officiel d'un bien |
| **Registre PDF** | PDF A4 paysage | État complet du parc |
| **Registre Excel** | .xlsx | Analyse, tableaux croisés |
| **Registre CSV** | .csv | Interopérabilité, autres logiciels |

- Les **non-administrateurs** n'exportent que les biens de leur service
- Le **journal d'audit** (admin national) liste toutes les opérations horodatées

---

## 8. Administration

| Menu | Fonction |
|---|---|
| **Catégories** | Types de biens + **durée de vie** (dépréciation) |
| **Services** | Services publics gérant les biens |
| **Ministères** | Ministères de tutelle |
| **Utilisateurs** | Création des comptes (auth centralisée) |
| **Paramètres** | Nom de l'organisme, devise, coordonnées |
| **Profil** | Modifier son nom / mot de passe |

---

## 9. Mode hors ligne (PWA)

L'application est **installable** comme une application native :

- **PC** : menu navigateur → « Installer l'application » / « Ajouter à l'écran d'accueil »
- **Mobile** : navigateur → « Ajouter à l'écran d'accueil »

### Constats hors ligne

1. Ouvrez la page d'une campagne **avant** de perdre le réseau
2. Coupez le Wi-Fi / passez en mode avion
3. Enregistrez vos constats normalement — ils sont mis en **file locale**
   (bandeau « X constat(s) en attente » en haut de page)
4. Au retour du réseau : **synchronisation automatique**
5. Les constats en conflit (doublon, campagne clôturée) restent affichés pour correction

> Les pages déjà consultées restent accessibles hors ligne (cache PWA).

---

## 10. Dépannage

| Problème | Solution |
|---|---|
| « Session expirée » | Reconnectez-vous, l'activité reprend |
| Page blanche après mise à jour | Videz le cache navigateur (Ctrl+Shift+Suppr) |
| Photo/justificatif illisible | Formats acceptés : JPG, PNG, PDF — max 5 Mo |
| QR code illisible | Ré-imprimez l'étiquette à 100 % (pas de mise à l'échelle) |
| Constats bloqués « en attente » | Vérifiez la connexion puis rechargez la page campagne |
| Lien de réinitialisation expiré | Refaire une demande — le lien est valable 60 min |
| Toujours bloqué | Contactez l'administrateur national |

---

*Document généré pour la Direction Générale du Patrimoine de l'État —
République Togolaise. Travail · Liberté · Patrie.*
