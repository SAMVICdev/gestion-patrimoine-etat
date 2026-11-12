<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion · Patrimoine de l’État</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
    <main class="login-card">
        <a class="brand login-brand" href="{{ route('login') }}">
            <span class="brand-mark">P</span>
            <span><strong>PATRIMOINE DE L’ÉTAT</strong><small>DOMAINE PUBLIC CENTRAL</small></span>
        </a>
        <p class="eyebrow">ESPACE DE GESTION SÉCURISÉ</p>
        <h1>Bienvenue.</h1>
        <p class="muted">Connectez-vous pour accéder au registre du patrimoine public.</p>
        @if ($errors->any())
            <div class="flash-error" role="alert">{{ $errors->first() }}</div>
        @endif
        <form class="stack-form" method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="email">Adresse e-mail</label>
            <input id="email" name="email" type="email" autocomplete="username" value="{{ old('email') }}" required autofocus>
            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <label class="checkbox-label"><input type="checkbox" name="remember" value="1"> Rester connecté sur cet appareil</label>
            <button class="button button-primary button-wide" type="submit">Se connecter <span>→</span></button>
        </form>
        <div class="login-foot"><span>◆</span> Accès réservé aux utilisateurs habilités</div>
    </main>
</body>
</html>
