<?php

namespace Database\Factories;

// --- Imports ---
use App\Models\User;                                        // Le modèle que cette factory va créer
use Illuminate\Database\Eloquent\Factories\Factory;        // Classe parente de toutes les factories
use Illuminate\Support\Facades\Hash;                       // Pour hasher les mots de passe
use Illuminate\Support\Str;                                // Pour générer des chaînes aléatoires

/**
 * Fichier : database/factories/UserFactory.php
 *
 * Une Factory sert à générer de FAUX utilisateurs pour :
 * - Les tests automatisés
 * - Remplir la BDD de données fictives (seeders)
 *
 * Utilisation : User::factory()->create()
 *               User::factory(10)->create() → crée 10 utilisateurs
 *
 * @extends Factory<User> → précise que cette factory génère des objets User
 */
class UserFactory extends Factory
{
    /**
     * Stocke le mot de passe hashé pour ne le calculer qu'une seule fois
     * Le "?" devant string signifie que la valeur peut être null
     * "static" signifie qu'elle est partagée entre toutes les instances de la factory
     */
    protected static ?string $password;

    /**
     * Définit les valeurs par défaut d'un faux utilisateur
     * Appelée automatiquement par Laravel lors de la création via factory
     */
    public function definition(): array
    {
        return [
            // Génère un faux nom aléatoire (ex: "Jean Dupont")
            'name' => fake()->name(),

            // Génère une fausse adresse email unique et valide (ex: "jean.dupont@example.com")
            // unique() garantit qu'on n'aura jamais deux fois le même email
            'email' => fake()->unique()->safeEmail(),

            // L'email est considéré comme vérifié immédiatement (date = maintenant)
            // Si null, l'utilisateur devrait vérifier son email
            'email_verified_at' => now(),

            // Hash le mot de passe "password" une seule fois et le réutilise pour tous les faux users
            // "??=" signifie : si $password est null, on le calcule et on le stocke, sinon on réutilise
            // Optimisation : évite de hasher le même mot de passe des dizaines de fois
            'password' => static::$password ??= Hash::make('password'),

            // Génère un token aléatoire de 10 caractères pour le "se souvenir de moi"
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * État alternatif : crée un utilisateur avec un email NON vérifié
     *
     * Utilisation : User::factory()->unverified()->create()
     *
     * state() permet de surcharger certains champs de definition()
     * Ici on écrase uniquement email_verified_at en le mettant à null
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null, // Email non vérifié
        ]);
    }
}