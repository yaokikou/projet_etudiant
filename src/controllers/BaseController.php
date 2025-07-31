<?php

//Classe de base pour tout les controlleur pour centraliser les fonctionnalités communes

class BaseController {
    protected $pdo;
    protected $title;
    protected $css;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    //Fonction pour gerer le transfert des données à la vue de chaque controller

    protected function render($viewName, $data = []) {
        // Rendre les variables disponibles dans la vue

        extract($data);
    
        // Sauvegarde les contenu de la vue 

        ob_start();
        require __DIR__ . "/../../templates/{$viewName}.php";
        $content = ob_get_clean();
    
        // Transmet les données à la vue principale autrement le header et le footer

        $title = $this->title ?? 'TECHNOVAServices';
        $css = $this->css ?? '/site-informatique/assets/css/style.css';
    
        // Affiche le layout principal
        require __DIR__ . '/../../templates/layout.php';
    }
    
    // Fonction pour rediriger vers une autre page

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    // Fonction pour vérifier si l'utilisateur est connecté

    protected function requireLogin() {
        if (!isLoggedIn()) {
            $this->redirect('index.php?action=connexion&message=connectez-vous');
        }
    }
    
    
    // Fonction pour vérifier si l'utilisateur est un administrateur
    
    protected function requireAdmin() {
        $this->requireLogin();
        if (!isAdmin()) {
            $this->redirect('index.php?message=acces-refuse');
        }
    }
}
?> 