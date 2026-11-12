<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Patrimoine de l’État') · Domaine public</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('dashboard') }}">
                <span class="brand-mark">P</span>
                <span><strong>PATRIMOINE DE L’ÉTAT</strong><small>DOMAINE PUBLIC CENTRAL</small></span>
            </a>
            <div class="nav-caption">NAVIGATION DOMANIALE</div>
            <nav class="main-nav" aria-label="Navigation principale">
                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>▦</span> Tableau de bord</a>
                <a class="{{ request()->routeIs('biens.*') ? 'active' : '' }}" href="{{ route('biens.index') }}"><span>▤</span> Biens & inventaire</a>
                <a class="{{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><span>◫</span> Catégories</a>
                <a class="{{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}"><span>⌂</span> Services publics</a>
            </nav>
            <div class="sidebar-bottom">
                <div class="secure-chip"><span>◆</span> Espace sécurisé <span class="secure-version">TLS</span></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit"><span>↪</span> Déconnexion sécurisée</button>
                </form>
            </div>
        </aside>

        <main class="main-area">
            <header class="topbar">
                <button class="menu-toggle" type="button" aria-label="Ouvrir le menu" aria-expanded="false">☰</button>
                <div class="topbar-context"><span>DIRECTION GÉNÉRALE</span><strong>Patrimoine & domaine public</strong></div>
                <div class="topbar-search"><span>⌕</span><a href="{{ route('biens.index') }}">Rechercher un bien, un code, une localisation…</a></div>
                <div class="topbar-user"><span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span><span><strong>{{ auth()->user()->name }}</strong><small>Gestionnaire</small></span></div>
            </header>

            <div class="page-content">
                @if (session('success'))
                    <div class="flash-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="flash-error" role="alert">Vérifiez les champs signalés puis réessayez.</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
    <script>
        const menuToggle = document.querySelector('.menu-toggle');
        menuToggle?.addEventListener('click', () => {
            const open = document.body.classList.toggle('nav-open');
            menuToggle.setAttribute('aria-expanded', String(open));
        });
    </script>
</body>
</html>
