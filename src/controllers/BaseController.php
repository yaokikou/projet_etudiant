<?php
/**
 * Classe de base pour tous les controllers
 */
class BaseController {
    protected $pdo;
    protected $title;
    protected $css;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    /**
     * Méthode pour rendre une vue
     */
    protected function render($viewName, $data = []) {
        // Rendre les variables disponibles dans la vue
        extract($data);
    
        // Capture le contenu de la vue
        ob_start();
        require __DIR__ . "/../../templates/{$viewName}.php";
        $content = ob_get_clean();
    
        // Titre et CSS personnalisés si définis
        $title = $this->title ?? 'TECHNOVAServices';
        $css = $this->css ?? '/site-informatique/assets/css/style.css';
    
        // Affiche le layout principal
        require __DIR__ . '/../../templates/layout.php';
    }
    
    /**
     * Méthode pour rediriger
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Méthode pour vérifier si l'utilisateur est connecté
     */
    protected function requireLogin() {
        if (!isLoggedIn()) {
            $this->redirect('index.php?action=connexion&message=connectez-vous');
        }
    }
    
    /**
     * Méthode pour vérifier les droits admin
     */
    protected function requireAdmin() {
        $this->requireLogin();
        if (!isAdmin()) {
            $this->redirect('index.php?message=acces-refuse');
        }
    }
}
?> 