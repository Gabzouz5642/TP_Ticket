<?php

// --- Imports ---
use Illuminate\Database\Migrations\Migration; // Classe parente des migrations
use Illuminate\Database\Schema\Blueprint;     // Pour définir les colonnes d'une table
use Illuminate\Support\Facades\Schema;        // Pour modifier des tables existantes

/**
 * Fichier : database/migrations/xxxx_add_project_id_to_tickets_table.php
 *
 * Cette migration N'EST PAS une création de table
 * Elle MODIFIE la table "tickets" qui existe déjà
 * Elle ajoute la vraie colonne project_id qu'on avait pas au départ
 *
 * C'est le correctif du problème qu'on avait noté dans la migration précédente
 * avec le "project_index" qui n'était pas une vraie clé étrangère !
 */
return new class extends Migration
{
    /**
     * up() — On ajoute la colonne project_id à la table tickets
     * Lance avec : php artisan migrate
     *
     * Notez le Schema::table() au lieu de Schema::create()
     * → on modifie une table existante, on n'en crée pas une nouvelle
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Ajoute une colonne "project_id" :
            // - unsignedBigInteger = nombre entier positif (compatible avec le type d'id de la table projects)
            // - nullable() = un ticket peut exister sans être lié à un projet
            // - after('assignees') = la colonne sera placée juste après la colonne "assignees" dans la table
            $table->unsignedBigInteger('project_id')->nullable()->after('assignees');

            // Ajoute un index sur project_id
            // → optimise les recherches du type "donne moi tous les tickets du projet 5"
            // Sans index, Laravel devrait scanner toute la table — avec index c'est beaucoup plus rapide
            $table->index('project_id');
        });
    }

    /**
     * down() — On annule les modifications
     * Lance avec : php artisan migrate:rollback
     * Supprime dans l'ordre inverse : d'abord l'index, ensuite la colonne
     * (on ne peut pas supprimer une colonne indexée sans d'abord supprimer l'index)
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['project_id']); // 1. On supprime l'index en premier
            $table->dropColumn('project_id');  // 2. Ensuite on supprime la colonne
        });
    }
};