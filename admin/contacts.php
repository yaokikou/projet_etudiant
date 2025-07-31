<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['nom_utilisateur'] !== 'admin') {
    header('Location: ../connexion.php');
    exit;
}

// Inclusion de la connexion à la base de données
require_once '../includes/db.php';

// Paramètres de pagination
$messages_par_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $messages_par_page;

// Récupération des informations utilisateur connecté
try {
    // Informations de l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT nom_utilisateur, email FROM Utilisateur WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_user = $stmt->fetch();
    
    // Statistiques des messages
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM Contact");
    $total_messages = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as aujourd_hui FROM Contact WHERE DATE(date_envoi) = CURDATE()");
    $messages_aujourd_hui = $stmt->fetch()['aujourd_hui'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as cette_semaine FROM Contact WHERE date_envoi >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $messages_semaine = $stmt->fetch()['cette_semaine'];
    
    // Liste des messages avec pagination
    $stmt = $pdo->prepare('SELECT * FROM Contact ORDER BY date_envoi DESC LIMIT :limit OFFSET :offset');
    $stmt->bindValue(':limit', $messages_par_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $contacts = $stmt->fetchAll();
    
    // Calcul du nombre total de pages
    $total_pages = ceil($total_messages / $messages_par_page);
    
} catch (PDOException $e) {
    // En cas d'erreur, utiliser des valeurs par défaut
    $current_user = ['nom_utilisateur' => $_SESSION['nom_utilisateur'], 'email' => ''];
    $total_messages = 0;
    $messages_aujourd_hui = 0;
    $messages_semaine = 0;
    $contacts = [];
    $total_pages = 1;
}

// Suppression d'un message
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare('DELETE FROM Contact WHERE id = ?');
        $stmt->execute([$id]);
        $_SESSION['success_message'] = "Message supprimé avec succès.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erreur lors de la suppression du message.";
    }
    header('Location: contacts.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Messages de contact</title>
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
        .stat-card.aujourd-hui { border-left: 4px solid var(--secondary); }
        .stat-card.semaine { border-left: 4px solid var(--warning); }
        
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
        
        .messages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .message-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            border-left: 4px solid var(--primary);
        }
        
        .message-card:hover {
            transform: translateY(-5px);
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .message-info h3 {
            margin: 0;
            font-size: 16px;
            color: #2c3e50;
        }
        
        .message-email {
            font-size: 14px;
            color: #7f8c8d;
            margin: 5px 0;
        }
        
        .message-date {
            font-size: 12px;
            color: #95a5a6;
        }
        
        .message-content {
            color: #2c3e50;
            line-height: 1.6;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 3px solid var(--primary);
        }
        
        .message-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .admin-table th,
        .admin-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .admin-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .admin-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .message-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
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
            margin: 2px;
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
        
        .btn-outline-primary {
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
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
        
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        
        .pagination li {
            margin: 0 5px;
        }
        
        .pagination a {
            display: block;
            padding: 8px 12px;
            background-color: white;
            color: var(--primary);
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: all 0.3s;
        }
        
        .pagination a:hover {
            background-color: var(--primary);
            color: white;
        }
        
        .pagination .active a {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .no-messages {
            text-align: center;
            padding: 40px;
            color: #777;
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
        
        .modal-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .modal-field {
            margin-bottom: 10px;
        }
        
        .modal-field strong {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .modal-field p {
            margin: 0;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid var(--primary);
        }
        
        .modal-message {
            margin-top: 15px;
        }
        
        .modal-message-content {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid var(--primary);
            white-space: pre-wrap;
            line-height: 1.6;
        }
        
        .modal-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
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
            
            .messages-grid {
                grid-template-columns: 1fr;
            }
            
            .message-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .admin-table {
                font-size: 12px;
            }
            
            .admin-table th,
            .admin-table td {
                padding: 8px 4px;
            }
            
            .modal-row {
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
                <a href="publications.php"><i class="fas fa-newspaper"></i> Publications</a>
                <a href="contacts.php" class="active"><i class="fas fa-envelope"></i> Messages</a>
                <a href="demandes_services.php"><i class="fas fa-clipboard-list"></i> Demandes</a>
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Retour au site</a>
                <a href="../includes/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="header-left">
                    <h1>Messages de contact</h1>
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
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert error">
                    <?= $_SESSION['error_message'] ?>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stat-card total">
                    <i class="fas fa-envelope"></i>
                    <div class="stat-value"><?php echo $total_messages; ?></div>
                    <div class="stat-label">Total des messages</div>
                </div>
                
                <div class="stat-card aujourd-hui">
                    <i class="fas fa-calendar-day"></i>
                    <div class="stat-value"><?php echo $messages_aujourd_hui; ?></div>
                    <div class="stat-label">Aujourd'hui</div>
                </div>
                
                <div class="stat-card semaine">
                    <i class="fas fa-calendar-week"></i>
                    <div class="stat-value"><?php echo $messages_semaine; ?></div>
                    <div class="stat-label">Cette semaine</div>
                </div>
            </div>
            
            <!-- Messages List -->
            <div class="admin-content">
                <h2><i class="fas fa-list"></i> Liste des messages</h2>
                
                <?php if (empty($contacts)): ?>
                    <div class="no-messages">
                        <i class="fas fa-envelope" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <p>Aucun message disponible</p>
                    </div>
                <?php else: ?>
                    <div class="messages-grid">
                        <?php foreach ($contacts as $contact): ?>
                            <div class="message-card">
                                <div class="message-header">
                                    <div class="message-info">
                                        <h3><?= htmlspecialchars($contact['nom']) ?></h3>
                                        <div class="message-email">
                                            <i class="fas fa-envelope"></i> <?= htmlspecialchars($contact['email']) ?>
                                        </div>
                                    </div>
                                    <div class="message-date">
                                        <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($contact['date_envoi'])) ?>
                                    </div>
                                </div>
                                
                                <div class="message-content">
                                    <?= nl2br(htmlspecialchars(substr($contact['message'], 0, 200))) ?>
                                    <?= strlen($contact['message']) > 200 ? '...' : '' ?>
                                </div>
                                
                                <div class="message-actions">
                                    <button class="btn btn-primary" onclick="viewMessage(<?= $contact['id'] ?>, '<?= htmlspecialchars($contact['nom']) ?>', '<?= htmlspecialchars($contact['email']) ?>', '<?= htmlspecialchars($contact['message']) ?>', '<?= date('d/m/Y H:i', strtotime($contact['date_envoi'])) ?>')">
                                        <i class="fas fa-eye"></i> Voir plus
                                    </button>
                                    <a href="mailto:<?= htmlspecialchars($contact['email']) ?>" class="btn btn-success">
                                        <i class="fas fa-reply"></i> Répondre
                                    </a>
                                    <a href="?delete=<?= $contact['id'] ?>" class="btn btn-danger" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li>
                                <a href="?page=<?php echo $page - 1; ?>">Précédent</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="<?php echo $i === $page ? 'active' : ''; ?>">
                                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li>
                                <a href="?page=<?php echo $page + 1; ?>">Suivant</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal Voir Message -->
    <div id="viewMessageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2><i class="fas fa-envelope"></i> Détails du message</h2>
            
            <div class="modal-row">
                <div class="modal-field">
                    <strong>Nom:</strong>
                    <p id="modal-nom"></p>
                </div>
                <div class="modal-field">
                    <strong>Email:</strong>
                    <p id="modal-email"></p>
                </div>
            </div>
            
            <div class="modal-row">
                <div class="modal-field">
                    <strong>Date:</strong>
                    <p id="modal-date"></p>
                </div>
            </div>
            
            <div class="modal-message">
                <strong>Message:</strong>
                <div id="modal-message" class="modal-message-content"></div>
            </div>
            
            <div class="modal-actions">
                <a href="#" id="modal-reply-link" class="btn btn-success">
                    <i class="fas fa-reply"></i> Répondre
                </a>
                <button type="button" class="btn btn-danger" onclick="closeModal()">
                    <i class="fas fa-times"></i> Fermer
                </button>
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
        });
        
        // Fonctions pour le modal
        function viewMessage(id, nom, email, message, date) {
            document.getElementById('modal-nom').textContent = nom;
            document.getElementById('modal-email').textContent = email;
            document.getElementById('modal-message').textContent = message;
            document.getElementById('modal-date').textContent = date;
            document.getElementById('modal-reply-link').href = 'mailto:' + email;
            
            document.getElementById('viewMessageModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('viewMessageModal').style.display = 'none';
        }
        
        // Fermer le modal en cliquant à l'extérieur
        window.onclick = function(event) {
            const modal = document.getElementById('viewMessageModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html> 
