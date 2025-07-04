<?php
require_once __DIR__.'/includes/db.php';
include __DIR__.'/includes/header.php';

$stmt = $pdo->prepare('SELECT titre, contenu, image, date_publication, date_modification FROM Publication WHERE actif = 1 ORDER BY date_publication DESC');
$stmt->execute();
$publications = $stmt->fetchAll();
?>
<section>
    <h1>Publications</h1>
    <?php foreach ($publications as $pub): ?>
        <article class="publication" style="display:flex;align-items:center;gap:1em;margin-bottom:2em;">
            <img src="assets/img/<?= htmlspecialchars($pub['image']) ?>" alt="Image publication" style="max-width:120px;max-height:120px;border-radius:8px;">
            <div>
                <h2><?= htmlspecialchars($pub['titre']) ?></h2>
                <div><?= nl2br(htmlspecialchars($pub['contenu'])) ?></div>
                <div style="font-size:0.95em;color:#666;margin-top:0.5em;">
                    Publié le <?= htmlspecialchars($pub['date_publication']) ?>
                    <?php if ($pub['date_modification'] && $pub['date_modification'] !== $pub['date_publication']): ?>
                        | Modifié le <?= htmlspecialchars($pub['date_modification']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</section>
<?php include __DIR__.'/includes/footer.php'; ?> 