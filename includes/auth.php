<?php
/**
 * Fonctions d'authentification et de gestion des rôles
 */

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['nom_utilisateur']);
}

/**
 * Vérifie si l'utilisateur a un rôle spécifique
 */
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

/**
 * Vérifie si l'utilisateur est admin
 */
function isAdmin() {
    return hasRole('admin');
}

/**
 * Vérifie si l'utilisateur est modérateur ou admin
 */
function isModerator() {
    return hasRole('moderateur') || hasRole('admin');
}

/**
 * Redirige vers la page de connexion si non connecté
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: connexion.php?message=connectez-vous');
        exit;
    }
}

/**
 * Redirige vers la page d'accueil si l'utilisateur n'a pas les droits admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../index.php?message=acces-refuse');
        exit;
    }
}

/**
 * Redirige vers la page d'accueil si l'utilisateur n'a pas les droits modérateur
 */
function requireModerator() {
    requireLogin();
    if (!isModerator()) {
        header('Location: ../index.php?message=acces-refuse');
        exit;
    }
}

/**
 * Récupère les informations de l'utilisateur connecté
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $stmt = $pdo->prepare('SELECT id, nom_utilisateur, email, role, date_creation FROM Utilisateur WHERE id = ? AND actif = 1');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Détermine la page de redirection après connexion selon le rôle
 */
function getRedirectAfterLogin($user) {
    switch ($user['role']) {
        case 'admin':
        case 'moderateur':
            return 'admin/';
        default:
            return 'index.php';
    }
}
