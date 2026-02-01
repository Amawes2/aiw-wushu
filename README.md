# Wushu Club CI - Système de Gestion des Compétitions de Wushu

## Description du Projet

Wushu Club CI est un système web complet pour la gestion des compétitions de wushu en Côte d'Ivoire. Le site permet l'inscription des compétiteurs avec des catégories d'âge traditionnelles, des styles de wushu authentiques et des spécialisations d'armes spécifiques. Il offre une interface publique informative et un panneau d'administration complet pour la gestion des affiliations, compétiteurs et compétitions.

## Fonctionnalités Principales

### Site Public (index.html)
- **Page d'accueil moderne** : Présentation du club avec design responsive
- **Section Histoire** : Aperçu historique du wushu en Côte d'Ivoire
- **Disciplines** : Présentation des arts martiaux chinois
- **Galerie** : Images des entraînements et compétitions
- **Contact** : Formulaire de contact intégré
- **Inscriptions** : Liens vers l'inscription des compétiteurs

### Système d'Inscription des Compétiteurs (competiteurs.php)
- **Catégories d'âge traditionnelles** :
  - **Cadet** : 5-14 ans
  - **Junior** : 15-17 ans
  - **Senior** : 18 ans et plus
- **Styles de wushu authentiques** :
  - **Chang Quan** : Style du Nord (Long Poing)
  - **Nan Quan** : Style du Sud (Poing du Sud)
  - **Taichi** : Tai Chi Chuan (forme traditionnelle)
  - **Shaolin** : Arts martiaux du Temple Shaolin
  - **Sanda** : Combat libre (par catégories de poids)
- **Spécialisations d'armes** :
  - **Chang Quan** : Dao Shu (sabre), Gun Shu (bâton), Jian Shu (épée), Qiang Shu (lance)
  - **Nan Quan** : Nan Dao (sabre du sud), Gun Dao (bâton)
  - **Taichi** : Taiji Jian (épée), Tai Chi Shan (éventail)
- **Validation automatique** : Calcul automatique de la catégorie selon la date de naissance
- **JavaScript dynamique** : Affichage des spécialisations d'armes selon le style choisi

### Panneau d'Administration (admin.php)
- **Authentification sécurisée** : Connexion admin avec sessions PHP
- **Dashboard complet** : Statistiques des clubs, compétiteurs et compétitions
- **Gestion des compétiteurs** : Affichage des nouvelles colonnes (style, arme)
- **Gestion des clubs** : Validation des affiliations
- **Gestion des compétitions** : CRUD complet des événements
- **API REST** : Système de notifications (/api/notifications.php)

## Technologies Utilisées

- **Backend** : PHP 8.3 avec PDO pour la base de données
- **Base de données** : SQLite (wushuclubci.db)
- **Frontend** : HTML5, CSS3, JavaScript ES6+
- **UI/UX** : Font Awesome, Google Fonts (Montserrat), thème responsive
- **Sécurité** : Sessions PHP, validation côté serveur
- **API** : RESTful pour les notifications

## Structure des Fichiers

```
/
├── index.html              # Page d'accueil publique
├── competiteurs.php        # Formulaire d'inscription des compétiteurs
├── competitions.php        # Affichage des compétitions
├── admin.php               # Panneau d'administration
├── login.php               # Page de connexion admin
├── contact.php             # Traitement du formulaire de contact
├── dashboard.php           # Dashboard alternatif
├── clubs.php               # Gestion des clubs
├── results.php             # Affichage des résultats
├── test_api.php            # Tests de l'API
├── init_db.php             # Initialisation de la base de données
├── config_email.php        # Configuration des emails
├── functions.php           # Fonctions utilitaires
├── novotile.html           # Système de paiements (séparé)
├── admin.html              # Interface admin simple
├── README.md               # Cette documentation
├── wushuclubci.db          # Base de données SQLite
├── cookies.txt             # Cookies de session (test)
├── ngrok                   # Outil de tunneling (optionnel)
├── api/
│   └── notifications.php   # API REST pour notifications
├── css/
│   ├── styles.css          # Styles principaux
│   └── js-styles.css       # Styles JavaScript
├── images/                 # Images du site
└── js/
    └── main.js             # JavaScript principal
```

## Installation et Configuration

### Prérequis
- PHP 8.3 ou supérieur
- SQLite3
- Serveur web (Apache/Nginx) ou PHP built-in server

### Installation
1. **Cloner le repository** :
   ```bash
   git clone https://github.com/Amawes2/aiw-wushu.git
   cd aiw-wushu
   ```

2. **Initialiser la base de données** :
   ```bash
   php init_db.php
   ```

3. **Démarrer le serveur** :
   ```bash
   php -S localhost:8080
   ```

4. **Accéder au site** :
   - Site public : http://localhost:8080
   - Inscription compétiteurs : http://localhost:8080/competiteurs.php
   - Administration : http://localhost:8080/admin.php

### Configuration Admin
- **Identifiants par défaut** :
  - Utilisateur : `admin`
  - Mot de passe : `wushuclubci2024`
- **Modifier dans** : `login.php` (ligne 12-13)

### Configuration Email
- **Fichier** : `config_email.php`
- **Variables à configurer** : SMTP host, port, username, password

## Utilisation

### Pour les Compétiteurs
1. Accéder à la page d'inscription
2. Remplir le formulaire avec :
   - Informations personnelles
   - Catégorie (calculée automatiquement)
   - Style de wushu
   - Spécialisation d'arme (si applicable)
3. Soumettre le formulaire
4. Recevoir confirmation par email

### Pour les Administrateurs
1. Se connecter via `/login.php`
2. Accéder au dashboard
3. Gérer clubs, compétiteurs et compétitions
4. Consulter les statistiques

## Base de Données

### Tables Principales
- **competiteurs** : nom, prenom, date_naissance, categorie, style, arme_specialisation, club_id, email, telephone
- **clubs** : nom, responsable, email, telephone, adresse, statut
- **competitions** : nom, date_debut, date_fin, lieu, description, statut

### Schéma des Catégories
```sql
-- Calcul automatique des catégories
Cadet: âge >= 5 AND âge <= 14
Junior: âge >= 15 AND âge <= 17
Senior: âge >= 18
```

## API REST

### Notifications
- **Endpoint** : `/api/notifications.php`
- **Méthodes** :
  - `GET ?action=check` : Vérifier les nouvelles notifications
  - `POST ?action=mark_read` : Marquer comme lu

## Sécurité

- **Authentification** : Sessions PHP sécurisées
- **Validation** : Côté serveur et client
- **Protection XSS** : Échappement des données
- **CSRF** : Tokens de session

## Design et Thème

- **Couleurs** : Rouge CI (#e30613), Or (#d4af37), Blanc (#ffffff)
- **Responsive** : Adapté mobile, tablette, desktop
- **Animations** : Transitions CSS fluides
- **Accessibilité** : Icônes Font Awesome, contrastes élevés

## Historique des Modifications

### Version 2.0.0 - Février 2026
- **Rebranding complet** : Changement de "FIAMC" à "Wushu Club CI"
- **Nouvelles catégories d'âge** : Implémentation des catégories traditionnelles (Cadet 5-14, Junior 15-17, Senior 18+)
- **Styles de wushu authentiques** : Ajout de Chang Quan, Nan Quan, Taichi, Shaolin, Sanda
- **Spécialisations d'armes** : Système complet d'armes par style
- **Base de données** : Migration vers SQLite avec nouvelles colonnes
- **Interface admin** : Corrections des variables de session, affichage des nouvelles données
- **JavaScript dynamique** : Mise à jour du DOM pour les spécialisations d'armes
- **Validation** : Tests complets des inscriptions et de l'administration

### Version 1.1.0 - Janvier 2026
- Séparation des projets (AIW Wushu et Novotile)
- Extraction du CSS vers fichier externe
- Amélioration de la structure des fichiers

### Version 1.0.0 - Initiale
- Site web statique avec interface admin basique
- Formulaires de contact et d'inscription simulés

## Améliorations Futures

- [ ] Système de paiement intégré
- [ ] Notifications push
- [ ] Application mobile
- [ ] Génération de rapports PDF
- [ ] Système de notation des compétitions
- [ ] API pour applications tierces
- [ ] Multilangue (français/anglais)

## Support

Pour toute question ou problème :
- **Email** : contact@wushuclubci.ci
- **Repository** : https://github.com/Amawes2/aiw-wushu
- **Issues** : Ouvrir une issue sur GitHub

## Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

---

**Wushu Club CI** - Promouvoir l'excellence dans les arts martiaux chinois en Côte d'Ivoire 🇨🇮

### Version 1.2.0 - 31 Janvier 2026
- **Implémentation PHP pour le formulaire de contact** : Création du fichier `contact.php` pour traiter les soumissions du formulaire de contact côté serveur.
  - Validation des données d'entrée (nom, email, sujet, message).
  - Envoi d'emails automatiques aux administrateurs.
  - Gestion des erreurs et messages de confirmation.
- **Suppression du JavaScript du formulaire** : Le code JS qui empêchait la soumission réelle du formulaire a été retiré pour permettre le traitement PHP.
- **Mise à jour du formulaire HTML** : Ajout des attributs `name` aux champs du formulaire et changement de la méthode en `POST` avec `action="contact.php"`.
- **Test du serveur local** : Démarrage d'un serveur PHP de développement pour tester la fonctionnalité du formulaire.

### Version 1.3.0 - 31 Janvier 2026
- **Ajout d'images locales** : Création du dossier `images/` et remplacement des URLs externes (Unsplash) par des chemins locaux pour la galerie.
  - Images créées : entraînement.jpg, compétition.jpg, stage.jpg, armes.jpg, enfants.jpg, cérémonie.jpg.
  - Les logos des partenaires restent sur des URLs officielles (Wikimedia).
- **Structure améliorée** : Organisation des ressources visuelles dans un dossier dédié pour une meilleure maintenabilité.

### Version 1.4.0 - 31 Janvier 2026
- **Renommage du site en Wushu Club CI** : Transformation du site d'AIW Wushu vers Wushu Club CI (Fédération Ivoirienne des Arts Martiaux Chinois).
  - Changement du titre, logo, descriptions et références dans `index.html`.
  - Mise à jour du `README.md` avec la nouvelle description et fonctionnalités.
  - Adaptation pour faciliter les inscriptions des clubs et compétiteurs aux compétitions.
- **Préparation pour les inscriptions** : Structure mise en place pour ajouter les pages d'inscription des clubs et compétiteurs.

### Version 1.5.0 - 31 Janvier 2026
- **Création de clubs.php** : Page d'inscription des clubs pour la Wushu Club CI.
  - Formulaire avec validation pour nom du club, maître, email, téléphone.
  - Base de données SQLite créée automatiquement avec table `clubs`.
  - Traitement PHP pour l'inscription et messages de confirmation/erreur.
- **Mise à jour de index.html** : Ajout du lien "Inscriptions" dans le menu de navigation et redirection du bouton hero vers clubs.php.
- **Base de données** : Introduction de SQLite pour la persistance des données des clubs.

### Version 1.6.0 - 31 Janvier 2026
- **Ajout du panel d'administration PHP** : Création du fichier `admin.php` avec authentification côté serveur et gestion complète des clubs (validation, rejet, suppression).
- **Intégration base de données** : Connexion à `wushuclubci.db` pour afficher les statistiques et la liste des clubs inscrits.
- **Amélioration de la sécurité** : Authentification PHP avec sessions pour l'accès admin.

### Version 1.7.0 - 31 Janvier 2026
- **Ajout de la gestion des compétiteurs** : Création de `competiteurs.php` pour l'inscription des athlètes avec validation automatique des catégories selon l'âge.
- **Extension de la base de données** : Ajout des tables `competiteurs` et `competitions` dans `wushuclubci.db`.
- **Mise à jour du panel admin** : Ajout de statistiques et gestion des compétiteurs dans `admin.php` avec navigation par onglets.

### Version 1.8.0 - 31 Janvier 2026
- **Ajout de la gestion des compétitions** : Création de `competitions.php` pour l'affichage public des événements sportifs.
- **Extension du panel admin** : Ajout de la gestion complète des compétitions (ajout, annulation, suppression) dans `admin.php`.
- **Interface améliorée** : Navigation à 3 onglets (Clubs, Compétiteurs, Compétitions) avec statistiques complètes.

### Version 1.9.0 - 31 Janvier 2026
- **Système de gestion média complet** : Création d'un système intégré pour gérer les vidéos YouTube et les galeries photos.
- **Pages détaillées des disciplines** : Développement des pages `taolu.php`, `sanda.php`, `qigong_taichi.php`, et `formes_traditionnelles.php` avec documentation complète, historique, règles IWUF, et intégration multimédia.
- **Intégration YouTube** : Ajout d'iframes YouTube dans chaque page de discipline avec des vidéos pertinentes et un outil de recherche YouTube (`youtube_search.php`).
- **Galerie média administrative** : Création de `media_upload.php` pour l'upload de médias et `media_gallery.php` pour la visualisation et gestion.
- **Structure média organisée** : Mise en place du dossier `media/` avec sous-dossiers par discipline pour une organisation optimale des fichiers.
- **Documentation média** : Ajout de guides complets (`guide_videos_youtube.md`, `liens_youtube_reels.md`, `youtube_videos.md`) pour l'intégration et la gestion des contenus multimédias.

## Contexte du Projet

Ce projet a été développé dans le cadre de la promotion du wushu en Côte d'Ivoire, en mettant l'accent sur l'accessibilité, l'information et la gestion efficace de l'association. Tous les éléments sont conçus pour refléter l'identité culturelle ivoirienne tout en offrant une expérience utilisateur moderne.