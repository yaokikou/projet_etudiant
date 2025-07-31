</main>
<footer class="footer">
    <div class="footer-container">
        <!-- Section À propos -->
        <div class="footer-section">
            <h3><i class="fas fa-building"></i> À propos</h3>
            <p>TECHNOVAServices est votre partenaire informatique de confiance, spécialisé dans les solutions technologiques innovantes et professionnelles.</p>
        </div>

        <!-- Section Services -->
        <div class="footer-section">
            <h3><i class="fas fa-cogs"></i> Nos Services</h3>
            <ul>
                <li><a href="index.php?action=services"><i class="fas fa-code"></i> Développement Web</a></li>
                <li><a href="index.php?action=services"><i class="fas fa-network-wired"></i> Réseaux & Sécurité</a></li>
                <li><a href="index.php?action=services"><i class="fas fa-database"></i> Base de Données</a></li>
                <li><a href="index.php?action=services"><i class="fas fa-tools"></i> Maintenance Informatique</a></li>
                <li><a href="index.php?action=services"><i class="fas fa-mobile-alt"></i> Applications Mobiles</a></li>
            </ul>
        </div>

        <!-- Section Liens rapides -->
        <div class="footer-section">
            <h3><i class="fas fa-link"></i> Liens rapides</h3>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="index.php?action=services"><i class="fas fa-cogs"></i> Services</a></li>
                <li><a href="index.php?action=publications"><i class="fas fa-newspaper"></i> Publications</a></li>
                <li><a href="index.php?action=contact"><i class="fas fa-envelope"></i> Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): 
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
                        <li><a href="index.php?action=mes_demandes"><i class="fas fa-clipboard-list"></i> Mes demandes</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'moderateur')): ?>
                        <li><a href="admin/"><i class="fas fa-cog"></i> Administration</a></li>
                    <?php endif; ?>
                <?php else: ?>
                    <li><a href="index.php?action=connexion"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
                    <li><a href="index.php?action=inscription"><i class="fas fa-user-plus"></i> Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Section Contact -->
        <div class="footer-section">
            <h3><i class="fas fa-address-card"></i> Contact</h3>
            <div class="contact-info">
                <i class="fas fa-map-marker-alt"></i>
                <span>123 Rue Bè-Kpota,Lomé Togo </span>
            </div>
            <div class="contact-info">
                <i class="fas fa-phone"></i>
                <span>+228 79 56 19 30</span>
            </div>
            <div class="contact-info">
                <i class="fas fa-envelope"></i>
                <span>technovaservices@gmail.com</span>
            </div>
            <div class="contact-info">
                <i class="fas fa-clock"></i>
                <span>Lundi-Vendredi: 9h-17h</span>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> TECHNOVAServices. Tous droits réservés. | 
        <a href="#">Politique de confidentialité</a></p>
    </div>
</footer>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 