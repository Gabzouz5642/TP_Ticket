<?php

// --- Imports ---
use Illuminate\Database\Migrations\Migration; // Classe parente des migrations
use Illuminate\Database\Schema\Blueprint;     // Pour définir les colonnes d'une table
use Illuminate\Support\Facades\Schema;        // Pour créer/supprimer des tables

/**
 * Fichier : database/migrations/xxxx_create_projects_table.php
 *
 * Crée la table "projects" — stocke tous les projets clients de l'app
 * C'est la table "parente" des tickets, un projet contient plusieurs tickets
 */
return new class extends Migration
{
    /**
     * up() — On crée la table projects
     * Lance avec : php artisan migrate
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();                                              // ID unique auto-incrémenté

            $table->string('name');                                    // Nom du projet — obligatoire
            $table->string('client');                                  // Nom du client — obligatoire

            $table->string('collaborators')->nullable();               // Les gens qui bossent dessus
                                                                       // Stocké en texte "Alice, Bob" — optionnel

            // decimal(8, 2) = max 8 chiffres dont 2 après la virgule → ex: 999999.99
            // default(0) = si on ne précise pas de valeur, ça vaut 0 automatiquement
            $table->decimal('hours_included', 8, 2)->default(0);      // Heures incluses dans le contrat (ex: 50.00)
            $table->decimal('hourly_rate', 8, 2)->default(0);         // Taux horaire du projet (ex: 75.00)

            $table->timestamps();                                      // Crée automatiquement created_at et updated_at
        });
    }

    /**
     * down() — On supprime la table
     * Lance avec : php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};