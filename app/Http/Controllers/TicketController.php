<?php

namespace App\Http\Controllers;

// --- Imports ---
use App\Models\Project;      // Modèle représentant la table "projects" en BDD
use App\Models\Ticket;       // Modèle représentant la table "tickets" en BDD
use Illuminate\Http\Request; // Permet de lire les données d'une requête HTTP

class TicketController extends Controller
{
    /**
     * Affiche le formulaire de création d'un ticket
     * Appelée par : GET /tickets/create
     */
    public function create(Request $request)
    {
        // Récupère tous les projets triés par nom (pour le menu déroulant du formulaire)
        $projects = Project::orderBy('name')->get();

        // Envoie les projets à la vue du formulaire
        return view('tickets.create', compact('projects'));
    }

    /**
     * Valide et enregistre un nouveau ticket en base de données
     * Appelée par : POST /tickets
     */
    public function store(Request $request)
    {
        // --- Validation des données du formulaire ---
        $data = $request->validate([
            'project_id'     => 'nullable|integer|exists:projects,id', // Optionnel, doit exister dans la table projects si fourni
            'title'          => 'required|string|max:255',             // Obligatoire, texte, max 255 caractères
            'description'    => 'nullable|string',                     // Optionnel, texte libre
            'status'         => 'required|string|max:255',             // Obligatoire (ex: "Nouveau", "En cours"...)
            'priority'       => 'required|string|max:255',             // Obligatoire (ex: "Haute", "Basse"...)
            'ticket_type'    => 'required|string|max:255',             // Obligatoire (ex: "Inclus", "Facturable"...)
            'estimated_time' => 'nullable|numeric|min:0',              // Optionnel, nombre >= 0
            'actual_time'    => 'required|numeric|min:0',              // Obligatoire, nombre >= 0
            'billable_flag'  => 'required|string|max:255',             // Obligatoire, indique si facturable
            'client_decision'=> 'nullable|string|max:255',             // Optionnel, décision du client
            'client_comment' => 'nullable|string|max:255',             // Optionnel, commentaire du client
            'assignees'      => 'nullable|array',                      // Optionnel, doit être un tableau
            'assignees.*'    => 'string|max:255',                      // Chaque assigné doit être un texte max 255 caractères
        ]);

        // Récupère les assignés ou tableau vide si non fourni
        $assignees = $data['assignees'] ?? [];

        // --- Création du ticket en base de données ---
        Ticket::create([
            'project_id'     => $data['project_id'] ?? null,
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'status'         => $data['status'],
            'priority'       => $data['priority'],
            'ticket_type'    => $data['ticket_type'],
            'estimated_time' => $data['estimated_time'] ?? null,
            'actual_time'    => $data['actual_time'],
            'billable_flag'  => $data['billable_flag'],
            'client_decision'=> $data['client_decision'] ?? null,
            'client_comment' => $data['client_comment'] ?? null,

            // Si des assignés existent, on les joint en "momo,adam...", sinon NULL
            'assignees'      => $assignees ? implode(', ', $assignees) : null,
        ]);

        // Redirige vers le dashboard avec un message de succès
        return redirect()->route('dashboard')->with('success', 'Ticket cree !');
    }

    /**
     * Affiche le formulaire de modification d'un ticket existant
     * Appelée par : GET /tickets/{ticket}/edit
     * Laravel injecte automatiquement le ticket correspondant à l'ID dans l'URL
     */
    public function edit(Request $request, Ticket $ticket)
    {
        // Récupère tous les projets pour le menu déroulant
        $projects = Project::orderBy('name')->get();

        $assignees = [];

        // Si le ticket a des assignés stockés en BDD (ex: "alan,batou...")
        // on les découpe en tableau ['Alice', 'Bob'] pour pré-remplir le formulaire
        if (!empty($ticket->assignees)) {
            $assignees = array_map('trim', explode(',', $ticket->assignees));
        }

        // Envoie le ticket, ses assignés et les projets à la vue d'édition
        return view('tickets.edit', compact('ticket', 'assignees', 'projects'));
    }

    /**
     * Valide et met à jour un ticket existant en base de données
     * Appelée par : PUT/PATCH /tickets/{ticket}
     */
    public function update(Request $request, Ticket $ticket)
    {
        // --- Validation identique à store() ---
        $data = $request->validate([
            'project_id'     => 'nullable|integer|exists:projects,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'status'         => 'required|string|max:255',
            'priority'       => 'required|string|max:255',
            'ticket_type'    => 'required|string|max:255',
            'estimated_time' => 'nullable|numeric|min:0',
            'actual_time'    => 'required|numeric|min:0',
            'billable_flag'  => 'required|string|max:255',
            'client_decision'=> 'nullable|string|max:255',
            'client_comment' => 'nullable|string|max:255',
            'assignees'      => 'nullable|array',
            'assignees.*'    => 'string|max:255',
        ]);

        $assignees = $data['assignees'] ?? [];

        // --- Mise à jour du ticket en base de données ---
        // Contrairement à store() qui fait un INSERT, update() fait un UPDATE sur le ticket existant
        $ticket->update([
            'project_id'     => $data['project_id'] ?? null,
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'status'         => $data['status'],
            'priority'       => $data['priority'],
            'ticket_type'    => $data['ticket_type'],
            'estimated_time' => $data['estimated_time'] ?? null,
            'actual_time'    => $data['actual_time'],
            'billable_flag'  => $data['billable_flag'],
            'client_decision'=> $data['client_decision'] ?? null,
            'client_comment' => $data['client_comment'] ?? null,

            // Si des assignés existent, on les joint en "momo...", sinon NULL
            'assignees'      => $assignees ? implode(', ', $assignees) : null,
        ]);

        // Redirige vers le dashboard avec un message de succès
        return redirect()->route('dashboard')->with('success', 'Ticket modifie !');
    }

    /**
     * Supprime un ticket de la base de données
     * Appelée par : DELETE /tickets/{ticket}
     * Laravel injecte automatiquement le ticket correspondant à l'ID dans l'URL
     */
    public function destroy(Ticket $ticket)
    {
        // Supprime le ticket de la BDD
        $ticket->delete();

        // Redirige vers le dashboard avec un message de succès
        return redirect()->route('dashboard')->with('success', 'Ticket supprime !');
    }
}