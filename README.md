# TECHNOVAServices

Plateforme web de services informatiques en architecture MVC (PHP)

## Fonctionnalités principales

- **Accueil** : Présentation de l'entreprise, mise en avant des services, statistiques, section à propos.
- **Espace de services** : Affichage dynamique des services avec image, description, et bouton de demande.
- **Demande de service** : Formulaire pour demander un service spécifique, suivi des demandes par l'utilisateur.

- **Gestion des utilisateurs** : Inscription, connexion, gestion des rôles (utilisateur, admin, etc.).
- **Publications** : Affichage d'actualités ou d'articles informatiques.
-  **Contact** : Formulaire de contact pour joindre l'équipe.
- **Administration** : Gestion centralisée des utilisateurs, services, publications, demandes, et contacts (via contrôleurs dédiés).

## Structure du projet

```
/ (racine)
│── admin                    #dashboard admin pour la gestion des services et autres 
│── assets/
│   ├── css/                 # Tout les styles css coté site(home.css, services.css, ...)
│   ├── img/                 # Dossier qui stocke tout les images uploader
│   └── js/                  # Scripts JS
├── includes/                # Authentification, connexion base de donnée, header/footer
├── src/
│   ├── controllers/         # Contrôleurs MVC (HomepageController, ServicesController, ...)
│   └── model/               # Modèles (accès base de données)
├── templates/               # Vues (homepage.php, services.php, ...)
|                
|── index.php                # Routeur principal MVC
├── sql/                     # Scripts SQL pour la base
└── README.md
```

## Technologies utilisées

- **Backend** : PHP,POO, architecture MVC, PDO (accès base de données sécurisé)
- **Frontend** : HTML5, CSS (Flexbox, responsive design), Bootstrap 5, FontAwesome(cloudfared)
- **Base de données** : MySQL
- **Autres** : JavaScript (pour l'interactivité), scripts SQL pour l'initialisation

## Contributeurs

- AKOUETE Yao (owner)
- AYi Kossi Didier Vital 
- DA Silvera Espoir 
- AGBASSAH Débi Kékéli Judith 


Ce projet étant un projet de cursus aussi que pas trop parfaite pour un début, je suis ouvert pour toute proposition d'amélioration.
