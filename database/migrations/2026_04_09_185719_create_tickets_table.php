<?php

// --- Imports ---
use Illuminate\Database\Migrations\Migration; // Classe parente des migrations
use Illuminate\Database\Schema\Blueprint;     // Pour définir les colonnes d'une table
use Illuminate\Support\Facades\Schema;        // Pour créer/supprimer des tables

/**
 * Fichier : database/migrations/xxxx_create_tickets_table.php
 *
 * Crée la table "tickets" — c'est LA table principale de ton app !
 * Elle stocke tous les tickets de suivi de temps/tâches
 */
return new class extends Migration
{
    /**
     * up() — On crée la table tickets
     * Lance avec : php artisan migrate
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();                                          // ID unique auto-incrémenté

            $table->string('title');                               // Titre du ticket — obligatoire
            $table->text('description')->nullable();               // Description détaillée — optionnelle (peut être null)

            $table->string('status');                              // Statut actuel (ex: "Nouveau", "En cours", "Terminé")
            $table->string('priority');                            // Priorité (ex: "Haute", "Moyenne", "Basse")
            $table->string('ticket_type');                         // Type (ex: "Inclus", "Facturable")

            // decimal(8, 2) = nombre avec max 8 chiffres dont 2 après la virgule → ex: 999999.99
            $table->decimal('estimated_time', 8, 2)->nullable();   // Temps estimé en heures — optionnel
            $table->decimal('actual_time', 8, 2);                  // Temps réellement passé — obligatoire

            $table->string('billable_flag');                       // Indique si le ticket est facturable ou non
            $table->string('client_decision')->nullable();         // Décision du client — optionnelle
            $table->string('client_comment')->nullable();          // Commentaire du client — optionnel
            $table->string('assignees')->nullable();               // Personnes assignées en texte "Alice, Bob" — optionnel

            // !!! Attention : c'est un unsignedInteger classique, pas une vraie clé étrangère (foreignId)
            // Ça veut dire qu'il n'y a pas de contrainte BDD qui vérifie que le projet existe vraiment
            // C'est probablement une version antérieure avant l'ajout de la relation avec projects
            $table->unsignedInteger('project_index')->nullable();  // ID du projet associé — optionnel

            $table->timestamps();                                  // Crée automatiquement created_at et updated_at
        });
    }

    /**
     * down() — On supprime la table
     * Lance avec : php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};