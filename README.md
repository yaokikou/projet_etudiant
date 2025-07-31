# TECHNOVAServices

Plateforme web de services informatiques en architecture MVC (PHP)

## Fonctionnalités principales



## Parcours utilisateur

Lorsqu’un utilisateur arrive sur TECHNOVAServices, il découvre d’abord la page d’accueil présentant l’entreprise, les services phares, des statistiques et une section à propos. Il peut naviguer via le menu vers les : Services, Publications, Contact, Connexion. Dans l’espace Services, il accède à la liste des services proposés, chacun avec une image, un titre, une description et un bouton “Demander ce service”. Il peut alors faire une demande personnalisée via un formulaire dédié. Après inscription ou connexion, il accède à un espace personnel pour suivre ses demandes. Les administrateurs disposent d’un accès à un tableau de bord pour gérer l’ensemble du site. L’expérience est fluide, sécurisée et adaptée à tous les écrans.

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

- AKOUETE Yao (Project Lead)
- AYi Kossi Didier Vital 
- DA Silvera Espoir 
- AGBASSAH Débi Kékéli Judith 


Ce projet étant un projet de cursus aussi que pas trop parfaite pour un début, je suis ouvert pour toute proposition d'amélioration.
