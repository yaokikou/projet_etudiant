<?php
// Model pour la gestion des requetes de contact

class ContactModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Fonction pour créer une nouvelle requête de prise de contact avec un utilisateur

    public function createContact($nom, $email, $message) {
        if (!$this->pdo) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare('INSERT INTO Contact (nom, email, message) VALUES (?, ?, ?)');
            return $stmt->execute([$nom, $email, $message]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Fonction pour récupérer toutes les demandes de contact envoyees par les utilisateurs

    public function getAllContacts() {
        if (!$this->pdo) {
            return [];
        }
        
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM Contact ORDER BY date_envoi DESC');
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}
?> 