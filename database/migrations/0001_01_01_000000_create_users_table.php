<?php

// --- Imports ---
use Illuminate\Database\Migrations\Migration; // Classe parente de toutes les migrations
use Illuminate\Database\Schema\Blueprint;     // Permet de définir la structure d'une table
use Illuminate\Support\Facades\Schema;        // Façade pour créer/modifier/supprimer des tables

/**
 * Fichier : database/migrations/xxxx_create_users_table.php
 
 */
return new class extends Migration
{
    /**
     * up() — Création des tables
     * Exécutée lors de : php artisan migrate
     * Crée 3 tables nécessaires au système d'authentification de Laravel
     */
    public function up(): void
    {
        // -------------------------------------------------------
        // TABLE "users" — Stocke les utilisateurs de l'application
        // -------------------------------------------------------
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                    // Colonne "id" auto-incrémentée (clé primaire)
            $table->string('name');                          // Nom de l'utilisateur
            $table->string('email')->unique();               // Email unique (impossible d'avoir 2 fois le même)
            $table->timestamp('email_verified_at')->nullable(); // Date de vérification email (null = non vérifié)
            $table->string('password');                      // Mot de passe hashé
            $table->rememberToken();                         // Token "se souvenir de moi" (colonne remember_token)
            $table->timestamps();                            // Crée automatiquement created_at et updated_at
        });

        // -------------------------------------------------------
        // TABLE "password_reset_tokens" — Gère la réinitialisation de mot de passe
        // Stocke les tokens temporaires envoyés par email quand un user clique "Mot de passe oublié"
        // -------------------------------------------------------
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Email comme clé primaire (1 token max par email)
            $table->string('token');            // Token secret envoyé par email
            $table->timestamp('created_at')->nullable(); // Date de création du token (pour l'expiration)
        });

        // -------------------------------------------------------
        // TABLE "sessions" — Stocke les sessions actives des utilisateurs
        // Permet à Laravel de garder les utilisateurs connectés entre les requêtes
        // -------------------------------------------------------
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();            // Identifiant unique de la session (clé primaire)
            $table->foreignId('user_id')->nullable()->index(); // ID de l'utilisateur connecté (null si visiteur anonyme)
                                                               // index() = optimise les recherches sur ce champ
            $table->string('ip_address', 45)->nullable(); // Adresse IP (45 caractères pour supporter IPv6)
            $table->text('user_agent')->nullable();       // Navigateur/appareil utilisé (ex: "Chrome on Windows")
            $table->longText('payload');                  // Données de session sérialisées (contenu complet)
            $table->integer('last_activity')->index();    // Timestamp de la dernière activité (pour expirer les sessions)
                                                          // index() = optimise les recherches pour nettoyer les vieilles sessions
        });
    }

    /**
     * down() — Suppression des tables
     * Exécutée lors de : php artisan migrate:rollback
     * Annule ce que up() a fait en supprimant les tables
     * "IfExists" = ne plante pas si la table n'existe pas déjà
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};