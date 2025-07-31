<?php

 // Model pour la gestion des utilisateurs
 
class UserModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
   //Authentifier un utilisateur par nom d'utilisateur et mot de passe

    public function authenticate($nom_utilisateur, $motdepasse) {
        if (!$this->pdo) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare('SELECT id, nom_utilisateur, motdepasse, role, actif FROM Utilisateur WHERE nom_utilisateur = ?');
            $stmt->execute([$nom_utilisateur]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($motdepasse, $user['motdepasse'])) {
                return $user;
            }
            
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
    
    // Fonction pour créer un nouvel utilisateur avec pour role utilisateur

    public function createUser($nom_utilisateur, $email, $motdepasse, $role = 'utilisateur') {
        if (!$this->pdo) {
            return false;
        }
        
        try {
            $hashedPassword = password_hash($motdepasse, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare('INSERT INTO Utilisateur (nom_utilisateur, email, motdepasse, role) VALUES (?, ?, ?, ?)');
            return $stmt->execute([$nom_utilisateur, $email, $hashedPassword, $role]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    //Fonction pour vérifier si un nom d'utilisateur existe

    public function usernameExists($nom_utilisateur) {
        if (!$this->pdo) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM Utilisateur WHERE nom_utilisateur = ?');
            $stmt->execute([$nom_utilisateur]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    
     //Fonction pour vérifier si un email existe

    public function emailExists($email) {
        if (!$this->pdo) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM Utilisateur WHERE email = ?');
            $stmt->execute([$email]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}
?> 