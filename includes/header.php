<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entreprise Informatique</title>
    <link rel="stylesheet" href="/site-informatique/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="/site-informatique/assets/js/main.js" defer></script>
</head>
<body>
<header>
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 2em;">
        <div class="logo" style="font-size: 1.8em; font-weight: bold; color: #fff; text-decoration: none;">
            <a href="index.php" style="color: #fff; text-decoration: none;">
                <span style="color: #0074d9;">TECHNOVA</span><span style="color: #fff;">Services</span>
            </a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="publications.php">Publications</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php session_start(); if (isset($_SESSION['user_id'])): ?>
                    <li><a href="mes_demandes.php">Mes demandes</a></li>
                    <li><a href="includes/logout.php">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="inscription.php">Inscription</a></li>
                    <li><a href="connexion.php">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<main> 