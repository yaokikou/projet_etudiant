# Site Web Dynamique - Entreprise de Services Informatiques

## Structure du projet

- `index.php` : Page d'accueil
- `services.php` : Liste des services
- `inscription.php` : Création de compte utilisateur
- `connexion.php` : Authentification utilisateur
- `contact.php` : Formulaire de contact
- `publications.php` : Affichage des publications
- `admin/` : Tableau de bord d'administration sécurisé
- `includes/` : Fichiers PHP réutilisables (connexion BDD, header, footer, etc.)
- `assets/` : Fichiers statiques (CSS, JS, images)
- `sql/` : Script SQL de création de la base

## Fonctionnalités principales
- Gestion des utilisateurs (inscription, connexion, suppression par admin)
- Gestion dynamique des services et publications
- Tableau de bord admin sécurisé
- Stockage sécurisé des mots de passe (hash)
- Formulaire de contact avec stockage en base

## Technologies
- PHP, MySQL, HTML, CSS, JavaScript

---

**Déploiement facile** : Placez le dossier sur votre serveur PHP/MySQL, importez le script SQL, configurez la connexion dans `includes/db.php`. 