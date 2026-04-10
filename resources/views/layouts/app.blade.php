<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promatic - @yield('title')</title>
    {{-- On charge le nouveau CSS --}}
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/projects.css') }}">
    {{-- On garde le CSS des tickets au cas où on est sur la page création --}}
    <link rel="stylesheet" href="{{ asset('css/NewTicket.css') }}">
</head>
<body class="@yield('body_class')">
    <header class="top">
        {{-- Zone pour le bouton de gauche --}}
        <div class="header-side">
            @yield('header_actions')
        </div>

        <h1>Promatic</h1>

        {{-- Zone pour le bouton de droite --}}
        <div class="header-side">
            @yield('header_actions_right')
        </div>
    </header>

    <main class="page">
        @yield('content')
    </main>
    
    {{-- Scripts JS si besoin --}}
    @stack('scripts')
</body>
</html>
