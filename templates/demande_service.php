
<section>
    <div class="connexion-container">
        <h1>Demande de service</h1>
        <p class="subtitle">Décrivez votre besoin et nous vous répondrons rapidement</p>
        
        <?php if ($message): ?>
            <div class="connexion-message<?= $messageType === 'error' ? ' error' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <form method="post" id="demandeForm">
            <div class="form-group">
                <label for="service_id">Service :</label>
                <select id="service_id" name="service_id">
                    <option value="">-- Choisir un service --</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($s['id'] == $service_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nom_service']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="description">Description de la demande :</label>
                <textarea id="description" name="description" rows="5" placeholder="Décrivez votre besoin en détail..."></textarea>
            </div>
            
            <button type="submit" class="connexion-btn">
                <i class="fas fa-paper-plane"></i> Envoyer la demande
            </button>
        </form>
        
        <a href="index.php?action=services" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour à la page des services
        </a>
    </div>
</section>

<script src="../assets/js/demande_service.js"></script>

