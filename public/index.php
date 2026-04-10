<?php

use Illuminate\Foundation\Application; // Le coeur de Laravel
use Illuminate\Http\Request;           // Représente la requête HTTP entrante

/**
 * Fichier : public/index.php
 *
 * C'est LA porte d'entrée de toute l'application Laravel
 * CHAQUE requête HTTP (page, API, formulaire...) passe par ce fichier en premier
 * C'est le seul fichier PHP directement accessible depuis le navigateur
 */


// Enregistre le timestamp du début de l'exécution
// Utile pour mesurer le temps de réponse de l'app (ex: dans les logs ou debugbar)
define('LARAVEL_START', microtime(true));


// --- MODE MAINTENANCE ---
// Vérifie si un fichier "maintenance.php" existe dans le dossier storage
// Ce fichier est créé par la commande : php artisan down
// Si il existe → affiche la page de maintenance et arrête tout
// Si il n'existe pas → on continue normalement
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance; // Charge la page de maintenance et stoppe l'exécution
}


// --- AUTOLOADER COMPOSER ---
// Charge le système d'autoloading de Composer
// C'est ce qui permet d'utiliser tous les packages installés (Laravel, Carbon, etc.)
// sans avoir à les "require" manuellement un par un
require __DIR__.'/../vendor/autoload.php';


// --- DÉMARRAGE DE LARAVEL ---
// Charge le fichier bootstrap/app.php qu'on a commenté plus tôt
// C'est lui qui configure les routes, middlewares, exceptions...
// et retourne l'instance de l'application prête à fonctionner
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';


// --- TRAITEMENT DE LA REQUÊTE ---
// Capture la requête HTTP entrante (URL, méthode GET/POST, headers, données...)
// et la passe à Laravel qui va :
// 1. Trouver la route correspondante
// 2. Appeler le bon contrôleur
// 3. Retourner la réponse au navigateur
$app->handleRequest(Request::capture());