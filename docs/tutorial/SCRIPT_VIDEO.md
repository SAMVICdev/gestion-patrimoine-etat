# Script vidéo — Tutoriel « Gestion du Patrimoine de l'État »

**Durée cible** : 8–12 minutes
**Format** : 1920×1080, voix off en français, sous-titres recommandés
**Outil de montage suggéré** : DaVinci Resolve (gratuit), CapCut ou OBS pour l'enregistrement écran

---

## Préparation avant le tournage

- [ ] Application lancée : `docker compose up -d` → http://localhost:8002
- [ ] Compte administrateur national créé
- [ ] Quelques données de démonstration : 3–4 biens dans 2 services, 1 catégorie, 1 campagne d'inventaire
- [ ] Navigateur en 1920×1080, zoom à 100 %, onglets fermés
- [ ] Micro pour la voix off (ou narration ajoutée au montage)

---

## SCÈNE 1 — Introduction (0:00 – 0:40)

**Visuel** : plan fixe sur le bureau ou générique animé ; puis page de connexion plein écran.

**Actions à l'écran** :
1. Ouvrir http://localhost:8002 → la page de connexion s'affiche
2. Laisser 3 s sur le design (blason, photos de Lomé, diaporama)
3. Zoom léger sur le formulaire de connexion

**Voix off** :
> « Bienvenue dans le système de Gestion du Patrimoine de l'État de la République Togolaise.
> Cette application permet à la Direction Générale du Patrimoine de recenser, suivre,
> transférer et inventorier l'ensemble des biens publics de l'État. »

**Sous-titre** : `Système d'Information Domaniale — République Togolaise`

---

## SCÈNE 2 — Connexion (0:40 – 1:10)

**Visuel** : enregistrement écran de la page de connexion.

**Actions** :
1. Saisir l'email + mot de passe administrateur
2. Cliquer « Se connecter »
3. Arrivée sur le tableau de bord

**Voix off** :
> « L'accès se fait par authentification sécurisée. Le système distingue trois profils :
> administrateur national, gestionnaire de service et agent d'inventaire. »

---

## SCÈNE 3 — Tableau de bord (1:10 – 1:50)

**Visuel** : écran du dashboard.

**Actions** :
1. Montrer les indicateurs (total biens, valeur, répartition par état)
2. Montrer le widget dépréciation
3. Cliquer « Personnaliser » → déplacer un widget → « Enregistrer »

**Voix off** :
> « Le tableau de bord offre une vision immédiate du parc : nombre de biens,
> valeur totale, états et dépréciation. Chaque utilisateur peut personnaliser
> l'affichage de ses widgets selon ses besoins. »

---

## SCÈNE 4 — Registre des biens (1:50 – 3:00)

**Visuel** : page `/biens` (liste) puis fiche d'un bien.

**Actions** :
1. Montrer la liste avec filtres/recherche
2. Ouvrir une fiche bien → dérouler les sections
3. Montrer : QR code, photos, valeur résiduelle, historique des états
4. Cliquer « Télécharger la fiche PDF » → montrer le PDF généré
5. Cliquer « Étiquette » → montrer l'étiquette QR imprimable

**Voix off** :
> « Chaque bien possède une fiche patrimoniale complète : identification,
> localisation, responsable, valeur et historique. Un QR code unique permet
> l'identification sur le terrain, et la fiche PDF constitue le document officiel. »

---

## SCÈNE 5 — Création d'un bien (3:00 – 3:50)

**Visuel** : formulaire de création.

**Actions** :
1. Cliquer « Nouveau bien »
2. Remplir les champs (désignation, catégorie, service, valeur…)
3. Soumettre → fiche créée

**Voix off** :
> « L'enregistrement d'un bien est simple : quelques champs suffisent,
> le numéro d'inventaire est généré automatiquement. »

---

## SCÈNE 6 — Transferts (3:50 – 4:40)

**Visuel** : page `/transferts`.

**Actions** :
1. Montrer la liste des transferts
2. Créer une demande de transfert → justificatif
3. Montrer la validation côté approbateur

**Voix off** :
> « Chaque mouvement d'un bien fait l'objet d'une demande de transfert
> avec justificatif, suivie d'une décision d'approbation ou de rejet.
> Aucune affectation ne se fait sans trace. »

---

## SCÈNE 7 — Inventaire terrain + hors ligne (4:40 – 6:00)

**Visuel** : page campagne d'inventaire ; **important** : montrer le mode hors ligne.

**Actions** :
1. Ouvrir une campagne → liste des biens à vérifier
2. Enregistrer un constat (état observé, commentaire)
3. **Couper le Wi-Fi** → enregistrer un constat → bandeau « en attente »
4. **Rallumer le Wi-Fi** → synchronisation automatique

**Voix off** :
> « L'inventaire physique peut se faire même sans réseau : l'application
> est installable sur mobile, les constats sont enregistrés localement
> puis synchronisés automatiquement au retour de la connexion. »

---

## SCÈNE 8 — Rapports et exports (6:00 – 7:00)

**Visuel** : page `/rapports`.

**Actions** :
1. Montrer le tableau de synthèse
2. Exporter en **PDF** → montrer le document
3. Exporter en **Excel** → montrer le fichier .xlsx
4. Exporter en **CSV**

**Voix off** :
> « Les rapports consolidés s'exportent en PDF pour les documents officiels,
> en Excel pour l'analyse et en CSV pour l'interopérabilité. »

---

## SCÈNE 9 — Administration (7:00 – 7:50)

**Visuel** : pages `/categories`, `/services`, `/utilisateurs`, `/parametres`.

**Actions** :
1. Créer une catégorie avec durée de vie (dépréciation)
2. Montrer la gestion des services/utilisateurs
3. Montrer le journal d'audit

**Voix off** :
> « L'administrateur gère les référentiels, les comptes utilisateurs
> et consulte le journal d'audit complet de toutes les opérations. »

---

## SCÈNE 10 — Conclusion (7:50 – 8:30)

**Visuel** : retour sur le dashboard, puis écran de fin avec blason.

**Actions** :
1. Retour au tableau de bord
2. Plan fixe 3 s
3. Carton de fin : « Gestion du Patrimoine de l'État — République Togolaise »

**Voix off** :
> « Le système de gestion du patrimoine de l'État offre à l'administration
> un outil complet, sécurisé et adapté au terrain : du recensement
> initial à l'audit final, chaque bien est tracé, chaque mouvement justifié. »

---

## Fiche technique du montage

| Élément | Recommandation |
|---|---|
| Logiciel d'enregistrement | OBS Studio (gratuit) ou Xbox Game Bar (Win+G) |
| Logiciel de montage | DaVinci Resolve (gratuit, pro) ou CapCut |
| Format export | MP4 H.264, 1920×1080, 30 fps |
| Sous-titres | Obligatoires — exporter le `.srt` depuis le script |
| Musique | Instrumental sobre (pas de voix concurrente) |
| transitions | Fondu enchaîné (0,5 s) entre les scènes |

## Conseils de tournage

1. **Nettoyez le bureau** et fermez notifications/raccourcis personnels
2. **Pré-remplissez les données** : préparez les biens de démo avant le tournage
3. **Faites 2–3 prises** par scène, gardez la meilleure
4. **Zoomez à 110 %** dans le navigateur si les textes sont petits
5. Pour la scène hors-ligne : coupez vraiment le Wi-Fi de la machine, c'est plus crédible
6. Sauvegardez chaque scène dans un dossier séparé (`SC01_connexion.mp4`…)
