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
    
    // Statistiques des demandes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM DemandeService");
    $total_demandes = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as en_attente FROM DemandeService WHERE statut = 'en attente'");
    $demandes_attente = $stmt->fetch()['en_attente'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as en_cours FROM DemandeService WHERE statut = 'en cours'");
    $demandes_cours = $stmt->fetch()['en_cours'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as traitees FROM DemandeService WHERE statut = 'traitée'");
    $demandes_traitees = $stmt->fetch()['traitees'];
    
    // Récupérer toutes les demandes de services avec jointure utilisateur et service
    $stmt = $pdo->query('SELECT d.id, u.nom_utilisateur, s.nom_service, d.description, d.date_demande, d.statut FROM DemandeService d JOIN Utilisateur u ON d.utilisateur_id = u.id JOIN Service s ON d.service_id = s.id ORDER BY d.date_demande DESC');
    $demandes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    // En cas d'erreur, utiliser des valeurs par défaut
    $current_user = ['nom_utilisateur' => $_SESSION['nom_utilisateur'], 'email' => ''];
    $total_demandes = 0;
    $demandes_attente = 0;
    $demandes_cours = 0;
    $demandes_traitees = 0;
    $demandes = [];
}

// Changement de statut
if (isset($_POST['changer_statut'])) {
    $id = (int)$_POST['id'];
    $statut = $_POST['statut']; 
    $stmt = $pdo->prepare('UPDATE DemandeService SET statut=? WHERE id=?');
    $stmt->execute([$statut, $id]);
    $_SESSION['success_message'] = "Statut de la demande modifié avec succès.";
    header('Location: demandes_services.php');
    exit;
}

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM DemandeService WHERE id=?')->execute([$id]);
    $_SESSION['success_message'] = "Demande supprimée avec succès.";
    header('Location: demandes_services.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Demandes de services</title>
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
        .stat-card.attente { border-left: 4px solid var(--warning); }
        .stat-card.cours { border-left: 4px solid var(--primary); }
        .stat-card.traitees { border-left: 4px solid var(--secondary); }
        
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
        }
        
        .demandes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .demande-card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            position: relative;
        }
        
        .demande-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .demande-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary);
            border-radius: 12px 12px 0 0;
        }
        
        .demande-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .demande-info h3 {
            margin: 0 0 8px 0;
            font-size: 18px;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .demande-service {
            font-size: 14px;
            color: #7f8c8d;
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background: #f8f9fa;
            border-radius: 20px;
            display: inline-flex;
        }
        
        .demande-date {
            font-size: 12px;
            color: #95a5a6;
            background: #f8f9fa;
            padding: 6px 12px;
            border-radius: 20px;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .demande-content {
            color: #2c3e50;
            line-height: 1.6;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
            max-height: 4.8em; /* 3 lignes avec line-height 1.6 */
            overflow: hidden;
            position: relative;
        }
        
        .demande-content.expanded {
            max-height: none;
        }
        
        .demande-content .content-text {
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-bottom: 10px;
        }
        
        .demande-content .expand-btn {
            position: absolute;
            bottom: 5px;
            right: 10px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 11px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .demande-content .expand-btn:hover {
            background: #2980b9;
        }
        
        .demande-content .fade-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(transparent, #f8f9fa);
            pointer-events: none;
            transition: opacity 0.3s;
        }
        
        .demande-content.expanded .fade-overlay {
            opacity: 0;
        }
        
        .demande-status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-en-attente {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-en-cours {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-traitee {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-refusee {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .demande-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
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
        
        .no-demandes {
            text-align: center;
            padding: 40px;
            color: #777;
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
            
            .demandes-grid {
                grid-template-columns: 1fr;
            }
            
            .demande-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
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
                <a href="publications.php"><i class="fas fa-newspaper"></i> Publications</a>
                <a href="contacts.php"><i class="fas fa-envelope"></i> Messages</a>
                <a href="demandes_services.php" class="active"><i class="fas fa-clipboard-list"></i> Demandes</a>
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Retour au site</a>
                <a href="../includes/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="header-left">
                    <h1>Demandes de services</h1>
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
                    <i class="fas fa-clipboard-list"></i>
                    <div class="stat-value"><?php echo $total_demandes; ?></div>
                    <div class="stat-label">Total des demandes</div>
                </div>
                
                <div class="stat-card attente">
                    <i class="fas fa-clock"></i>
                    <div class="stat-value"><?php echo $demandes_attente; ?></div>
                    <div class="stat-label">En attente</div>
                </div>
                
                <div class="stat-card cours">
                    <i class="fas fa-spinner"></i>
                    <div class="stat-value"><?php echo $demandes_cours; ?></div>
                    <div class="stat-label">En cours</div>
                </div>
                
                <div class="stat-card traitees">
                    <i class="fas fa-check-circle"></i>
                    <div class="stat-value"><?php echo $demandes_traitees; ?></div>
                    <div class="stat-label">Traitées</div>
                </div>
            </div>
            
            <!-- Demandes List -->
            <div class="admin-content">
                <h2><i class="fas fa-list"></i> Liste des demandes</h2>
                
                <?php if (empty($demandes)): ?>
                    <div class="no-demandes">
                        <i class="fas fa-clipboard-list" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <p>Aucune demande à afficher</p>
                    </div>
                <?php else: ?>
                    <div class="demandes-grid">
                        <?php foreach ($demandes as $demande): ?>
                            <div class="demande-card">
                                <div class="demande-header">
                                    <div class="demande-info">
                                        <h3><?= htmlspecialchars($demande['nom_utilisateur']) ?></h3>
                                        <div class="demande-service">
                                            <i class="fas fa-cog"></i> <?= htmlspecialchars($demande['nom_service']) ?>
                                        </div>
                                    </div>
                                    <div class="demande-date">
                                        <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($demande['date_demande'])) ?>
                                    </div>
                                </div>
                                
                                <div class="demande-content" id="content-<?= $demande['id'] ?>">
                                    <div class="content-text"><?= nl2br(htmlspecialchars($demande['description'])) ?></div>
                                    <div class="fade-overlay"></div>
                                    <button type="button" class="expand-btn" onclick="toggleContent(<?= $demande['id'] ?>)">
                                        <i class="fas fa-expand-alt"></i> Voir plus
                                    </button>
                                </div>
                                
                                <div class="demande-status">
                                    <form method="post" style="display: flex; align-items: center; gap: 10px;">
                                        <input type="hidden" name="id" value="<?= $demande['id'] ?>">
                                        <select name="statut" class="status-select">
                                            <option value="en attente" <?= $demande['statut']==='en attente'?'selected':'' ?>>En attente</option>
                                            <option value="en cours" <?= $demande['statut']==='en cours'?'selected':'' ?>>En cours</option>
                                            <option value="traitée" <?= $demande['statut']==='traitée'?'selected':'' ?>>Traitée</option>
                                            <option value="refusée" <?= $demande['statut']==='refusée'?'selected':'' ?>>Refusée</option>
                                        </select>
                                        <button type="submit" name="changer_statut" class="btn btn-success">
                                            <i class="fas fa-save"></i> Mettre à jour
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="demande-actions">
                                    <a href="?delete=<?= $demande['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </div>
                            </div>
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
            
            // Initialiser l'affichage des contenus
            initializeContentDisplay();
        });
        
        // Fonction pour basculer l'affichage du contenu
        function toggleContent(id) {
            const content = document.getElementById('content-' + id);
            const expandBtn = content.querySelector('.expand-btn');
            const fadeOverlay = content.querySelector('.fade-overlay');
            
            if (content.classList.contains('expanded')) {
                // Réduire
                content.classList.remove('expanded');
                expandBtn.innerHTML = '<i class="fas fa-expand-alt"></i> Voir plus';
                fadeOverlay.style.opacity = '1';
            } else {
                // Étendre
                content.classList.add('expanded');
                expandBtn.innerHTML = '<i class="fas fa-compress-alt"></i> Voir moins';
                fadeOverlay.style.opacity = '0';
            }
        }
        
        // Fonction pour initialiser l'affichage des contenus
        function initializeContentDisplay() {
            const contents = document.querySelectorAll('.demande-content');
            
            contents.forEach(content => {
                const contentText = content.querySelector('.content-text');
                const expandBtn = content.querySelector('.expand-btn');
                const fadeOverlay = content.querySelector('.fade-overlay');
                
                // Calculer la hauteur de 3 lignes (line-height 1.6)
                const lineHeight = parseFloat(window.getComputedStyle(contentText).lineHeight);
                const maxHeight = lineHeight * 3;
                
                // Vérifier si le contenu dépasse 3 lignes
                if (contentText.scrollHeight > maxHeight) {
                    expandBtn.style.display = 'block';
                    fadeOverlay.style.opacity = '1';
                } else {
                    expandBtn.style.display = 'none';
                    fadeOverlay.style.opacity = '0';
                }
            });
        }
    </script>
</body>
</html> 