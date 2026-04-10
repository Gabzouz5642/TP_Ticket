@extends('layouts.app')

@section('title', 'Nouveau projet')
@section('body_class', 'page-new-project page-gradient')

@section('content')
    <header class="page-header">
        <h1>Nouveau projet</h1>
        <p>Créez un projet et définissez son cadre contractuel</p>
    </header>

    <form class="ticket-form" action="{{ route('projets.store') }}" method="post">
        @csrf

        <section class="card">
            <h2>Informations projet</h2>
            <label class="field">
                <span>Nom du projet*</span>
                <input type="text" name="project_name" placeholder="Ex. Refonte site" required>
            </label>

            <label class="field">
                <span>Client*</span>
                <input type="text" name="client_name" placeholder="Ex. ACME" required>
            </label>

            <label class="field">
                <span>Collaborateurs*</span>
                <div class="chip-group">
                    <label class="chip"><input type="checkbox" name="collaborators[]" value="Alan"> Alan</label>
                    <label class="chip"><input type="checkbox" name="collaborators[]" value="Morgane"> Morgane</label>
                    <label class="chip"><input type="checkbox" name="collaborators[]" value="Baptiste"> Baptiste</label>
                    <label class="chip"><input type="checkbox" name="collaborators[]" value="Adam"> Adam</label>
                </div>
            </label>
        </section>

        <section class="card">
            <h2>Contrat & heures</h2>
            <div class="grid-2">
                <label class="field">
                    <span>Heures incluses*</span>
                    <input type="number" name="hours_included" min="0" step="0.25" placeholder="50" required>
                </label>

                <label class="field">
                    <span>Taux horaire </span>
                    <input type="number" name="hourly_rate" min="0" step="0.01" placeholder="80" required>
                </label>
            </div>
        </section>

        <section class="actions">
            <button class="btn btn-secondary" type="button" onclick="window.location='{{ route('dashboard') }}'">Annuler</button>
            <button class="btn btn-primary" type="submit">Créer le projet</button>
        </section>
    </form>
@endsection
