
<section class="services-container">
    <div class="services-header">
        <h1>Nos Services</h1>
        <p class="subtitle">Découvrez nos services informatiques professionnels</p>
    </div>

    <?php if (empty($services)): ?>
        <div class="empty-state">
            <i class="fas fa-cogs"></i>
            <h2>Aucun service disponible</h2>
            <p>Nos services seront bientôt disponibles.</p>
        </div>
    <?php else: ?>
        <div class="services">
            <?php foreach ($services as $service): ?>
                <div class="service">
                    <div class="service-image-section">
                        <?php if ($service['image'] && file_exists(__DIR__ . '/../assets/img/' . $service['image'])): ?>
                            <img src="assets/img/<?= htmlspecialchars($service['image']) ?>"
                                alt="<?= htmlspecialchars($service['nom_service']) ?>"
                                class="service-image">
                        <?php else: ?>
                            <div class="service-image-placeholder">
                                <i class="fas fa-cog"></i>
                            </div>
                        <?php endif; ?>
                        <a href="index.php?action=demande_service&service_id=<?= urlencode($service['id']) ?>" class="btn-service">
                            <i class="fas fa-paper-plane"></i> Demander ce service
                        </a>
                    </div>

                    <div class="service-content">
                        <h2 class="service-title"><?= htmlspecialchars($service['nom_service']) ?></h2>
                        <p class="service-description"><?= nl2br(htmlspecialchars($service['description'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
