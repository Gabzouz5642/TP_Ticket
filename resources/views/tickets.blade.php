@extends('layout')

@section('content')

<h1>Liste des tickets</h1>

<ul>
@foreach ($tickets as $ticket)
    <li>
        <strong>{{ $ticket['titre'] }}</strong> - 
        {{ $ticket['description'] }}
    </li>
@endforeach
</ul>

<h2>Ajouter un ticket</h2>

<form method="POST" action="/ticket">
    @csrf
    <input type="text" name="titre" placeholder="Titre">
    <textarea name="description"></textarea>
    <button type="submit">Envoyer</button>
</form>

@endsection