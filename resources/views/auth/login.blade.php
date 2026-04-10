@extends('layouts.app')

@section('title', 'Connexion')
@section('body_class', 'page-login page-gradient')

@section('content')
    <div class="login-card">
        <h2 class="login-title">Connexion</h2>
        <p class="login-subtitle">Veuillez vous connecter pour accéder au tableau de bord.</p>

        <form method="post" action="#">
            @csrf

            <label class="field">
                <span>Email</span>
                <input type="email" name="email" placeholder="exemple@mail.com" required>
            </label>

            <label class="field">
                <span>Mot de passe</span>
                <input type="password" name="password" placeholder="••••••••" required>
            </label>

            <div class="login-help">
                <a class="login-link" href="#">Mot de passe oublié ?</a>
            </div>

            <div class="login-actions">
                <button class="btn btn-secondary" type="submit">Se connecter</button>
            </div>
        </form>
    </div>
@endsection
