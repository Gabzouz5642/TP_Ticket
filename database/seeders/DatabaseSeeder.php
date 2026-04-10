<?php

namespace Database\Seeders;

// --- Imports ---
use App\Models\User;                                              // Le modèle User pour créer un utilisateur
use Illuminate\Database\Console\Seeds\WithoutModelEvents;        // Trait pour désactiver les events pendant le seeding
use Illuminate\Database\Seeder;                                   // Classe parente de tous les seeders

/**
 * Fichier : database/seeders/DatabaseSeeder.php
 *
 * Un Seeder sert à remplir la BDD avec des données de départ
 * C'est utile pour avoir un environnement de dev/test prêt à l'emploi
 * Lance avec : php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    /**
     * WithoutModelEvents désactive tous les "observers" et "events" sur les modèles
     * pendant l'exécution du seeder
     * Ex: si t'as un event qui envoie un email à chaque création d'user,
     * ce trait l'empêche de se déclencher pendant le seed — pas d'emails parasites !
     */
    use WithoutModelEvents;

    /**
     * Méthode principale du seeder — c'est ici qu'on insère les données
     * Lance avec : php artisan db:seed
     */
    public function run(): void
    {
        

        // Crée UN seul utilisateur avec des valeurs fixes
        // Pratique pour avoir toujours le même compte de test disponible
        // Mot de passe par défaut : "password" (défini dans UserFactory)
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}