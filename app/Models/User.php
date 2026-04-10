<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// ^ Décommenté, cette interface forcerait la vérification d'email à l'inscription

// --- Imports ---
use Database\Factories\UserFactory;                        // Factory pour générer des faux utilisateurs (tests)
use Illuminate\Database\Eloquent\Factories\HasFactory;     // Trait qui active les factories sur le modèle
use Illuminate\Foundation\Auth\User as Authenticatable;    // Classe parente spéciale pour les utilisateurs avec authentification
use Illuminate\Notifications\Notifiable;                   // Trait qui permet d'envoyer des notifications (email, SMS...)

/**
 * Modèle représentant la table "users" en base de données
 * Hérite de Authenticatable (et non de Model comme les autres)
 * car c'est un modèle d'authentification — il gère login, sessions, mots de passe
 */
class User extends Authenticatable
{
    /**
     * Active deux fonctionnalités supplémentaires via les "traits" :
     * - HasFactory  : permet de créer de faux utilisateurs pour les tests (ex: User::factory()->create())
     * - Notifiable  : permet d'envoyer des notifications à l'utilisateur (ex: reset de mot de passe par email)
     */
    use HasFactory, Notifiable;

    /**
     * Champs autorisés à être remplis en masse (mass assignment)
     * Seuls ces 3 champs peuvent être utilisés avec create() ou update()
     */
    protected $fillable = [
        'name',     // Nom de l'utilisateur
        'email',    // Adresse email (utilisée pour la connexion)
        'password', // Mot de passe (sera automatiquement hashé grâce aux casts)
    ];

    /**
     * Champs masqués lors de la sérialisation (ex: quand on retourne l'utilisateur en JSON)
     * Ces champs existent en BDD mais ne seront JAMAIS exposés dans une réponse API
     * Sécurité importante : on ne veut pas envoyer le mot de passe au front-end
     */
    protected $hidden = [
        'password',       // Le mot de passe hashé ne doit jamais être exposé
        'remember_token', // Token de session "se souvenir de moi" — données sensibles
    ];

    /**
     * Définit comment Laravel convertit automatiquement certains champs
     * Ici sous forme de méthode (plutôt que propriété comme dans les autres modèles)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Converti en objet Carbon (date manipulable) plutôt qu'une simple chaîne
            'password'          => 'hashed',   // Le mot de passe est automatiquement hashé avant d'être sauvegardé en BDD
        ];
    }
}