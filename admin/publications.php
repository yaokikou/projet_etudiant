<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['nom_utilisateur'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}
// Ajout publication
if (isset($_POST['ajouter'])) {
    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    if ($titre && $contenu && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid('pub_') . '_' . basename($_FILES['image']['name']);
        $img_path = '../assets/img/' . $img_name;
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($img_ext, $allowed)) {
            move_uploaded_file($_FILES['image']['tmp_name'], $img_path);
            $stmt = $pdo->prepare('INSERT INTO Publication (titre, contenu, image) VALUES (?, ?, ?)');
            $stmt->execute([$titre, $contenu, $img_name]);
        }
    }
    header('Location: publications.php');
    exit;
}
// Activation/désactivation
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->query('UPDATE Publication SET actif = 1 - actif WHERE id = ' . $id);
}
// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM Publication WHERE id = ?')->execute([$id]);
}
// Modification
if (isset($_POST['modifier'])) {
    $id = (int)$_POST['id'];
    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $img_name = $_POST['image_actuelle'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid('pub_') . '_' . basename($_FILES['image']['name']);
        $img_path = '../assets/img/' . $img_name;
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($img_ext, $allowed)) {
            move_uploaded_file($_FILES['image']['tmp_name'], $img_path);
        }
    }
    if ($titre && $contenu && $img_name) {
        $stmt = $pdo->prepare('UPDATE Publication SET titre=?, contenu=?, image=? WHERE id=?');
        $stmt->execute([$titre, $contenu, $img_name, $id]);
    }
    header('Location: publications.php');
    exit;
}
// Liste publications
$stmt = $pdo->query('SELECT * FROM Publication ORDER BY date_publication DESC');
$publications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Publications</title>
    <link rel="stylesheet" href="/site-informatique/assets/css/style.css">
</head>
<body>
<header><h1>Gestion des publications</h1></header>
<main>
    <h2>Ajouter une publication</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="titre" placeholder="Titre" required>
        <textarea name="contenu" placeholder="Contenu" required></textarea>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>
    <h2>Liste des publications</h2>
    <table>
        <tr><th>ID</th><th>Titre</th><th>Contenu</th><th>Image</th><th>Date</th><th>Actif</th><th>Actions</th></tr>
        <?php foreach ($publications as $p): ?>
        <tr>
            <form method="post" enctype="multipart/form-data">
                <td><?= $p['id'] ?><input type="hidden" name="id" value="<?= $p['id'] ?>"></td>
                <td><input type="text" name="titre" value="<?= htmlspecialchars($p['titre']) ?>" required></td>
                <td><textarea name="contenu" required><?= htmlspecialchars($p['contenu']) ?></textarea></td>
                <td>
                    <?php if ($p['image']): ?>
                        <img src="../assets/img/<?= htmlspecialchars($p['image']) ?>" alt="Image publication" style="max-width:80px;max-height:80px;vertical-align:middle;">
                    <?php endif; ?>
                </td>
                <td><?= $p['date_publication'] ?></td>
                <td><?= $p['actif'] ? 'Oui' : 'Non' ?> <a href="?toggle=<?= $p['id'] ?>">Changer</a></td>
                <td>
                    <input type="hidden" name="image_actuelle" value="<?= htmlspecialchars($p['image']) ?>">
                    <input type="file" name="image" accept="image/*">
                    <button type="submit" name="modifier">Modifier</button>
                    <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Supprimer cette publication ?')">Supprimer</a>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="index.php">Retour au dashboard</a>
</main>
</body>
</html> 