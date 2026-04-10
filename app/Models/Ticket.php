<?php

namespace App\Models;

// --- Import ---
use Illuminate\Database\Eloquent\Model; // Classe parente de tous les modèles Laravel

/**
 * Modèle représentant la table "tickets" en base de données
 * Chaque instance de cette classe = une ligne dans la table "tickets"
 */
class Ticket extends Model
{
    /**
     * Liste des champs autorisés à être remplis en masse (mass assignment)
     * Sécurité Laravel : seuls ces champs peuvent être utilisés avec create() ou update()
     */
    protected $fillable = [
        'title',           // Titre du ticket
        'description',     // Description détaillée (optionnel)
        'status',          // Statut actuel (ex: "Nouveau", "En cours", "Terminé")
        'priority',        // Priorité (ex: "Haute", "Moyenne", "Basse")
        'ticket_type',     // Type de ticket (ex: "Inclus", "Facturable")
        'estimated_time',  // Temps estimé en heures (optionnel)
        'actual_time',     // Temps réellement passé en heures
        'billable_flag',   // Indique si le ticket est facturable
        'client_decision', // Décision du client (optionnel)
        'client_comment',  // Commentaire du client (optionnel)
        'assignees',       // Personnes assignées, stockées en texte : "Alice, Bob" (optionnel)
        'project_id',      // Clé étrangère vers la table "projects" (optionnel)
    ];

    /**
     * Définit comment Laravel convertit automatiquement certains champs
     * quand on les lit depuis la base de données
     */
    protected $casts = [
        'estimated_time' => 'decimal:2', // Ex: 2.50 (toujours 2 chiffres après la virgule)
        'actual_time'    => 'decimal:2', // Ex: 3.75
        'project_id'     => 'integer',   // Toujours retourné comme un entier (ex: 4, pas "4")
    ];
}