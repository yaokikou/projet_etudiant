<?php
$title = "TECHNOVAServices - Inscription";
$css = "../assets/css/inscription.css";
?>
<?php ob_start(); ?>
<section>
    <div class="connexion-container">
        <h1>Inscription</h1>
        <?php if ($message): ?><div class="connexion-message"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <form method="post" id="register-container">
            <div class="form-group">
                <label for="nom_utilisateur">Nom d'utilisateur :</label>
                <input type="text" id="nom_utilisateur" name="nom_utilisateur" >
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" >
            </div>
            <div class="form-group">
                <label for="motdepasse">Mot de passe :</label>
                <input type="password" id="motdepasse" name="motdepasse" >
            </div>
            <div class="form-group">
                <label for="motdepasse2">Confirmer le mot de passe :</label>
                <input type="password" id="motdepasse2" name="motdepasse2">
            </div>
            <button type="submit" class="connexion-btn">S'inscrire</button>
            <p id="message" style="color: red;text-align: center;"></p>
            <p>Déja s'inscrire ?<a href="connexion.php"> Conectez-vous !<style>           
                a{
                    color: #3498db;
                    text-decoration: none;
                }
            </style></a></p>
        </form>
    </div>
    <script src="../assets/js/inscription.js"></script>
</section>

<?php $content = ob_get_clean(); ?>

<?php require('layout.php'); ?>