{{-- 
    Fichier : resources/views/tickets/edit.blade.php
    Vue Blade pour la modification d'un ticket existant
    Très similaire à create.blade.php mais avec les valeurs du ticket pré-remplies
--}}

@extends('layouts.app')

@section('title', 'Modifier ticket')
@section('body_class', 'page-new-ticket page-gradient')

@section('content')

    <header class="page-header">
        <h1>Modifier ticket</h1>
        <p>Modifiez les informations du ticket</p>
    </header>

    {{-- Formulaire de modification
         route('tickets.update', $ticket->id) → envoie vers la méthode update() du TicketController
         avec l'ID du ticket dans l'URL (ex: /tickets/5) --}}
    <form class="ticket-form" action="{{ route('tickets.update', $ticket->id) }}" method="post">

        @csrf

        {{-- @method('PUT') est obligatoire car les navigateurs ne supportent que GET et POST
             Cette directive génère un champ caché <input type="hidden" name="_method" value="PUT">
             qui dit à Laravel de traiter cette requête comme un PUT (= mise à jour) --}}
        @method('PUT')


        {{-- ============================================================
             SECTION 1 — INFORMATIONS PRINCIPALES
             Différence clé avec create.blade.php :
             old('champ', $ticket->champ) → affiche d'abord la valeur du ticket existant
             Si le formulaire a été resoumis avec erreur → affiche ce que l'user avait saisi
             ============================================================ --}}
        <section class="card">
            <h2>Informations principales</h2>

            {{-- Sélecteur de projet --}}
            @if($projects->isEmpty())
                <p class="muted">Aucun projet disponible. Vous pouvez modifier le ticket sans projet.</p>
                <p class="muted"><a href="{{ route('projets.create') }}">Creer un projet</a></p>
            @else
                <label class="field">
                    <span>Projet</span>
                    <select name="project_id">
                        <option value="">Selectionner</option>

                        {{-- @php permet d'écrire du PHP pur dans une vue Blade
                             Ici on stocke la valeur dans une variable pour éviter
                             de répéter old('project_id', $ticket->project_id) dans chaque option --}}
                        @php $projectId = old('project_id', $ticket->project_id); @endphp

                        @foreach($projects as $project)
                            {{-- (string) force la comparaison en texte pour éviter les bugs
                                 car $projectId peut être une string et $project->id un integer --}}
                            <option value="{{ $project->id }}" {{ (string)$projectId === (string)$project->id ? 'selected' : '' }}>
                                {{ $project->name }} - {{ $project->client }}
                            </option>
                        @endforeach
                    </select>
                </label>
            @endif

            {{-- Titre — pré-rempli avec la valeur actuelle du ticket --}}
            <label class="field">
                <span>Titre*</span>
                <input type="text" name="title" value="{{ old('title', $ticket->title) }}" required>
            </label>

            {{-- Description — pré-remplie avec la valeur actuelle --}}
            <label class="field">
                <span>Description detaillee*</span>
                <textarea name="description" rows="5" required>{{ old('description', $ticket->description) }}</textarea>
            </label>

            <div class="grid-3">

                {{-- Statut
                     @php stocke la valeur dans $status pour alléger le code dans les options --}}
                <label class="field">
                    <span>Statut*</span>
                    <select name="status" required>
                        <option value="">Selectionner</option>
                        @php $status = old('status', $ticket->status); @endphp
                        <option {{ $status === 'Nouveau'  ? 'selected' : '' }}>Nouveau</option>
                        <option {{ $status === 'En cours' ? 'selected' : '' }}>En cours</option>
                        <option {{ $status === 'Termine'  ? 'selected' : '' }}>Termine</option>
                        <option {{ $status === 'Valide'   ? 'selected' : '' }}>Valide</option>
                        <option {{ $status === 'Refuse'   ? 'selected' : '' }}>Refuse</option>
                    </select>
                </label>

                {{-- Priorité --}}
                <label class="field">
                    <span>Priorite*</span>
                    <select name="priority" required>
                        <option value="">Aucune</option>
                        @php $priority = old('priority', $ticket->priority); @endphp
                        <option {{ $priority === 'Faible'   ? 'selected' : '' }}>Faible</option>
                        <option {{ $priority === 'Moyenne'  ? 'selected' : '' }}>Moyenne</option>
                        <option {{ $priority === 'Haute'    ? 'selected' : '' }}>Haute</option>
                        <option {{ $priority === 'Critique' ? 'selected' : '' }}>Critique</option>
                    </select>
                </label>

                {{-- Type de ticket --}}
                <label class="field">
                    <span>Type*</span>
                    <select name="ticket_type" required>
                        <option value="">Selectionner</option>
                        @php $type = old('ticket_type', $ticket->ticket_type); @endphp
                        <option {{ $type === 'Inclus (contrat)' ? 'selected' : '' }}>Inclus (contrat)</option>
                        <option {{ $type === 'Facturable'       ? 'selected' : '' }}>Facturable</option>
                    </select>
                </label>

            </div>
        </section>


        {{-- ============================================================
             SECTION 2 — TEMPS & SUIVI
             ============================================================ --}}
        <section class="card">
            <h2>Temps & suivi</h2>
            <div class="grid-3">

                {{-- Temps estimé — optionnel, pré-rempli --}}
                <label class="field">
                    <span>Temps estime (h)</span>
                    <input type="number" name="estimated_time" min="0" step="0.25"
                        value="{{ old('estimated_time', $ticket->estimated_time) }}" placeholder="0.00">
                </label>

                {{-- Temps réel — obligatoire, pré-rempli --}}
                <label class="field">
                    <span>Temps reel passe (h)*</span>
                    <input type="number" name="actual_time" min="0" step="0.25"
                        value="{{ old('actual_time', $ticket->actual_time) }}" required>
                </label>

                {{-- Mode de facturation --}}
                <label class="field">
                    <span>Facturation*</span>
                    @php $billable = old('billable_flag', $ticket->billable_flag); @endphp
                    <select name="billable_flag" required>
                        <option value="">Selectionner</option>
                        <option {{ $billable === 'Inclus dans le contrat' ? 'selected' : '' }}>Inclus dans le contrat</option>
                        <option {{ $billable === 'A facturer'             ? 'selected' : '' }}>A facturer</option>
                    </select>
                </label>

            </div>
        </section>


        {{-- ============================================================
             SECTION 3 — COLLABORATEURS ASSIGNÉS
             Différence avec create.blade.php :
             Ici on utilise $assigned (tableau des assignés du ticket existant)
             au lieu de old('assignees', []) qui serait vide sur un nouveau ticket
             $assignees ?? [] → si $assignees n'existe pas, on utilise [] par défaut
             ============================================================ --}}
        <section class="card">
            <h2>Collaborateurs assignes</h2>
            @php $assigned = $assignees ?? []; @endphp
            <div class="chip-group">
                <label class="chip">
                    <input type="checkbox" name="assignees[]" value="Alan"
                        {{ in_array('Alan',     $assigned) ? 'checked' : '' }}> Alan
                </label>
                <label class="chip">
                    <input type="checkbox" name="assignees[]" value="Morgane"
                        {{ in_array('Morgane',  $assigned) ? 'checked' : '' }}> Morgane
                </label>
                <label class="chip">
                    <input type="checkbox" name="assignees[]" value="Baptiste"
                        {{ in_array('Baptiste', $assigned) ? 'checked' : '' }}> Baptiste
                </label>
                <label class="chip">
                    <input type="checkbox" name="assignees[]" value="Adam"
                        {{ in_array('Adam',     $assigned) ? 'checked' : '' }}> Adam
                </label>
            </div>
        </section>


        {{-- ============================================================
             SECTION 4 — VALIDATION CLIENT
             ============================================================ --}}
        <section class="card">
            <h2>Validation client</h2>
            <p class="hint">Les tickets facturables doivent etre valides avant facturation.</p>

            <div class="grid-2">

                {{-- Décision client --}}
                <label class="field">
                    <span>Decision client</span>
                    @php $decision = old('client_decision', $ticket->client_decision); @endphp
                    <select name="client_decision">
                        <option value="">A renseigner</option>
                        <option {{ $decision === 'En attente' ? 'selected' : '' }}>En attente</option>
                        <option {{ $decision === 'Accepte'    ? 'selected' : '' }}>Accepte</option>
                        <option {{ $decision === 'Refuse'     ? 'selected' : '' }}>Refuse</option>
                    </select>
                </label>

                {{-- Commentaire client --}}
                <label class="field">
                    <span>Commentaire</span>
                    <input type="text" name="client_comment"
                        value="{{ old('client_comment', $ticket->client_comment) }}"
                        placeholder="Motif ou precision">
                </label>

            </div>
        </section>


        {{-- ============================================================
             BOUTONS D'ACTION
             Différence avec create.blade.php :
             - "Réinitialiser" est remplacé par "Annuler" qui redirige vers le dashboard
             - onclick="window.location='...'" → redirige en JS vers le dashboard
               (pas un type="reset" car on ne veut pas vider le formulaire, on veut quitter)
             ============================================================ --}}
        <section class="actions">
            <button class="btn btn-secondary" type="button"
                onclick="window.location='{{ route('dashboard') }}'">Annuler</button>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </section>

    </form>

@endsection