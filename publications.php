<?php
require_once __DIR__.'/includes/db.php';
include __DIR__.'/includes/header.php';

$stmt = $pdo->prepare('SELECT titre, contenu, image, date_publication, date_modification FROM Publication WHERE actif = 1 ORDER BY date_publication DESC');
$stmt->execute();
$publications = $stmt->fetchAll();
?>


<?php include __DIR__.'/includes/footer.php'; ?>  