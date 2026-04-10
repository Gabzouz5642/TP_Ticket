<?php

namespace App\Http\Controllers\Api; // Adresse de la classe dans le projet Laravel

// --- Imports des dépendances ---
use App\Http\Controllers\Controller; // Classe parente de base Laravel
use App\Models\Ticket;               // Modèle représentant la table "tickets" en BDD
use Illuminate\Http\Request;         // Permet de lire les données d'une requête HTTP

class TicketApiController extends Controller
{
    /**
     * Retourne la liste de tous les tickets (du plus récent au plus ancien)
     * Appelée par : GET /api/tickets
     */
    public function index()
    {
        // Récupère tous les tickets triés par date de création décroissante
        $tickets = Ticket::orderByDesc('created_at')->get();

        // Retourne les tickets en JSON
        return response()->json([
            'data' => $tickets,
        ]);
    }

    /**
     * Crée un nouveau ticket en base de données
     * Appelée par : POST /api/tickets
     */
    public function store(Request $request)
    {
        // --- Validation des données reçues ---
        // Si une règle échoue, Laravel renvoie automatiquement une erreur 422
        $data = $request->validate([
            'title'       => 'required|string|max:255', // Obligatoire, texte, max 255 caractères
            'status'      => 'required|string|max:255', // Obligatoire, texte, max 255 caractères
            'priority'    => 'required|string|max:255', // Obligatoire, texte, max 255 caractères
            'ticket_type' => 'required|string|max:255', // Obligatoire, texte, max 255 caractères
            'actual_time' => 'required|numeric|min:0',  // Obligatoire, nombre, minimum 0
            'description' => 'nullable|string',         // Optionnel, texte libre
        ]);

        // --- Insertion en base de données ---
        $ticket = Ticket::create([
            'title'       => $data['title'],
            'status'      => $data['status'],
            'priority'    => $data['priority'],
            'ticket_type' => $data['ticket_type'],
            'actual_time' => $data['actual_time'],

            // Si 'description' n'est pas fourni, on stocke NULL
            'description' => $data['description'] ?? null,

            // ⚠️ billable_flag prend la valeur de ticket_type (à vérifier si intentionnel)
            'billable_flag' => $data['ticket_type'],
        ]);

        // --- Retourne le ticket créé en JSON avec le code HTTP 201 (= Créé avec succès) ---
        return response()->json([
            'message' => 'Ticket created',
            'data'    => $ticket,
        ], 201);
    }
}