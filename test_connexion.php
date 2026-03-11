<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
    $pdo = new PDO(
        "mysql:host=localhost;port=3306;dbname=ticketing_app;charset=utf8mb4",
        "root",
        "root"
    );
    echo "Connexion réussie !";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}