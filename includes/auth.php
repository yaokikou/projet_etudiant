<?php
//Fonction pour la gestion de l'authentification et des rôles des utilisateurs

//Fonction pour vérifier si l'utilisateur est connecté

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['nom_utilisateur']);
}

// Fonction pour vérifier si l'utilisateur a un rôle spécifique

function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    
    global $pdo;
    $stmt = $pdo->prepare('SELECT role FROM Utilisateur WHERE id = ? AND actif = 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    return $user && $user['role'] === $role;
}

// Fonction pour vérifier si l'utilisateur est admin

function isAdmin() {
    return hasRole('admin');
}

// Fonction pour vérifier si l'utilisateur est modérateur ou admin

function isModerator() {
    return hasRole('moderateur') || hasRole('admin');
}

// Fonction pour la redirection vers la page de connexion si ce n'est pas fait 

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: connexion.php?message=connectez-vous');
        exit;
    }
}

//Fonction pour rediriger l'utilisateur vers l'acceuil si ce n'est pas admin 

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../index.php?message=acces-refuse');
        exit;
    }
}

//Fonction pour rediriger vers l'accueil si ce n'est pas un moderateur

function requireModerator() {
    requireLogin();
    if (!isModerator()) {
        header('Location: ../index.php?message=acces-refuse');
        exit;
    }
}

//Fonction pour recuperer les informations des utilisateurs connecté

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $stmt = $pdo->prepare('SELECT id, nom_utilisateur, email, role, date_creation FROM Utilisateur WHERE id = ? AND actif = 1');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

//Fonction pour determiner la page de vue apres connexion selon les roles 

function getRedirectAfterLogin($user) {
    switch ($user['role']) {
        case 'admin':
        case 'moderateur':
            return 'admin/';
        default:
            return 'index.php';
    }
}
