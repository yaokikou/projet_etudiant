<?php
require_once __DIR__.'/includes/db.php';
include __DIR__.'/includes/header.php';

// Récupérer 4 services actifs maximum pour l'affichage
$stmt = $pdo->prepare('SELECT nom_service, description, image FROM Service WHERE actif = 1 ORDER BY date_ajout DESC LIMIT 4');
$stmt->execute();
$services = $stmt->fetchAll();
?>
<style>
    /* Reset et base */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    /* Section Hero */
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 800px;
        padding: 0 20px;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .hero-subtitle {
        font-size: 1.5rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }
    
    .cta-button {
        display: inline-block;
        background: #ff6b6b;
        color: white;
        padding: 15px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
    }
    
    .cta-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        background: #ff5252;
    }
    
    /* Section Services */
    .services-section {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 60px;
    }
    
    .section-title {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 1rem;
        font-weight: 700;
    }
    
    .section-subtitle {
        font-size: 1.2rem;
        color: #7f8c8d;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }
    
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .service-card {
        background: white;
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }
    
    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .service-image {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #667eea;
    }
    
    .service-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
    }
    
    .service-title {
        font-size: 1.5rem;
        color: #2c3e50;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .service-description {
        color: #7f8c8d;
        line-height: 1.6;
        margin-bottom: 20px;
        max-height: 80px;
        overflow: hidden;
    }
    
    .service-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    
    .service-link:hover {
        color: #764ba2;
    }
    
    /* Section Statistiques */
    .stats-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: white;
    }
    
    .stats-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 40px;
        margin-top: 60px;
    }
    
    .stat-card {
        text-align: center;
        padding: 40px 20px;
        background: rgba(255,255,255,0.1);
        border-radius: 20px;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-number {
        font-size: 3.5rem;
        font-weight: 700;
        color: #ff6b6b;
        margin-bottom: 10px;
        display: block;
    }
    
    .stat-label {
        font-size: 1.2rem;
        opacity: 0.9;
    }
    
    /* Section À propos */
    .about-section {
        padding: 80px 0;
        background: white;
    }
    
    .about-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        text-align: center;
    }
    
    .about-content {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 40px;
        border-radius: 20px;
        margin-bottom: 60px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .about-text {
        font-size: 1.2rem;
        line-height: 1.8;
        color: #2c3e50;
        margin-bottom: 30px;
    }
    
    .about-link {
        display: inline-block;
        background: #667eea;
        color: white;
        padding: 12px 25px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .about-link:hover {
        background: #764ba2;
        transform: translateY(-2px);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
        }
        
        .services-grid {
            grid-template-columns: 1fr;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<!-- Section Hero -->
<section class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">TECHNOVAServices</h1>
        <p class="hero-subtitle">Votre partenaire informatique de confiance pour des solutions innovantes et professionnelles</p>
        <a href="services.php" class="cta-button">Découvrir nos services</a>
    </div>
</section>

<!-- Section Services -->
<section class="services-section">
    <div class="section-header">
        <h2 class="section-title">Nos Services</h2>
        <p class="section-subtitle">Découvrez notre gamme complète de services informatiques adaptés à vos besoins</p>
    </div>
    
    <div class="services-grid">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <?php if (!empty($service['image'])): ?>
                        <img src="assets/img/<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['nom_service']) ?>" class="service-image">
                    <?php else: ?>
                        <div class="service-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                    <?php endif; ?>
                    <h3 class="service-title"><?= htmlspecialchars($service['nom_service']) ?></h3>
                    <p class="service-description"><?= htmlspecialchars(strlen($service['description']) > 120 ? substr($service['description'], 0, 120) . '...' : $service['description']) ?></p>
                    <a href="services.php" class="service-link">En savoir plus →</a>
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
                <a href="services.php" class="service-link">En savoir plus →</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <h3 class="service-title">Réseaux & Sécurité</h3>
                <p class="service-description">Configuration, sécurisation et maintenance de vos infrastructures réseau.</p>
                <a href="services.php" class="service-link">En savoir plus →</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-database"></i>
                </div>
                <h3 class="service-title">Base de Données</h3>
                <p class="service-description">Conception, optimisation et maintenance de vos bases de données.</p>
                <a href="services.php" class="service-link">En savoir plus →</a>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h3 class="service-title">Maintenance Informatique</h3>
                <p class="service-description">Maintenance préventive et curative de vos équipements informatiques.</p>
                <a href="services.php" class="service-link">En savoir plus →</a>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="text-align: center; margin-top: 50px;">
        <a href="services.php" class="cta-button">Voir tous nos services</a>
    </div>
</section>

<!-- Section Statistiques -->
<section class="stats-section">
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
<section class="about-section">
    <div class="about-container">
        <div class="about-content">
            <p class="about-text">
                Chez TECHNOVAServices, nous nous engageons à fournir des solutions informatiques de haute qualité. 
                Notre équipe d'experts est dédiée à la réussite de vos projets, en vous offrant un service 
                personnalisé et adapté à vos besoins spécifiques.
            </p>
            <a href="contact.php" class="about-link">Nous contacter</a>
        </div>
    </div>
</section>

<?php include __DIR__.'/includes/footer.php'; ?> 