<?php
// Inclure les fonctions d'authentification

require_once __DIR__.'/auth.php';
require_once __DIR__.'/db.php';

//Démarrer la session seulement si elle n'est pas déjà active

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php" style="font-size: 1.8em; font-weight: bold;">
                <span style="color: #3498db;">TECHNOVA</span><span style="color: #fff;">Services</span>
            </a>
            
            <!-- Bouton hamburger pour mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Menu de navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=accueil">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=publications">Publications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=contact">Contact</a>
                    </li>
                    <?php 
                    if (isset($_SESSION['user_id'])): 
                        $current_user = getCurrentUser();
                        
                        // Vérifier si l'utilisateur a des demandes de service

                        if ($pdo) {
                            $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM DemandeService WHERE utilisateur_id = ?');
                            $stmt->execute([$_SESSION['user_id']]);
                            $has_demandes = $stmt->fetch()['count'] > 0;
                        } else {
                            $has_demandes = false;
                        }
                    ?>
                        <?php if ($has_demandes): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?action=mes_demandes">Demandes</a>
                            </li>
                        <?php endif; ?>
                        <?php if (isAdmin() || isModerator()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/" >Administration</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="includes/logout.php">Déconnexion</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=connexion">Connexion</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main> 