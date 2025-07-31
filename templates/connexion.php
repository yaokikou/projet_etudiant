<?php
$title = "TECHNOVAService - Connexion";
$css = '../assets/css/conexion.css';
?>
<?php ob_start(); ?>
<section>
    <div class="connexion-container">
        <h1>Connexion</h1>
        <?php if ($message): ?><div class="connexion-message"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <form method="post" id="connexion-form">
            <div class="form-group">
                <label for="nom_utilisateur">Nom d'utilisateur :</label>
                <input type="text" id="nom_utilisateur" name="nom_utilisateur" >
            </div>
            <div class="form-group">
                <label for="motdepasse">Mot de passe :</label>
                <input type="password" id="motdepasse" name="motdepasse" >
            </div>
            <button type="submit" class="connexion-btn">Se connecter</button>
            <p>Déja s'inscrire ?<a href="inscription.php"> Inscrivez-vous ! <style>
                a{
                    color: #3498db;
                    text-decoration: none;
                }
            </style></a></p>
        </form>
    </div>
    <script src="../assets/js/connexion.js"></script>
</section>

<?php $content = ob_get_clean(); ?>

<?php require('layout.php'); ?>