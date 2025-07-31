<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

// Vérification des droits admin
requireAdmin();

// Récupération des statistiques dynamiques
try {
    // Informations de l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT nom_utilisateur, email FROM Utilisateur WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_user = $stmt->fetch();
    
    // Nombre d'utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Utilisateur");
    $users_count = $stmt->fetch()['count'];
    
    // Nombre de services actifs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Service WHERE actif = 1");
    $services_count = $stmt->fetch()['count'];
    
    // Nombre de nouveaux messages (dernières 24h)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Contact WHERE date_envoi >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    $messages_count = $stmt->fetch()['count'];
    
    // Nombre de demandes en attente
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM DemandeService WHERE statut = 'en attente'");
    $demandes_count = $stmt->fetch()['count'];
    
    // Nombre de publications actives
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Publication WHERE actif = 1");
    $publications_count = $stmt->fetch()['count'];
    
    // Notifications par page (une notification par page)
    $notifications = [];
    
    // Notification pour les utilisateurs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Utilisateur WHERE id > (SELECT MAX(id) FROM Utilisateur) - 5");
    $recent_users = $stmt->fetch()['count'];
    if ($recent_users > 0) {
        $notifications[] = [
            'page' => 'utilisateurs',
            'title' => 'Nouveaux utilisateurs',
            'description' => $recent_users . ' nouveaux utilisateurs inscrits',
            'icon' => 'fas fa-user-plus',
            'color' => 'var(--primary)',
            'link' => 'utilisateurs.php'
        ];
    }
    
    // Notification pour les messages
    if ($messages_count > 0) {
        $notifications[] = [
            'page' => 'contacts',
            'title' => 'Nouveaux messages',
            'description' => $messages_count . ' nouveaux messages reçus',
            'icon' => 'fas fa-envelope',
            'color' => 'var(--warning)',
            'link' => 'contacts.php'
        ];
    }
    
    // Notification pour les demandes
    if ($demandes_count > 0) {
        $notifications[] = [
            'page' => 'demandes',
            'title' => 'Demandes en attente',
            'description' => $demandes_count . ' demandes nécessitent votre attention',
            'icon' => 'fas fa-clipboard-list',
            'color' => 'var(--danger)',
            'link' => 'demandes_services.php'
        ];
    }
    
    // Notification pour les services
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Service WHERE actif = 0");
    $inactive_services = $stmt->fetch()['count'];
    if ($inactive_services > 0) {
        $notifications[] = [
            'page' => 'services',
            'title' => 'Services inactifs',
            'description' => $inactive_services . ' services sont désactivés',
            'icon' => 'fas fa-cog',
            'color' => 'var(--secondary)',
            'link' => 'services.php'
        ];
    }
    
    // Notification pour les services récemment modifiés
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Service WHERE date_modification >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $recently_modified_services = $stmt->fetch()['count'];
    if ($recently_modified_services > 0) {
        $notifications[] = [
            'page' => 'services',
            'title' => 'Services modifiés',
            'description' => $recently_modified_services . ' services ont été modifiés cette semaine',
            'icon' => 'fas fa-edit',
            'color' => 'var(--primary)',
            'link' => 'services.php'
        ];
    }
    
    // Notification pour les publications
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Publication WHERE actif = 0");
    $inactive_publications = $stmt->fetch()['count'];
    if ($inactive_publications > 0) {
        $notifications[] = [
            'page' => 'publications',
            'title' => 'Publications inactives',
            'description' => $inactive_publications . ' publications sont désactivées',
            'icon' => 'fas fa-newspaper',
            'color' => '#9b59b6',
            'link' => 'publications.php'
        ];
    }
    
    // Notification pour les publications récemment modifiées
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM Publication WHERE date_modification >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $recently_modified_publications = $stmt->fetch()['count'];
    if ($recently_modified_publications > 0) {
        $notifications[] = [
            'page' => 'publications',
            'title' => 'Publications modifiées',
            'description' => $recently_modified_publications . ' publications ont été modifiées cette semaine',
            'icon' => 'fas fa-edit',
            'color' => '#9b59b6',
            'link' => 'publications.php'
        ];
    }
    
} catch (PDOException $e) {
    // En cas d'erreur, utiliser des valeurs par défaut
    $current_user = ['nom_utilisateur' => $_SESSION['nom_utilisateur'], 'email' => ''];
    $users_count = 0;
    $services_count = 0;
    $messages_count = 0;
    $demandes_count = 0;
    $publications_count = 0;
    $notifications = [];
}

$stats = [
    'users' => $users_count,
    'services' => $services_count,
    'messages' => $messages_count,
    'demandes' => $demandes_count,
    'publications' => $publications_count
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tableau de bord</title>
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
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.users { border-left: 4px solid var(--primary); }
        .stat-card.services { border-left: 4px solid var(--secondary); }
        .stat-card.messages { border-left: 4px solid var(--warning); }
        .stat-card.demandes { border-left: 4px solid var(--danger); }
        .stat-card.publications { border-left: 4px solid #9b59b6; }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .notifications-container {
            background-color: white;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .notifications-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .notification-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .notification-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            color: inherit;
        }
        
        .notification-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }
        
        .notification-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }
        
        .notification-description {
            color: #7f8c8d;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }
        
        .notification-arrow {
            margin-left: auto;
            color: #95a5a6;
            font-size: 16px;
            transition: transform 0.3s;
        }
        
        .notification-card:hover .notification-arrow {
            transform: translateX(5px);
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
        
        .logout-btn {
            background-color: var(--danger);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #c0392b;
        }
        
        .no-notifications {
            text-align: center;
            color: #7f8c8d;
            padding: 40px;
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
                <a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
                <a href="utilisateurs.php"><i class="fas fa-users"></i> Utilisateurs</a>
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
                <h1>Tableau de bord</h1>
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
            
            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stat-card users">
                    <i class="fas fa-users"></i>
                    <div class="stat-value"><?php echo $stats['users']; ?></div>
                    <div class="stat-label">Utilisateurs</div>
                </div>
                
                <div class="stat-card services">
                    <i class="fas fa-cogs"></i>
                    <div class="stat-value"><?php echo $stats['services']; ?></div>
                    <div class="stat-label">Services</div>
                </div>
                
                <div class="stat-card messages">
                    <i class="fas fa-envelope"></i>
                    <div class="stat-value"><?php echo $stats['messages']; ?></div>
                    <div class="stat-label">Nouveaux messages</div>
                </div>
                
                <div class="stat-card demandes">
                    <i class="fas fa-clipboard-list"></i>
                    <div class="stat-value"><?php echo $stats['demandes']; ?></div>
                    <div class="stat-label">Demandes en attente</div>
                </div>
                
                <div class="stat-card publications">
                    <i class="fas fa-newspaper"></i>
                    <div class="stat-value"><?php echo $stats['publications']; ?></div>
                    <div class="stat-label">Publications</div>
                </div>
            </div>
            
            <!-- Notifications par page -->
            <div class="notifications-container">
                <h2><i class="fas fa-bell"></i> Notifications importantes</h2>
                
                <?php if (empty($notifications)): ?>
                    <div class="no-notifications">
                        <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 15px; color: var(--secondary);"></i>
                        <p>Tout est à jour ! Aucune action requise.</p>
                    </div>
                <?php else: ?>
                    <div class="notifications-grid">
                        <?php foreach ($notifications as $notification): ?>
                            <a href="<?php echo $notification['link']; ?>" class="notification-card" style="border-left-color: <?php echo $notification['color']; ?>;">
                                <div class="notification-header">
                                    <div class="notification-icon" style="background-color: <?php echo $notification['color']; ?>;">
                                        <i class="<?php echo $notification['icon']; ?>"></i>
                                    </div>
                                    <div>
                                        <h3 class="notification-title"><?php echo $notification['title']; ?></h3>
                                        <p class="notification-description"><?php echo $notification['description']; ?></p>
                                    </div>
                                    <div class="notification-arrow">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
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
    </script>
</body>
</html> 