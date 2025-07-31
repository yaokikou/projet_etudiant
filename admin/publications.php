<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['nom_utilisateur'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}

// Inclusion de la connexion à la base de données
require_once '../includes/db.php';

// Récupération des informations utilisateur connecté
try {
    // Informations de l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT nom_utilisateur, email FROM Utilisateur WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_user = $stmt->fetch();
    
    // Statistiques des publications
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM Publication");
    $total_publications = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as actifs FROM Publication WHERE actif = 1");
    $publications_actifs = $stmt->fetch()['actifs'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as inactifs FROM Publication WHERE actif = 0");
    $publications_inactifs = $stmt->fetch()['inactifs'];
    
    // Liste publications
    $stmt = $pdo->query('SELECT * FROM Publication ORDER BY date_publication DESC');
    $publications = $stmt->fetchAll();
    
} catch (PDOException $e) {
    // En cas d'erreur, utiliser des valeurs par défaut
    $current_user = ['nom_utilisateur' => $_SESSION['nom_utilisateur'], 'email' => ''];
    $total_publications = 0;
    $publications_actifs = 0;
    $publications_inactifs = 0;
    $publications = [];
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
            $_SESSION['success_message'] = "Publication ajoutée avec succès.";
        }
    }
    header('Location: publications.php');
    exit;
}

// Activation/désactivation
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->query('UPDATE Publication SET actif = 1 - actif WHERE id = ' . $id);
    $_SESSION['success_message'] = "Statut de la publication modifié.";
    header('Location: publications.php');
    exit;
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
        $_SESSION['success_message'] = "Publication modifiée avec succès.";
    }
    header('Location: publications.php');
    exit;
}

// Suppression publication
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM Publication WHERE id = ?')->execute([$id]);
    $_SESSION['success_message'] = "Publication supprimée avec succès.";
    header('Location: publications.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion des publications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/site-informatique/assets/css/style.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
            --dark: #34495e;
            --light: #ecf0f1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: var(--dark);
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(255,255,255,0.1);
            padding-left: 25px;
        }
        
        .sidebar-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .header {
            background-color: white;
            padding: 15px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-left {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .back-link {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.3s;
        }
        
        .back-link:hover {
            color: #2980b9;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            text-align: center;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.total { border-left: 4px solid var(--primary); }
        .stat-card.actifs { border-left: 4px solid var(--secondary); }
        .stat-card.inactifs { border-left: 4px solid var(--warning); }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .admin-content {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 12px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            text-decoration: none;
        }
        
        .btn:hover {
            background-color: #2980b9;
            color: white;
        }
        
        .btn-success {
            background-color: var(--secondary);
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-warning {
            background-color: var(--warning);
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
        }
        
        .publications-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .publication-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .publication-card:hover {
            transform: translateY(-5px);
        }
        
        .publication-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .publication-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .publication-content {
            color: #7f8c8d;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .publication-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 15px;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .publication-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-left: auto;
        }
        
        .user-details {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-right: 15px;
        }
        
        .current-time {
            font-size: 14px;
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .user-name {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 600;
            margin-top: 2px;
        }
        
        .user-email {
            font-size: 12px;
            color: #7f8c8d;
            font-weight: 400;
            margin-top: 2px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ddd;
            overflow: hidden;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }
        
        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }
        
        .close:hover {
            color: #000;
        }
        
        .modal h2 {
            margin-top: 0;
            margin-bottom: 15px;
            color: var(--dark);
            font-size: 20px;
        }
        
        .current-image {
            max-width: 150px;
            max-height: 100px;
            border-radius: 5px;
            margin: 8px 0;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .publications-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Bienvenue, <?php echo $_SESSION['nom_utilisateur']; ?></p>
            </div>
            
            <div class="sidebar-menu">
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                <a href="utilisateurs.php"><i class="fas fa-users"></i> Utilisateurs</a>
                <a href="services.php"><i class="fas fa-cogs"></i> Services</a>
                <a href="publications.php" class="active"><i class="fas fa-newspaper"></i> Publications</a>
                <a href="contacts.php"><i class="fas fa-envelope"></i> Messages</a>
                <a href="demandes_services.php"><i class="fas fa-clipboard-list"></i> Demandes</a>
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Retour au site</a>
                <a href="../includes/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="header-left">
                    <h1>Gestion des publications</h1>
                    <a href="index.php" class="back-link">
                        <i class="fas fa-arrow-left"></i> Retour au dashboard
                    </a>
                </div>
                <div class="user-info">
                    <div class="user-details">
                        <span class="current-time" id="current-time"><?php echo date('d/m/Y H:i:s'); ?></span>
                        <span class="user-name"><?php echo htmlspecialchars($current_user['nom_utilisateur']); ?></span>
                        <span class="user-email"><?php echo htmlspecialchars($current_user['email']); ?></span>
                    </div>
                    <div class="user-avatar">
                        <i class="fas fa-user" style="font-size: 20px; line-height: 40px; text-align: center; width: 100%;"></i>
                    </div>
                </div>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert success">
                    <?= $_SESSION['success_message'] ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stat-card total">
                    <i class="fas fa-newspaper"></i>
                    <div class="stat-value"><?php echo $total_publications; ?></div>
                    <div class="stat-label">Total des publications</div>
                </div>
                
                <div class="stat-card actifs">
                    <i class="fas fa-check-circle"></i>
                    <div class="stat-value"><?php echo $publications_actifs; ?></div>
                    <div class="stat-label">Publications actives</div>
                </div>
                
                <div class="stat-card inactifs">
                    <i class="fas fa-pause-circle"></i>
                    <div class="stat-value"><?php echo $publications_inactifs; ?></div>
                    <div class="stat-label">Publications inactives</div>
                </div>
            </div>
            
            <!-- Add Publication Form -->
            <div class="admin-content">
                <h2><i class="fas fa-plus"></i> Ajouter une publication</h2>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="titre">Titre de la publication</label>
                        <input type="text" id="titre" name="titre" placeholder="Titre de la publication" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="contenu">Contenu</label>
                        <textarea id="contenu" name="contenu" placeholder="Contenu de la publication" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                    
                    <button type="submit" name="ajouter" class="btn btn-success">
                        <i class="fas fa-plus"></i> Ajouter la publication
                    </button>
                </form>
            </div>
            
            <!-- Publications List -->
            <div class="admin-content">
                <h2><i class="fas fa-list"></i> Liste des publications</h2>
                
                <div class="publications-grid">
                    <?php foreach ($publications as $publication): ?>
                        <div class="publication-card">
                            <?php if ($publication['image'] && file_exists(__DIR__ . '/../assets/img/' . $publication['image'])): ?>
                                <img src="../assets/img/<?= htmlspecialchars($publication['image']) ?>" alt="Image publication" class="publication-image">
                            <?php else: ?>
                                <div style="width:100%;height:200px;background-color:#f0f0f0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#999;">
                                    <i class="fas fa-image" style="font-size:3em;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="publication-title"><?= htmlspecialchars($publication['titre']) ?></div>
                            
                            <div class="publication-content">
                                <?= nl2br(htmlspecialchars(substr($publication['contenu'], 0, 200))) ?>
                                <?= strlen($publication['contenu']) > 200 ? '...' : '' ?>
                            </div>
                            
                            <div class="publication-status <?= $publication['actif'] ? 'status-active' : 'status-inactive' ?>">
                                <?= $publication['actif'] ? 'Active' : 'Inactive' ?>
                            </div>
                            
                            <div class="publication-actions">
                                <button class="btn btn-primary" onclick="openEditModal(<?= $publication['id'] ?>, '<?= htmlspecialchars($publication['titre']) ?>', '<?= htmlspecialchars($publication['contenu']) ?>', '<?= htmlspecialchars($publication['image']) ?>')">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <a href="?toggle=<?= $publication['id'] ?>" class="btn btn-warning">
                                    <i class="fas fa-toggle-on"></i> <?= $publication['actif'] ? 'Désactiver' : 'Activer' ?>
                                </a>
                                <a href="?delete=<?= $publication['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publication ?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($publications)): ?>
                    <div style="text-align: center; padding: 40px; color: #777;">
                        <i class="fas fa-newspaper" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <p>Aucune publication à afficher</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal de modification -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2><i class="fas fa-edit"></i> Modifier la publication</h2>
            
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="id">
                <input type="hidden" id="edit_image_actuelle" name="image_actuelle">
                
                <div class="form-group">
                    <label for="edit_titre">Titre de la publication</label>
                    <input type="text" id="edit_titre" name="titre" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_contenu">Contenu</label>
                    <textarea id="edit_contenu" name="contenu" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Image actuelle</label>
                    <div id="current_image_container">
                        <img id="current_image" class="current-image" src="" alt="Image actuelle">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_image">Nouvelle image (optionnel)</label>
                    <input type="file" id="edit_image" name="image" accept="image/*">
                    <small>Laissez vide pour conserver l'image actuelle</small>
                </div>
                
                <div class="publication-actions">
                    <button type="submit" name="modifier" class="btn btn-success">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Fonction pour mettre à jour l'heure en temps réel
        function updateTime() {
            const now = new Date();
            const options = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const timeString = now.toLocaleDateString('fr-FR', options);
            document.getElementById('current-time').textContent = timeString;
        }
        
        // Mettre à jour toutes les secondes
        setInterval(updateTime, 1000);
        
        // Mettre à jour immédiatement au chargement
        updateTime();
        
        // Gestion des messages d'alerte
        document.addEventListener('DOMContentLoaded', function() {
            // Fermer les alertes après 5 secondes
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
        
        // Fonctions pour le modal de modification
        function openEditModal(id, titre, contenu, image) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_titre').value = titre;
            document.getElementById('edit_contenu').value = contenu;
            document.getElementById('edit_image_actuelle').value = image;
            
            // Afficher l'image actuelle
            const currentImage = document.getElementById('current_image');
            if (image) {
                currentImage.src = '../assets/img/' + image;
                currentImage.style.display = 'block';
            } else {
                currentImage.style.display = 'none';
            }
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Fermer le modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html> 