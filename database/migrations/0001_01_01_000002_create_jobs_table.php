<?php

// --- Imports ---
use Illuminate\Database\Migrations\Migration; // Classe parente des migrations
use Illuminate\Database\Schema\Blueprint;     // Pour définir les colonnes d'une table
use Illuminate\Support\Facades\Schema;        // Pour créer/supprimer des tables

/**
 * Fichier : database/migrations/xxxx_create_jobs_table.php
 *
 * Cette migration crée les tables pour le système de QUEUES (files d'attente) de Laravel
 * C'est quoi une queue ? C'est une façon de mettre des tâches lourdes en arrière-plan
 * plutôt que de faire attendre l'utilisateur
 * Ex: envoyer un email, générer un PDF, traiter une image...
 */
return new class extends Migration
{
    /**
     * up() — On crée les 3 tables
     * Lance avec : php artisan migrate
     */
    public function up(): void
    {
        // -------------------------------------------------------
        // TABLE "jobs" — La file d'attente principale
        // Stocke les tâches en attente d'être exécutées
        // -------------------------------------------------------
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();                                       // ID unique du job
            $table->string('queue')->index();                   // Nom de la file (on peut avoir plusieurs files : "emails", "exports"...)
                                                                // index() pour retrouver rapidement les jobs d'une file
            $table->longText('payload');                        // Le contenu du job sérialisé (la tâche à faire)
            $table->unsignedTinyInteger('attempts');            // Nombre de tentatives d'exécution (si ça plante, on réessaie)
            $table->unsignedInteger('reserved_at')->nullable(); // Timestamp quand un worker a pris le job en charge (null = pas encore pris)
            $table->unsignedInteger('available_at');            // Timestamp à partir duquel le job peut être exécuté
            $table->unsignedInteger('created_at');              // Timestamp de création du job
        });

        // -------------------------------------------------------
        // TABLE "job_batches" — Les lots de jobs
        // Quand on veut grouper plusieurs jobs ensemble et suivre leur avancement
        // Ex: envoyer 500 emails → un batch de 500 jobs
        // -------------------------------------------------------
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();             // ID unique du batch
            $table->string('name');                      // Nom du batch pour s'y retrouver
            $table->integer('total_jobs');               // Nombre total de jobs dans ce batch
            $table->integer('pending_jobs');             // Nombre de jobs pas encore traités
            $table->integer('failed_jobs');              // Nombre de jobs qui ont planté
            $table->longText('failed_job_ids');          // Liste des IDs des jobs en échec
            $table->mediumText('options')->nullable();   // Options supplémentaires du batch (sérialisées)
            $table->integer('cancelled_at')->nullable(); // Timestamp si le batch a été annulé (null = pas annulé)
            $table->integer('created_at');               // Timestamp de création du batch
            $table->integer('finished_at')->nullable();  // Timestamp de fin (null = pas encore terminé)
        });

        // -------------------------------------------------------
        // TABLE "failed_jobs" — Le cimetière des jobs ratés
        // Stocke les jobs qui ont échoué pour pouvoir les analyser ou les relancer
        // Commande pour les relancer : php artisan queue:retry all
        // -------------------------------------------------------
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();                                  // ID unique
            $table->string('uuid')->unique();              // Identifiant universel unique du job raté
            $table->text('connection');                    // Connexion utilisée (ex: "database", "redis"...)
            $table->text('queue');                         // Nom de la file où le job a planté
            $table->longText('payload');                   // Le contenu du job (pour pouvoir le relancer)
            $table->longText('exception');                 // Le message d'erreur complet — pratique pour débugger !
            $table->timestamp('failed_at')->useCurrent(); // Date/heure de l'échec (remplie automatiquement)
        });
    }

    /**
     * down() — On supprime les tables
     * Lance avec : php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};