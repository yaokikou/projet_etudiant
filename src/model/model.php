<?php

function dbConnect()
{
    try {
        $database = new PDO('mysql:host=localhost;dbname=entreprise_informatique;charset=utf8', 'root');

        return $database;
    } catch (Exception $e) {
        die('Erreur de connexion a la base de donnée : ' . $e->getMessage());
    }
}


// Fonction pour récupérer les services actifs

function getService()
{
    $pdo = dbConnect();
    $stmt = $pdo->prepare('SELECT nom_service, description, image FROM Service WHERE actif = 1 ORDER BY date_ajout DESC');
    $stmt->execute();
    $services =  $stmt->fetchAll();

    return $services;
}

echo $services = getService();
echo $services[0]['nom_service'] ?? 'Aucun service trouvé';