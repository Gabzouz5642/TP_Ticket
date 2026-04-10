{{-- 
    Fichier : resources/views/tickets/create.blade.php
    Vue Blade pour la création d'un nouveau ticket
--}}

{{-- Hérite du layout principal --}}
@extends('layouts.app')

@section('title', 'Nouveau ticket')
@section('body_class', 'page-new-ticket page-gradient')

@section('content')

    {{-- En-tête de la page --}}
    <header class="page-header">
        <h1>Nouveau ticket</h1>
        <p>Remplissez ce formulaire pour creer un ticket</p>
    </header>

    {{-- Formulaire principal
         action="{{ route('tickets.store') }}" → envoie les données à la méthode store() du TicketController
         method="post" → envoi sécurisé --}}
    <form class="ticket-form" action="{{ route('tickets.store') }}" method="post">

        {{-- Token de sécurité anti-CSRF obligatoire sur tous les formulaires POST --}}
        @csrf


        {{-- ============================================================
             SECTION 1 — INFORMATIONS PRINCIPALES
             ============================================================ --}}
        <section class="card">
            <h2>Informations principales</h2>

            {{-- Sélecteur de projet
                 @if/@else/@endif = condition Blade
                 Si aucun projet n'existe → affiche un message avec un lien pour en créer un
                 Sinon → affiche la liste déroulante des projets --}}
            @if($projects->isEmpty())
                <p class="muted">Aucun projet disponible. Vous pouvez creer un ticket sans projet.</p>
                <p class="muted"><a href="{{ route('projets.create') }}">Creer un projet</a></p>
            @else
                <label class="field">
                    <span>Projet</span>
                    <select name="project_id">
                        <option value="">Selectionner</option>

                        {{-- Boucle sur tous les projets passés par le contrôleur
                             old('project_id') → réaffiche la valeur si le formulaire a été soumis avec une erreur
                             (évite de tout perdre quand il y a une erreur de validation) --}}
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }} - {{ $project->client }}
                            </option>
                        @endforeach
                    </select>
                </label>
            @endif

            {{-- Titre du ticket
                 old('title') → réaffiche la valeur saisie si erreur de validation --}}
            <label class="field">
                <span>Titre*</span>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="Ex. Export compta" required>
            </label>

            {{-- Description longue --}}
            <label class="field">
                <span>Description detaillee*</span>
                <textarea name="description" rows="5" placeholder="Contexte, besoin, contraintes, livrables attendus..." required>{{ old('description') }}</textarea>
            </label>

            {{-- Grille 3 colonnes : Statut | Priorité | Type
                 Pour chaque select, old('champ') === 'valeur' ? 'selected' : ''
                 remet la bonne option sélectionnée après une erreur de validation --}}
            <div class="grid-3">

                {{-- Statut --}}
                <label class="field">
                    <span>Statut*</span>
                    <select name="status" required>
                        <option value="">Selectionner</option>
                        <option {{ old('status') === 'Nouveau'  ? 'selected' : '' }}>Nouveau</option>
                        <option {{ old('status') === 'En cours' ? 'selected' : '' }}>En cours</option>
                        <option {{ old('status') === 'Termine'  ? 'selected' : '' }}>Termine</option>
                        <option {{ old('status') === 'Valide'   ? 'selected' : '' }}>Valide</option>
                        <option {{ old('status') === 'Refuse'   ? 'selected' : '' }}>Refuse</option>
                    </select>
                </label>

                {{-- Priorité --}}
                <label class="field">
                    <span>Priorite*</span>
                    <select name="priority" required>
                        <option value="">Aucune</option>
                        <option {{ old('priority') === 'Faible'   ? 'selected' : '' }}>Faible</option>
                        <option {{ old('priority') === 'Moyenne'  ? 'selected' : '' }}>Moyenne</option>
                        <option {{ old('priority') === 'Haute'    ? 'selected' : '' }}>Haute</option>
                        <option {{ old('priority') === 'Critique' ? 'selected' : '' }}>Critique</option>
                    </select>
                </label>

                {{-- Type de ticket --}}
                <label class="field">
                    <span>Type*</span>
                    <select name="ticket_type" required>
                        <option value="">Selectionner</option>
                        <option {{ old('ticket_type') === 'Inclus (contrat)' ? 'selected' : '' }}>Inclus (contrat)</option>
                        <option {{ old('ticket_type') === 'Facturable'       ? 'selected' : '' }}>Facturable</option>
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

                {{-- Temps estimé — optionnel, pas de "required"
                     step="0.25" → permet les quarts d'heure (0.25, 0.50, 0.75...) --}}
                <label class="field">
                    <span>Temps estime (h)</span>
                    <input type="number" name="estimated_time" min="0" step="0.25" value="{{ old('estimated_time') }}" placeholder="0.00">
                </label>

                {{-- Temps réel — obligatoire --}}
                <label class="field">
                    <span>Temps reel passe (h)*</span>
                    <input type="number" name="actual_time" min="0" step="0.25" value="{{ old('actual_time') }}" placeholder="0.00" required>
                </label>

                {{-- Mode de facturation --}}
                <label class="field">
                    <span>Facturation*</span>
                    <select name="billable_flag" required>
                        <option value="">Selectionner</option>
                        <option {{ old('billable_flag') === 'Inclus dans le contrat' ? 'selected' : '' }}>Inclus dans le contrat</option>
                        <option {{ old('billable_flag') === 'A facturer'             ? 'selected' : '' }}>A facturer</option>
                    </select>
                </label>

            </div>
        </section>


        {{-- ============================================================
             SECTION 3 — COLLABORATEURS ASSIGNÉS
             Cases à cocher sous forme de chips (badges cliquables)
             name="assignees[]" → le [] indique à PHP que c'est un tableau
             in_array(..., old('assignees', [])) → remet les cases cochées après une erreur
             ============================================================ --}}
        <section class="card">
            <h2>Collaborateurs assignes</h2>
            <div class="chip-group">
                <label class="chip">
                    <input type="checkbox" name="assignees[]" value="Alan"
                        {{ in_array('Alan',    old('assignees', [])) ? 'checked' : '' }}> Alan
                </label>
                <label class="chip">
                    <input type="checkbox" name="assignees[]" value="Morgane"
                        {{ in_array('Morgane', old('assignees', [])) ? 'checked' : '' }}> Morgane
                </label>
                <label class="chip">
                    <input type="checkbox" name="assignees[]" value="Baptiste"
                        {{ in_array('Baptiste',old('assignees', [])) ? 'checked' : '' }}> Baptiste
                </label>
                <label class="chip">
                    <input type="checkbox" name="assignees[]" value="Adam"
                        {{ in_array('Adam',    old('assignees', [])) ? 'checked' : '' }}> Adam
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

                {{-- Décision du client — optionnel --}}
                <label class="field">
                    <span>Decision client</span>
                    <select name="client_decision">
                        <option value="">A renseigner</option>
                        <option {{ old('client_decision') === 'En attente' ? 'selected' : '' }}>En attente</option>
                        <option {{ old('client_decision') === 'Accepte'    ? 'selected' : '' }}>Accepte</option>
                        <option {{ old('client_decision') === 'Refuse'     ? 'selected' : '' }}>Refuse</option>
                    </select>
                </label>

                {{-- Commentaire libre du client — optionnel --}}
                <label class="field">
                    <span>Commentaire</span>
                    <input type="text" name="client_comment" value="{{ old('client_comment') }}" placeholder="Motif ou precision">
                </label>

            </div>
        </section>


        {{-- ============================================================
             BOUTONS D'ACTION
             Réinitialiser → vide tous les champs du formulaire (natif HTML)
             Créer le ticket → soumet le formulaire
             ============================================================ --}}
        <section class="actions">
            <button class="btn btn-secondary" type="reset">Reinitialiser</button>
            <button class="btn btn-primary"   type="submit">Creer le ticket</button>
        </section>

    </form>

@endsection