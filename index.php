<?php
//Le routeur principal du site
session_start();

// Inclusion des fichiers de base
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

// Définition des routes disponibles
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

// Récupération  de l'action demandé par l'utilsateur
// Par défaut, on charge la page d'accueil

$action = $_GET['action'] ?? 'accueil';

// Vérifier si la route existe

if (!array_key_exists($action, $routes)) {
    $action = 'accueil'; // Route par défaut
}

// On charge le contrôleur correspondant à l'action demandée

$controllerName = $routes[$action];
$controllerFile = __DIR__ . "/src/controllers/{$controllerName}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Creation de l'instance du contrôleur

    $controller = new $controllerName();
    $controller->index();
} else {
    // Le cas ou la page n'existe pas on affiche une ereur 404

    http_response_code(404);
    echo "Page non trouvée";
}
?>