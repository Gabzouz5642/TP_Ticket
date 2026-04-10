<?php

namespace App\Http\Controllers;

// --- Imports ---
use App\Models\Project; // Modèle représentant la table "projects" en BDD
use App\Models\Ticket;  // Modèle représentant la table "tickets" en BDD
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Prépare toutes les données nécessaires et affiche la page dashboard
     * Appelée par : GET /dashboard
     */
    public function dashboard(Request $request)
    {
        // --- Récupération de toutes les données en BDD ---
        $tickets  = Ticket::orderByDesc('created_at')->get();  // Tous les tickets, du plus récent au plus ancien
        $projects = Project::orderBy('created_at')->get();     // Tous les projets, du plus ancien au plus récent


        // -------------------------------------------------------
        // SECTION 1 : Statistiques globales des tickets
        // -------------------------------------------------------

        $totalTickets      = $tickets->count();                                  // Nombre total de tickets
        $newTickets        = $tickets->where('status', 'Nouveau')->count();      // Tickets avec le statut "Nouveau"
        $inProgressTickets = $tickets->where('status', 'En cours')->count();     // Tickets avec le statut "En cours"

        // Tickets terminés : on additionne les deux graphies possibles du mot "Terminé"
        // (l'une avec accent, l'autre sans — sécurité contre les problèmes d'encodage en BDD)
        $doneTickets = $tickets->where('status', 'Termine')->count()
                     + $tickets->where('status', 'Terminé')->count();


        // -------------------------------------------------------
        // SECTION 2 : Calcul des heures globales (tous projets)
        // -------------------------------------------------------

        // Limite d'heures incluses définie dans la config Laravel (par défaut : 50h)
        $includedLimit = (float) config('app.included_hours', 50);

        // Additionne les heures des tickets dont le type contient le mot "inclus"
        $includedHours = $tickets->filter(function ($ticket) {
            return str_contains(strtolower($ticket->ticket_type), 'inclus');
        })->sum('actual_time');

        // Additionne les heures des tickets dont le type contient le mot "facturable"
        $billableHours = $tickets->filter(function ($ticket) {
            return str_contains(strtolower($ticket->ticket_type), 'facturable');
        })->sum('actual_time');

        // Heures restantes = limite incluse - heures déjà consommées
        $remainingHours = $includedLimit - $includedHours;


        // -------------------------------------------------------
        // SECTION 3 : Résumé par projet
        // -------------------------------------------------------

        $projectSummaries    = []; // Tableau qui va contenir le résumé de chaque projet
        $totalProjects       = 0;
        $totalIncludedHours  = 0.0;
        $totalRemainingHours = 0.0;
        $totalBillableHours  = 0.0;

        foreach ($projects as $project) {

            // Filtre les tickets qui appartiennent à ce projet
            $projectTickets = $tickets->where('project_id', $project->id);

            // Heures incluses pour ce projet
            $included = $projectTickets->filter(function ($ticket) {
                return str_contains(strtolower($ticket->ticket_type), 'inclus');
            })->sum('actual_time');

            // Heures facturables pour ce projet
            $billable = $projectTickets->filter(function ($ticket) {
                return str_contains(strtolower($ticket->ticket_type), 'facturable');
            })->sum('actual_time');

            // Heures incluses prévues dans le contrat du projet (0 si non défini)
            $hoursIncluded = (float)($project->hours_included ?? 0);

            // Heures restantes = heures prévues - heures déjà consommées
            $remaining = $hoursIncluded - $included;

            // Transforme la liste des collaborateurs (chaîne "Alice, Bob") en tableau ['Alice', 'Bob']
            $collaborators = [];
            if (!empty($project->collaborators)) {
                // Si le champ collaborateurs n'est pas vide, on le découpe par virgule
                $collaborators = array_map('trim', explode(',', $project->collaborators));
            }

            // Construit le résumé du projet et l'ajoute au tableau
            $projectSummaries[] = [
                'id'             => $project->id,
                'nom'            => $project->name ?? '',
                'client'         => $project->client ?? '',
                'collaborateurs' => $collaborators,
                'heures_incluses'=> $hoursIncluded,
                'taux_horaire'   => (float)($project->hourly_rate ?? 0),
                'included_hours' => $included,
                'billable_hours' => $billable,
                'remaining_hours'=> $remaining,
                'tickets'        => $projectTickets, // Les tickets liés à ce projet
            ];

            // Mise à jour des totaux globaux
            $totalProjects++;
            $totalIncludedHours  += $hoursIncluded;
            $totalRemainingHours += $remaining;
            $totalBillableHours  += $billable;
        }


        // -------------------------------------------------------
        // SECTION 4 : Envoi des données à la vue "dashboard"
        // -------------------------------------------------------
        // compact() transforme les variables en tableau associatif pour la vue Blade
        return view('dashboard', compact(
            'tickets',
            'totalTickets',
            'newTickets',
            'inProgressTickets',
            'doneTickets',
            'includedLimit',
            'includedHours',
            'billableHours',
            'remainingHours',
            'projectSummaries',
            'totalProjects',
            'totalIncludedHours',
            'totalRemainingHours',
            'totalBillableHours'
        ));
    }
}