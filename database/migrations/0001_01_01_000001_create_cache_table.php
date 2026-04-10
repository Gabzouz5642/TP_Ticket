<?php

// --- Imports ---
use Illuminate\Database\Migrations\Migration; // Classe parente des migrations
use Illuminate\Database\Schema\Blueprint;     // Pour définir les colonnes d'une table
use Illuminate\Support\Facades\Schema;        // Pour créer/supprimer des tables

/**
 * Fichier : database/migrations/xxxx_create_cache_table.php
 *
 * Cette migration crée les tables qui gèrent le système de CACHE de Laravel
 * Le cache c'est quoi ? C'est une mémoire temporaire pour stocker des résultats
 * et éviter de refaire les mêmes calculs ou requêtes BDD à chaque fois
 */
return new class extends Migration
{
    /**
     * up() — On crée les tables
     * Lance avec : php artisan migrate
     */
    public function up(): void
    {
        // -------------------------------------------------------
        // TABLE "cache" — Le cache en lui-même
        // Stocke des données temporaires pour aller plus vite
        // Ex: résultat d'une requête lourde qu'on veut pas refaire 50 fois
        // -------------------------------------------------------
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary(); // La clé unique pour retrouver la donnée (ex: "tickets_list")
            $table->mediumText('value');      // La donnée stockée (peut être assez volumineuse)
            $table->integer('expiration')->index(); // Timestamp d'expiration — passé ce délai, la donnée est périmée
                                                    // index() pour retrouver/nettoyer les entrées expirées rapidement
        });

        // -------------------------------------------------------
        // TABLE "cache_locks" — Les verrous du cache
        // Evite que deux processus modifient le même cache en même temps
        // Ex: si deux users déclenchent le même calcul lourd simultanément,
        // le "lock" fait patienter le second pendant que le premier termine
        // -------------------------------------------------------
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary(); // Identifiant du verrou (même nom que la clé du cache)
            $table->string('owner');          // Qui a posé le verrou (pour savoir qui doit le lever)
            $table->integer('expiration')->index(); // Expiration du verrou — sécurité si le process plante sans lever le verrou
        });
    }

    /**
     * down() — On supprime les tables
     * Lance avec : php artisan migrate:rollback
     * Annule exactement ce que up() a fait
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};