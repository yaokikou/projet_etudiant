<?php
// Fichier de connexion à la base de données

$host = 'localhost';
$db   = 'entreprise_informatique';
$user = 'root'; 
$pass = '';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Utiliser du pdo pour etablir la connexion 

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, $options);
} catch (PDOException $e) {

    // En cas d'erreur, définir $pdo à null 
    $pdo = null;
} 