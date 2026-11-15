<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réinitialiser le mot de passe · Patrimoine de l'État · République Togolaise</title>
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
                <p class="login-eyebrow">NOUVEAU MOT DE PASSE</p>
                <h1 class="login-title">Réinitialisation</h1>
                <p class="login-subtitle">
                    Choisissez un nouveau mot de passe pour votre compte
                    <strong>{{ $email }}</strong>.
                </p>

                @if ($errors->any())
                    <div class="flash-error login-flash" role="alert">
                        <span>⚠</span> {{ $errors->first() }}
                    </div>
                @endif

                <form class="login-form" method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="login-field">
                        <label for="email">Adresse e-mail</label>
                        <div class="login-input-wrap">
                            <span class="login-input-icon">✉</span>
                            <input id="email"
                                   name="email"
                                   type="email"
                                   autocomplete="username"
                                   value="{{ old('email', $email) }}"
                                   required>
                        </div>
                    </div>

                    <div class="login-field">
                        <label for="password">Nouveau mot de passe</label>
                        <div class="login-input-wrap">
                            <span class="login-input-icon">◉</span>
                            <input id="password"
                                   name="password"
                                   type="password"
                                   autocomplete="new-password"
                                   required
                                   placeholder="12 caractères min. · majuscules, minuscules, chiffres">
                        </div>
                    </div>

                    <div class="login-field">
                        <label for="password_confirmation">Confirmer le mot de passe</label>
                        <div class="login-input-wrap">
                            <span class="login-input-icon">◉</span>
                            <input id="password_confirmation"
                                   name="password_confirmation"
                                   type="password"
                                   autocomplete="new-password"
                                   required
                                   placeholder="••••••••••••">
                        </div>
                    </div>

                    <button class="login-submit" type="submit">
                        <span>Enregistrer le mot de passe</span>
                        <span class="login-submit-arrow">→</span>
                    </button>
                </form>

                <div class="login-security">
                    <span class="login-security-icon">◆</span>
                    <span>Mot de passe : 12 caractères minimum, lettres, chiffres</span>
                </div>

                <p class="login-back-link">
                    <a href="{{ route('login') }}">← Retour à la connexion</a>
                </p>
            </div>
        </section>
    </main>

</body>
</html>
