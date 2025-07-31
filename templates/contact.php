<?php $title = 'TECHNOVAService - Contact'; 
$css = '../assets/css/contact.css'; 
?>

<?php ob_start();?>

<section>
    <div class="connexion-container">
        <h1>Contact</h1>
        <p class="subtitle">Nous sommes là pour vous aider. N'hésitez pas à nous contacter !</p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-info">
                <i class="fas fa-user-check"></i> Vous êtes connecté en tant que <strong><?= htmlspecialchars($nom) ?></strong>
            </div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="connexion-message<?= (strpos($message, 'bien été envoyé') === false) ? ' error' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
    
        <form method="post">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" <?= isset($_SESSION['user_id']) ? 'readonly' : '' ?>>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>"  <?= isset($_SESSION['user_id']) ? 'readonly' : '' ?>>
            </div>
            <div class="form-group">
                <label for="message">Message :</label>
                <textarea id="message" name="message"  placeholder="Décrivez votre demande ou question..."></textarea>
            </div>
            <button type="submit" class="connexion-btn">
                <i class="fas fa-paper-plane"></i> Envoyer le message
            </button>
        </form>
        
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div style="margin-top: 20px; text-align: center; color: #7f8c8d; font-size: 0.9rem;">
                <p>Vous avez déjà un compte ? <a href="connexion.php" style="color: #3498db; text-decoration: none;">Connectez-vous</a> pour une expérience optimale.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script src="../assets/js/contact.js"></script>

<?php $content = ob_get_clean(); ?>

<?php require('layout.php'); ?>