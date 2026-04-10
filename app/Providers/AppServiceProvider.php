<?php

namespace App\Providers;

// --- Import ---
use Illuminate\Support\ServiceProvider; // Classe parente de tous les Service Providers Laravel

/**
 * Service Provider principal de l'application
 * C'est l'un des premiers fichiers exécutés au démarrage de Laravel
 * Il sert de point d'entrée pour configurer et initialiser les services de l'app
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Méthode register() — Enregistrement des services
     *
     * S'exécute en PREMIER au démarrage
     * Sert à lier des classes/interfaces dans le conteneur d'injection de dépendances Laravel
     *
     * Exemples de ce qu'on pourrait y mettre :
     * $this->app->bind(MonInterface::class, MonImplementation::class);
     *
     * Actuellement vide : aucun service personnalisé n'est enregistré
     */
    public function register(): void
    {
        //
    }

    /**
     * Méthode boot() — Initialisation des services
     *
     * S'exécute en SECOND, après que tous les services sont enregistrés
     * Sert à initialiser des comportements globaux de l'application
     *
     * Exemples de ce qu'on pourrait y mettre :
     * - Règles de validation personnalisées
     * - Observers sur les modèles
     * - Partage de variables globales dans toutes les vues Blade
     * - Configuration de macros
     *
     * Actuellement vide : aucune initialisation personnalisée
     */
    public function boot(): void
    {
        //
    }
}