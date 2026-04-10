<?php

// --- Imports ---
use Illuminate\Foundation\Application;                      // Classe principale de Laravel, le "coeur" du framework
use Illuminate\Foundation\Configuration\Exceptions;        // Gestion des exceptions/erreurs globales
use Illuminate\Foundation\Configuration\Middleware;        // Gestion des middlewares (filtres de requêtes)

/**
 * Fichier : bootstrap/app.php
 * C'est le POINT D'ENTRÉE de toute l'application Laravel
 * Il est exécuté à chaque requête HTTP et configure l'application
 */

return Application::configure(basePath: dirname(__DIR__))
    // dirname(__DIR__) = remonte d'un dossier pour pointer vers la racine du projet

    /**
     * Configuration des routes de l'application
     * Indique à Laravel où trouver chaque type de route
     */
    ->withRouting(
        // Routes web classiques (pages HTML, formulaires, dashboard...)
        web: __DIR__.'/../routes/web.php',

        // Routes API (retournent du JSON, utilisées par le front-end JS ou une app mobile)
        api: __DIR__.'/../routes/api.php',

        // Routes pour les commandes Artisan personnalisées (terminal)
        commands: __DIR__.'/../routes/console.php',

        // Route de santé : GET /up → vérifie que l'application est bien démarrée
        // Utilisée par les outils de monitoring ou Docker pour savoir si l'app tourne
        health: '/up',
    )

    /**
     * Configuration des Middlewares
     * Les middlewares sont des "filtres" qui s'exécutent avant/après chaque requête
     * Ex: vérifier si l'utilisateur est connecté, logger les requêtes...
     *
     * Actuellement vide : seuls les middlewares par défaut de Laravel sont actifs
     */
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })

    /**
     * Configuration de la gestion des erreurs globales
     * Permet de personnaliser comment Laravel réagit à certaines exceptions
     * Ex: rediriger vers une page 404 custom, logger certaines erreurs...
     *
     * Actuellement vide : comportement par défaut de Laravel
     */
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    // Finalise la configuration et crée l'instance de l'application
    ->create();