<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['nom_utilisateur'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}
// Ajout service
if (isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom_service'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($nom && $desc && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid('service_') . '_' . basename($_FILES['image']['name']);
        $img_path = '../assets/img/' . $img_name;
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($img_ext, $allowed)) {
            move_uploaded_file($_FILES['image']['tmp_name'], $img_path);
            $stmt = $pdo->prepare('INSERT INTO Service (nom_service, description, image) VALUES (?, ?, ?)');
            $stmt->execute([$nom, $desc, $img_name]);
        }
    }
    header('Location: services.php');
    exit;
}
// Activation/désactivation
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->query('UPDATE Service SET actif = 1 - actif WHERE id = ' . $id);
    header('Location: services.php');
    exit;
}
// Modification
if (isset($_POST['modifier'])) {
    $id = (int)$_POST['id'];
    $nom = trim($_POST['nom_service'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $img_name = $_POST['image_actuelle'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid('service_') . '_' . basename($_FILES['image']['name']);
        $img_path = '../assets/img/' . $img_name;
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (in_array($img_ext, $allowed)) {
            move_uploaded_file($_FILES['image']['tmp_name'], $img_path);
        }
    }
    if ($nom && $desc && $img_name) {
        $stmt = $pdo->prepare('UPDATE Service SET nom_service=?, description=?, image=? WHERE id=?');
        $stmt->execute([$nom, $desc, $img_name, $id]);
    }
    header('Location: services.php');
    exit;
}
// Suppression service
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM Service WHERE id = ?')->execute([$id]);
    header('Location: services.php');
    exit;
}
// Liste services
$stmt = $pdo->query('SELECT * FROM Service ORDER BY date_ajout DESC');
$services = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Services</title>
    <link rel="stylesheet" href="/site-informatique/assets/css/style.css">
</head>
<body>
<header><h1>Gestion des services</h1></header>
<main>
    <h2>Ajouter un service</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="nom_service" placeholder="Nom du service" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>
    <h2>Liste des services</h2>
    <table>
        <tr><th>ID</th><th>Nom</th><th>Description</th><th>Image</th><th>Actif</th><th>Actions</th></tr>
        <?php foreach ($services as $s): ?>
        <tr>
            <form method="post" enctype="multipart/form-data">
                <td><?= $s['id'] ?><input type="hidden" name="id" value="<?= $s['id'] ?>"></td>
                <td><input type="text" name="nom_service" value="<?= htmlspecialchars($s['nom_service']) ?>" required></td>
                <td><textarea name="description" required><?= htmlspecialchars($s['description']) ?></textarea></td>
                <td>
                    <?php if ($s['image']): ?>
                        <img src="../assets/img/<?= htmlspecialchars($s['image']) ?>" alt="Image service" style="max-width:80px;max-height:80px;vertical-align:middle;">
                    <?php endif; ?>
                </td>
                <td><?= $s['actif'] ? 'Oui' : 'Non' ?> <a href="?toggle=<?= $s['id'] ?>">Changer</a></td>
                <td>
                    <input type="hidden" name="image_actuelle" value="<?= htmlspecialchars($s['image']) ?>">
                    <input type="file" name="image" accept="image/*">
                    <button type="submit" name="modifier">Modifier</button>
                    <a href="?delete=<?= $s['id'] ?>" onclick="return confirm('Supprimer ce service ?')">Supprimer</a>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="index.php">Retour au dashboard</a>
</main>
</body>
</html> 