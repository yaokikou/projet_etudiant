<?php
/**
 * Model pour la gestion des publications
 */
class PublicationModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Récupérer toutes les publications actives
     */
    public function getActivePublications() {
        if (!$this->pdo) {
            return [];
        }
        
        try {
            $stmt = $this->pdo->prepare('SELECT id, titre, contenu, image, date_publication FROM Publication WHERE actif = 1 ORDER BY date_publication DESC');
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Récupérer une publication par ID
     */
    public function getPublicationById($id) {
        if (!$this->pdo) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM Publication WHERE id = ? AND actif = 1');
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
}
?> 