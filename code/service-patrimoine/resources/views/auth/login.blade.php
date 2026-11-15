<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion · Patrimoine de l'État · République Togolaise</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">

    @include('auth.partials.background')

    <main class="login-wrap">

        @include('auth.partials.identity')

        <section class="login-card">

            <div class="login-mobile-header">
                <div class="login-mobile-coat">
                    <img src="{{ asset('images/blason_rt.png') }}"
                         alt="Blason de la République Togolaise"
                         class="login-mobile-coat-img"/>
                </div>
                <div class="login-mobile-text">
                    <strong>RÉPUBLIQUE TOGOLAISE</strong>
                    <small>Patrimoine de l'État</small>
                    <em>Travail · Liberté · Patrie</em>
                </div>
            </div>

            <div class="login-card-inner">
                <div class="login-card-progress" aria-hidden="true">
                    <span></span><span></span><span></span>
                </div>
                <p class="login-eyebrow">ESPACE DE GESTION SÉCURISÉ</p>
                <h1 class="login-title">Connexion</h1>
                <p class="login-subtitle">
                    Accédez au registre national du patrimoine public.
                </p>

                @if (session('status'))
                    <div class="flash-success login-flash" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="flash-error login-flash" role="alert">
                        <span>⚠</span> {{ $errors->first() }}
                    </div>
                @endif

                <form class="login-form" method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="login-field">
                        <label for="email">Adresse e-mail</label>
                        <div class="login-input-wrap">
                            <span class="login-input-icon">✉</span>
                            <input id="email"
                                   name="email"
                                   type="email"
                                   autocomplete="username"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   placeholder="votre@email.tg">
                        </div>
                    </div>

                    <div class="login-field">
                        <label for="password">Mot de passe</label>
                        <div class="login-input-wrap">
                            <span class="login-input-icon">◉</span>
                            <input id="password"
                                   name="password"
                                   type="password"
                                   autocomplete="current-password"
                                   required
                                   placeholder="••••••••••••">
                        </div>
                    </div>

                    <div class="login-row">
                        <label class="login-remember">
                            <input type="checkbox" name="remember" value="1">
                            <span>Rester connecté sur cet appareil</span>
                        </label>
                        <a class="login-forgot" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                    </div>

                    <button class="login-submit" type="submit">
                        <span>Se connecter</span>
                        <span class="login-submit-arrow">→</span>
                    </button>
                </form>

                <div class="login-security">
                    <span class="login-security-icon">◆</span>
                    <span>Accès réservé aux agents habilités &middot; Connexion chiffrée TLS</span>
                </div>
            </div>
        </section>
    </main>

</body>
</html>
