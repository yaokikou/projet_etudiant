-- Script de migration pour ajouter le système de rôles
-- À exécuter sur la base de données existante

USE entreprise_informatique;

-- Ajouter la colonne role si elle n'existe pas
ALTER TABLE Utilisateur 
ADD COLUMN IF NOT EXISTS role ENUM('utilisateur', 'admin', 'moderateur') NOT NULL DEFAULT 'utilisateur';

-- Ajouter la colonne date_creation si elle n'existe pas
ALTER TABLE Utilisateur 
ADD COLUMN IF NOT EXISTS date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- Ajouter la colonne actif si elle n'existe pas
ALTER TABLE Utilisateur 
ADD COLUMN IF NOT EXISTS actif TINYINT(1) NOT NULL DEFAULT 1;

-- Mettre à jour l'utilisateur 'admin' existant pour lui donner le rôle admin
UPDATE Utilisateur SET role = 'admin' WHERE nom_utilisateur = 'admin';

-- Si aucun utilisateur admin n'existe, en créer un par défaut
-- (mot de passe: admin123 - à changer après la première connexion)
INSERT IGNORE INTO Utilisateur (nom_utilisateur, email, motdepasse, role) 
VALUES ('admin', 'admin@site.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 