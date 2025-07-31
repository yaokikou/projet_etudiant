-- Script de création de la base de données et des tables
CREATE DATABASE IF NOT EXISTS entreprise_informatique CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE entreprise_informatique;

-- Table Utilisateur
CREATE TABLE IF NOT EXISTS Utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    motdepasse VARCHAR(255) NOT NULL,
    role ENUM('utilisateur', 'admin', 'moderateur') NOT NULL DEFAULT 'utilisateur',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actif TINYINT(1) NOT NULL DEFAULT 1
);

-- Table Service
CREATE TABLE IF NOT EXISTS Service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_service VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    actif TINYINT(1) NOT NULL DEFAULT 1
);

-- Table Publication
CREATE TABLE IF NOT EXISTS Publication (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    contenu TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    date_publication DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    actif TINYINT(1) NOT NULL DEFAULT 1
);

-- Table Contact
CREATE TABLE IF NOT EXISTS Contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    date_envoi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table DemandeService
CREATE TABLE IF NOT EXISTS DemandeService (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    service_id INT NOT NULL,
    description TEXT NOT NULL,
    date_demande DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50) NOT NULL DEFAULT 'en attente',
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES Service(id) ON DELETE CASCADE
); 