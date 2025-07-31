<?php
require_once '../includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['nom_utilisateur'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}

// Suppression utilisateur
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM Utilisateur WHERE id = ? AND nom_utilisateur != "admin"');
    $stmt->execute([$id]);
    $_SESSION['success_message'] = "L'utilisateur a été supprimé avec succès.";
    header('Location: utilisateurs.php');
    exit;
}

// Récupération des informations utilisateur connecté
try {
    // Informations de l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT nom_utilisateur, email FROM Utilisateur WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_user = $stmt->fetch();
    
    // Liste utilisateurs
    $stmt = $pdo->query('SELECT id, nom_utilisateur, email FROM Utilisateur WHERE nom_utilisateur != "admin"');
    $users = $stmt->fetchAll();
    
} catch (PDOException $e) {
    // En cas d'erreur, utiliser des valeurs par défaut
    $current_user = ['nom_utilisateur' => $_SESSION['nom_utilisateur'], 'email' => ''];
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion des utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            transition: all 0.3s ease;
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
            transition: all 0.3s ease;
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
            flex-wrap: wrap;
            gap: 15px;
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
        
        .admin-content {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th,
        .admin-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .admin-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .admin-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: var(--primary);
            color: white;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            text-decoration: none;
        }
        
        .btn:hover {
            background-color: #2980b9;
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-back {
            background-color: var(--dark);
            margin-top: 20px;
        }
        
        .btn-back:hover {
            background-color: #2c3e50;
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
        
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
        }
        
        /* Responsive Design */
        @media (max-width: 991px) {
            .admin-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: relative;
                z-index: 1000;
            }
            
            .sidebar-toggle {
                display: block;
                position: absolute;
                top: 20px;
                right: 20px;
            }
            
            .sidebar.collapsed {
                display: none;
            }
            
            .main-content {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .user-info {
                margin-left: 0;
                align-self: flex-end;
            }
        }
        
        @media (max-width: 768px) {
            .admin-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .admin-table th,
            .admin-table td {
                padding: 8px 10px;
                font-size: 14px;
            }
            
            .btn {
                padding: 6px 10px;
                font-size: 12px;
            }
            
            .user-details {
                margin-right: 10px;
            }
            
            .current-time {
                font-size: 12px;
            }
            
            .user-name {
                font-size: 14px;
            }
            
            .user-email {
                font-size: 11px;
            }
            
            .user-avatar {
                width: 35px;
                height: 35px;
            }
        }
        
        @media (max-width: 576px) {
            .main-content {
                padding: 10px;
            }
            
            .admin-content {
                padding: 15px;
            }
            
            .header {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .admin-table th,
            .admin-table td {
                padding: 6px 8px;
                font-size: 13px;
            }
            
            .btn {
                padding: 5px 8px;
                font-size: 11px;
            }
            
            .btn i {
                margin-right: 3px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar" id="sidebar">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p>Bienvenue, <?= $_SESSION['nom_utilisateur'] ?></p>
            </div>
            
            <div class="sidebar-menu">
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                <a href="utilisateurs.php" class="active"><i class="fas fa-users"></i> Utilisateurs</a>
                <a href="services.php"><i class="fas fa-cogs"></i> Services</a>
                <a href="publications.php"><i class="fas fa-newspaper"></i> Publications</a>
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
                    <h1>Gestion des utilisateurs</h1>
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
            
            <div class="admin-content">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom d'utilisateur</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['nom_utilisateur']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <a href="?delete=<?= $u['id'] ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($users)): ?>
                    <div style="text-align: center; padding: 20px; color: #777;">
                        <i class="fas fa-user-slash" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <p>Aucun utilisateur à afficher</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
            
            // Gestion du toggle sidebar sur mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                });
            }
            
            // Fermer le sidebar quand on clique en dehors sur mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 991) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.add('collapsed');
                    }
                }
            });
        });
    </script>
</body>
</html>