<?php
// Router principal MVC
session_start();

// Inclure les fichiers de base
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

// Définir les routes disponibles
$routes = [
    'accueil' => 'HomepageController',
    'services' => 'ServicesController', 
    'publications' => 'PublicationsController',
    'contact' => 'ContactController',
    'connexion' => 'ConnexionController',
    'inscription' => 'InscriptionController',
    'demande_service' => 'DemandeServiceController',
    'mes_demandes' => 'MesDemandesController'
];

// Récupérer l'action demandée
$action = $_GET['action'] ?? 'accueil';

// Vérifier si la route existe
if (!array_key_exists($action, $routes)) {
    $action = 'accueil'; // Route par défaut
}

// Charger le controller correspondant
$controllerName = $routes[$action];
$controllerFile = __DIR__ . "/src/controllers/{$controllerName}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Créer une instance du controller et appeler la méthode principale
    $controller = new $controllerName();
    $controller->index();
} else {
    // Page d'erreur 404
    http_response_code(404);
    echo "Page non trouvée";
}
?>