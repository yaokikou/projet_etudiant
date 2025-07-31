

<!-- Section Hero -->
<section class="hero-section fullwidth-section">
    <div class="hero-content">
        <h1 class="hero-title">TECHNOVAServices</h1>
        <p class="hero-subtitle">Votre partenaire informatique de confiance pour des solutions innovantes et professionnelles</p>
        <a href="index.php?action=services" class="cta-button">Découvrir nos services</a>
    </div>
</section>

<!-- Section Services -->
<section class="services-section fullwidth-section">
    <div class="section-header">
        <h2 class="section-title">Nos Services</h2>
        <p class="section-subtitle">Découvrez notre gamme complète de services informatiques adaptés à vos besoins</p>
    </div>
    <div class="services-grid">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <?php if (!empty($service['image']) && file_exists(__DIR__ . '/../assets/img/' . $service['image'])): ?>
                        <img src="assets/img/<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['nom_service']) ?>" class="service-image">
                    <?php else: ?>
                        <div class="service-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                    <?php endif; ?>
                    <h3 class="service-title"><?= htmlspecialchars($service['nom_service']) ?></h3>
                    <p class="service-description"><?= htmlspecialchars(strlen($service['description']) > 120 ? substr($service['description'], 0, 120) . '...' : $service['description']) ?></p>
                    <a href="index.php?action=services" class="service-link">En savoir plus →</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Services par défaut si aucun service en base -->
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-code"></i>
                </div>
                <h3 class="service-title">Développement Web</h3>
                <p class="service-description">Création de sites web modernes et responsives adaptés à vos besoins spécifiques.</p>
                <a href="index.php?action=services" class="service-link">En savoir plus →</a>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h3 class="service-title">Réseaux & Sécurité</h3>
                <p class="service-description">Configuration, sécurisation et maintenance de vos infrastructures réseau.</p>
                <a href="index.php?action=services" class="service-link">En savoir plus →</a>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-database"></i>
                </div>
                <h3 class="service-title">Base de Données</h3>
                <p class="service-description">Conception, optimisation et maintenance de vos bases de données.</p>
                <a href="index.php?action=services" class="service-link">En savoir plus →</a>
            </div>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h3 class="service-title">Maintenance Informatique</h3>
                <p class="service-description">Maintenance préventive et curative de vos équipements informatiques.</p>
                <a href="index.php?action=services" class="service-link">En savoir plus →</a>
            </div>
        <?php endif; ?>
    </div>
    <div style="text-align: center; margin-top: 50px;">
        <a href="index.php?action=services" class="cta-button">Voir tous nos services</a>
    </div>
</section>

<!-- Section Statistiques -->
<section class="stats-section fullwidth-section">
    <div class="stats-container">
        <div class="section-header">
            <h2 class="section-title" style="color: white;">Pourquoi nous choisir ?</h2>
            <p class="section-subtitle" style="color: rgba(255,255,255,0.8);">Des chiffres qui parlent d'eux-mêmes</p>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number">200+</span>
                <div class="stat-label">Clients satisfaits</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">15</span>
                <div class="stat-label">Experts techniques</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">500+</span>
                <div class="stat-label">Projets réalisés</div>
            </div>
            <div class="stat-card">
                <span class="stat-number">12</span>
                <div class="stat-label">Ans d'expérience</div>
            </div>
        </div>
    </div>
</section>

<!-- Section À propos -->
<section class="about-section fullwidth-section">
    <div class="about-container">
        <div class="about-content">
            <p class="about-text">
                Chez TECHNOVAServices, nous nous engageons à fournir des solutions informatiques de haute qualité.
                Notre équipe d'experts est dédiée à la réussite de vos projets, en vous offrant un service
                personnalisé et adapté à vos besoins spécifiques.
            </p>
            <a href="index.php?action=contact" class="about-link">Nous contacter</a>
        </div>
    </div>
</section>

