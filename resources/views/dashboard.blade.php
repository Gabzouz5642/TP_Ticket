{{-- 
    Fichier : resources/views/dashboard.blade.php
    Vue principale du tableau de bord — la page centrale de l'application
    Affiche les tickets, les projets et les statistiques
--}}

@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('body_class', 'page-dashboard page-gradient')

{{-- Bouton "Nouveau projet" injecté dans le côté GAUCHE de la navbar --}}
@section('header_actions')
    <a href="{{ route('projets.create') }}" class="nav-btn">
        <h2>Nouveaux projets</h2>
    </a>
@endsection

{{-- Bouton "Nouveau ticket" injecté dans le côté DROIT de la navbar --}}
@section('header_actions_right')
    <a href="{{ route('tickets.create') }}" class="nav-btn">
        <h2>Nouveau Ticket</h2>
    </a>
@endsection


@section('content')
<div class="dashboard-layout">

    {{-- ============================================================
         SECTION PRINCIPALE — TABLEAU DES TICKETS
         ============================================================ --}}
    <section class="section dashboard-main">
        <h2>Tickets</h2>
        <p class="muted">Liste des tickets enregistres depuis le formulaire.</p>

        {{-- Formulaire de création rapide de ticket (sans quitter le dashboard)
             Soumis en JavaScript via fetch() vers l'API — pas de rechargement de page --}}
        <form id="quick-ticket-form" class="quick-ticket-form">
            <input type="text" name="title" placeholder="Titre" required>
            <select name="status" required>
                <option value="">Statut</option>
                <option>Nouveau</option>
                <option>En cours</option>
                <option>Termine</option>
                <option>Valide</option>
                <option>Refuse</option>
            </select>
            <select name="priority" required>
                <option value="">Priorite</option>
                <option>Faible</option>
                <option>Moyenne</option>
                <option>Haute</option>
                <option>Critique</option>
            </select>
            <select name="ticket_type" required>
                <option value="">Type</option>
                <option>Inclus (contrat)</option>
                <option>Facturable</option>
            </select>
            <input type="number" name="actual_time" min="0" step="0.25" placeholder="Temps (h)" required>
            <button class="btn btn-secondary" type="submit">Ajouter</button>
        </form>

        {{-- Boutons de filtre par type de ticket
             data-filter → valeur lue par le JS pour filtrer les lignes du tableau --}}
        <div class="type-filter">
            <button type="button" class="filter-btn is-active" data-filter="all">Tous</button>
            <button type="button" class="filter-btn" data-filter="inclus">Inclus</button>
            <button type="button" class="filter-btn" data-filter="facturable">Facturable</button>
        </div>

        {{-- Tableau des tickets --}}
        <div class="table-wrap">
            <table class="tickets-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th>Priorite</th>
                        <th>Type</th>
                        <th>Temps reel (h)</th>
                        <th>Collaborateurs</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tickets-body">

                    {{-- @forelse = @foreach mais avec un cas @empty si la liste est vide
                         $tickets ?? [] → tableau vide si $tickets n'est pas défini --}}
                    @forelse($tickets ?? [] as $ticket)
                        @php
                            // Convertit la priorité en classe CSS pour colorier la ligne
                            $priority = strtolower($ticket->priority ?? '');
                            $priorityClass = match ($priority) {
                                'faible'   => 'priority-low',
                                'moyenne'  => 'priority-medium',
                                'haute'    => 'priority-high',
                                'critique' => 'priority-critical',
                                default    => 'priority-default', // Blanc si priorité inconnue
                            };

                            // Détermine le type pour le filtre JS
                            // Si le type contient "facturable" → 'facturable', sinon → 'inclus'
                            $typeRaw = strtolower($ticket->ticket_type ?? '');
                            $typeKey = str_contains($typeRaw, 'facturable') ? 'facturable' : 'inclus';
                        @endphp

                        {{-- data-type → utilisé par le JS pour filtrer les lignes --}}
                        <tr class="{{ $priorityClass }}" data-type="{{ $typeKey }}">
                            <td>{{ $ticket->title }}</td>
                            <td>{{ $ticket->status }}</td>
                            <td>{{ $ticket->priority }}</td>
                            <td>{{ $ticket->ticket_type }}</td>
                            <td>{{ $ticket->actual_time }}</td>
                            <td>
                                <div class="assignee-bubbles">
                                    {{-- Affiche les assignés ou "Aucun" si vide --}}
                                    <span class="assignee-bubble">{{ $ticket->assignees ?? 'Aucun' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">

                                    {{-- Bouton Modifier → redirige vers la page d'édition du ticket --}}
                                    <a class="btn btn-secondary" href="{{ route('tickets.edit', $ticket->id) }}">Modifier</a>

                                    {{-- Formulaire de suppression
                                         Doit être un <form> car DELETE n'est pas supporté nativement
                                         @method('DELETE') génère un champ caché _method=DELETE --}}
                                    <form action="{{ route('tickets.destroy', $ticket->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit">Supprimer</button>
                                    </form>

                                </div>
                            </td>
                        </tr>

                    {{-- Affiché si $tickets est vide --}}
                    @empty
                        <tr>
                            <td colspan="7" class="empty">Aucun ticket pour le moment.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </section>


    {{-- ============================================================
         SIDEBAR DROITE — STATISTIQUES DES TICKETS
         Données injectées par le PageController
         ?? 0 → affiche 0 si la variable n'est pas définie
         ============================================================ --}}
    <aside class="dashboard-sidebar">

        {{-- Nombre total de tickets --}}
        <div class="stat-card">
            <p class="stat-label"><span class="stat-icon stat-icon--all"></span>Tickets crees</p>
            <p class="stat-value">{{ $totalTickets ?? 0 }}</p>
        </div>

        {{-- Répartition par statut --}}
        <div class="stat-card">
            <p class="stat-label stat-label--new"><span class="stat-icon stat-icon--new"></span>Nouveau</p>
            <p class="stat-value stat-value--new">{{ $newTickets ?? 0 }}</p>
            <div class="stat-divider"></div>
            <p class="stat-label stat-label--progress"><span class="stat-icon stat-icon--progress"></span>En cours</p>
            <p class="stat-value stat-value--progress">{{ $inProgressTickets ?? 0 }}</p>
            <div class="stat-divider"></div>
            <p class="stat-label stat-label--done"><span class="stat-icon stat-icon--done"></span>Termine</p>
            <p class="stat-value stat-value--done">{{ $doneTickets ?? 0 }}</p>
        </div>

    </aside>
</div>


{{-- ============================================================
     SECTION PROJETS
     ============================================================ --}}
<section class="section">
    <h2>Projets</h2>
    <p class="muted">Vue d'ensemble des projets par client.</p>

    <div class="project-layout">

        {{-- SIDEBAR GAUCHE — STATISTIQUES DES PROJETS --}}
        <aside class="project-sidebar">
            @php $remainingTotal = $totalRemainingHours ?? 0; @endphp

            {{-- Nombre de projets --}}
            <div class="project-stat">
                <p class="project-stat-label"><span class="stat-icon stat-icon--projects"></span>Projets crees</p>
                <p class="project-stat-value">{{ $totalProjects ?? 0 }}</p>
            </div>

            {{-- Résumé des heures sur tous les projets --}}
            <div class="project-stat">
                <p class="project-stat-label"><span class="stat-icon stat-icon--hours"></span>Heures incluses</p>
                <p class="project-stat-value">{{ $totalIncludedHours ?? 0 }}h</p>
                <div class="project-stat-divider"></div>

                <p class="project-stat-label"><span class="stat-icon stat-icon--remaining"></span>Heures restantes</p>
                {{-- is-negative (rouge) si on a dépassé le quota, is-positive (vert) sinon --}}
                <p class="project-stat-value {{ $remainingTotal < 0 ? 'is-negative' : 'is-positive' }}">
                    {{ $totalRemainingHours ?? 0 }}h
                </p>
                <div class="project-stat-divider"></div>

                <p class="project-stat-label"><span class="stat-icon stat-icon--billable"></span>Heures facturables</p>
                <p class="project-stat-value">{{ $totalBillableHours ?? 0 }}h</p>
            </div>
        </aside>


        {{-- LISTE DES PROJETS --}}
        <div class="project-main">

            {{-- Si aucun projet → message vide --}}
            @if(empty($projectSummaries))
                <div class="table-wrap">
                    <p class="empty">Aucun projet pour le moment.</p>
                </div>
            @else
                <div class="project-list">
                    @foreach($projectSummaries as $project)
                        <div class="project-card">

                            {{-- En-tête de la carte : infos projet à gauche, heures à droite --}}
                            <div class="project-header">
                                <div>
                                    <h3 class="project-title">{{ $project['nom'] }}</h3>
                                    <p class="project-client">Client : {{ $project['client'] }}</p>
                                </div>

                                {{-- Bloc résumé des heures du projet --}}
                                <div class="project-hours">
                                    @php $remaining = $project['remaining_hours'] ?? 0; @endphp
                                    <p>Inclus : <strong>{{ $project['heures_incluses'] }}h</strong></p>
                                    <p>Consomme : <strong>{{ $project['included_hours'] }}h</strong></p>
                                    {{-- Heures restantes en rouge si dépassement --}}
                                    <p>Restant : <strong class="{{ $remaining < 0 ? 'is-negative' : 'is-positive' }}">{{ $project['remaining_hours'] }}h</strong></p>
                                    <p>Facturable : <strong>{{ $project['billable_hours'] }}h</strong> | {{ $project['taux_horaire'] }}EUR/h</p>
                                </div>
                            </div>

                            {{-- Liste des collaborateurs du projet --}}
                            <div class="project-meta">
                                {{-- implode joint le tableau ['Alice','Bob'] en "Alice, Bob" --}}
                                <p><strong>Collaborateurs :</strong> {{ implode(', ', $project['collaborateurs']) }}</p>
                            </div>

                            {{-- Tickets liés au projet --}}
                            <div class="project-tickets">
                                <p class="project-subtitle">Tickets lies</p>

                                @if($project['tickets']->isEmpty())
                                    <p class="empty">Aucun ticket lie.</p>
                                @else
                                    <ul class="project-ticket-list">
                                        @foreach($project['tickets'] as $pTicket)
                                            <li>
                                                <span>{{ $pTicket->title }}</span>
                                                <span class="ticket-meta">{{ $pTicket->ticket_type }} : {{ $pTicket->actual_time }}h</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</section>


{{-- ============================================================
     JAVASCRIPT DU DASHBOARD
     @push('scripts') → injecte ce script dans la pile 'scripts'
     du layout parent, juste avant </body>
     Tout est dans une IIFE (function(){}()) pour éviter de polluer
     le scope global avec des variables
     ============================================================ --}}
@push('scripts')
<script>
(function () {
    const buttons   = document.querySelectorAll('.filter-btn');
    const tbody     = document.getElementById('tickets-body');
    const quickForm = document.getElementById('quick-ticket-form');

    // Récupère le token CSRF depuis Blade pour l'envoyer dans les requêtes fetch()
    // Laravel refuse toute requête POST/DELETE sans ce token
    const csrfToken = '{{ csrf_token() }}';


    /**
     * Génère le HTML d'une ligne de ticket
     * Appelée après création rapide pour ajouter la ligne sans recharger
     * Reproduit la même logique de couleur que le Blade côté serveur
     */
    function rowHtml(ticket) {
        // Détermine la classe CSS de couleur selon la priorité
        const priority = (ticket.priority || '').toLowerCase();
        const priorityClass = priority === 'faible'    ? 'priority-low'
                            : priority === 'moyenne'   ? 'priority-medium'
                            : priority === 'haute'     ? 'priority-high'
                            : priority === 'critique'  ? 'priority-critical'
                            : 'priority-default';

        // Détermine le type pour le filtre
        const typeRaw = (ticket.ticket_type || '').toLowerCase();
        const typeKey = typeRaw.includes('facturable') ? 'facturable' : 'inclus';

        // Retourne le HTML complet de la ligne avec les boutons Modifier et Supprimer
        return `
            <tr class="${priorityClass}" data-type="${typeKey}">
                <td>${ticket.title ?? ''}</td>
                <td>${ticket.status ?? ''}</td>
                <td>${ticket.priority ?? ''}</td>
                <td>${ticket.ticket_type ?? ''}</td>
                <td>${ticket.actual_time ?? ''}</td>
                <td>
                    <div class="assignee-bubbles">
                        <span class="assignee-bubble">${ticket.assignees ?? 'Aucun'}</span>
                    </div>
                </td>
                <td>
                    <div class="action-buttons">
                        <a class="btn btn-secondary" href="/tickets/${ticket.id}/edit">Modifier</a>
                        <form action="/tickets/${ticket.id}" method="post">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-danger" type="submit">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Active le bouton de filtre cliqué et désactive les autres
     */
    function setActive(btn) {
        buttons.forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');
    }

    /**
     * Filtre les lignes du tableau selon le type sélectionné
     * "all" → tout afficher
     * "inclus" ou "facturable" → masque les lignes qui ne correspondent pas
     */
    function applyFilter(type) {
        const rows = document.querySelectorAll('.tickets-table tbody tr[data-type]');
        rows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            const show    = type === 'all' || rowType === type;
            row.style.display = show ? '' : 'none'; // '' = visible, 'none' = caché
        });
    }

    /**
     * Charge les tickets depuis l'API et redessine le tableau
     * Appelée au chargement de la page et après chaque création rapide
     */
    async function loadTickets() {
        const res   = await fetch('/api/tickets', { headers: { 'Accept': 'application/json' } });
        const data  = await res.json();
        const items = data.data || [];

        // Remplace le contenu du tbody par les nouvelles lignes
        // Si vide → affiche le message "Aucun ticket"
        tbody.innerHTML = items.map(rowHtml).join('') || `
            <tr><td colspan="7" class="empty">Aucun ticket pour le moment.</td></tr>
        `;
    }

    // --- Soumission du formulaire rapide ---
    if (quickForm) {
        quickForm.addEventListener('submit', async (e) => {
            e.preventDefault(); // Empêche le rechargement de la page

            // Récupère les données du formulaire et les convertit en objet JS
            const formData = new FormData(quickForm);
            const payload  = Object.fromEntries(formData.entries());

            // Envoie les données à l'API en JSON
            const res = await fetch('/api/tickets', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload),
            });

            // Si la création a réussi → vide le formulaire et recharge les tickets
            if (res.ok) {
                quickForm.reset();
                await loadTickets();
            }
        });
    }

    // --- Gestion des boutons de filtre ---
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.getAttribute('data-filter');
            setActive(btn);
            applyFilter(type);
        });
    });

    // Chargement initial : récupère les tickets puis applique le filtre "Tous"
    loadTickets().then(() => applyFilter('all'));

})(); // Fin — tout ce qui est dedans reste isolé du reste de la page
</script>
@endpush

@endsection