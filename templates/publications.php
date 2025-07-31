
<section>
    <h1>Publications</h1>
    <?php if (empty($publications)): ?>
        <div style="text-align: center; padding: 3rem; color: #666;">
            <i class="fas fa-newspaper" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <h2>Aucune publication disponible</h2>
            <p>Nos publications seront bientôt disponibles.</p>
        </div>
    <?php else: ?>
        <?php foreach ($publications as $pub): ?>
            <article class="publication">
                <div class="publication-image">
                    <?php if ($pub['image'] && file_exists(__DIR__ . '/../assets/img/' . $pub['image'])): ?>
                        <img src="assets/img/<?= htmlspecialchars($pub['image']) ?>" alt="Image publication">
                    <?php else: ?>
                        <div class="default-image">
                            <i class="fas fa-newspaper" style="font-size:3em;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="publication-content">
                    <h2><?= htmlspecialchars($pub['titre']) ?></h2>
                    <div class="content"><?= nl2br(htmlspecialchars($pub['contenu'])) ?></div>
                    <div class="date-info">
                        <i class="fas fa-calendar-alt"></i> Publié le <?= htmlspecialchars($pub['date_publication']) ?>
                        <?php if ($pub['date_modification'] && $pub['date_modification'] !== $pub['date_publication']): ?>
                            <br><i class="fas fa-edit"></i> Modifié le <?= htmlspecialchars($pub['date_modification']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
