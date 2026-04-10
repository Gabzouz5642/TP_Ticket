<?php

namespace App\Models;

// --- Import ---
use Illuminate\Database\Eloquent\Model; // Classe parente de tous les modèles Laravel

/**
 * Modèle représentant la table "projects" en base de données
 * Chaque instance de cette classe = une ligne dans la table "projects"
 */
class Project extends Model
{
    /**
     * Liste des champs autorisés à être remplis en masse (mass assignment)
     * Sécurité Laravel : seuls ces champs peuvent être utilisés avec create() ou update()
     * Si un champ n'est pas listé ici, Laravel ignorera toute tentative de le remplir
     */
    protected $fillable = [
        'name',           // Nom du projet
        'client',         // Nom du client
        'collaborators',  // Collaborateurs (stockés en texte : "Alice, Bob")
        'hours_included', // Nombre d'heures incluses dans le contrat
        'hourly_rate',    // Taux horaire du projet
    ];

    /**
     * Définit comment Laravel convertit automatiquement certains champs
     * quand on les lit depuis la base de données
     * Ici, les deux champs seront toujours retournés comme des décimaux à 2 chiffres après la virgule
     * Ex: 50 en BDD → 50.00 en PHP
     */
    protected $casts = [
        'hours_included' => 'decimal:2', // Ex: 49.50
        'hourly_rate'    => 'decimal:2', // Ex: 75.00
    ];
}