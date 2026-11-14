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

    {{-- Arrière-plan décoratif --}}
    <div class="login-bg" aria-hidden="true">
        <div class="login-bg-vert"></div>
        <div class="login-bg-jaune"></div>
        <div class="login-bg-orb"></div>
    </div>

    <main class="login-wrap">

        {{-- Colonne gauche : identité nationale --}}
        <aside class="login-identity" aria-hidden="true">

            {{-- Blason officiel de la République Togolaise --}}
            {{-- Drapeau : 5 bandes horizontales vert/jaune, canton rouge avec étoile blanche --}}
            <div class="login-coat">
                <svg class="coat-svg"
                     viewBox="0 0 300 200"
                     fill="none"
                     xmlns="http://www.w3.org/2000/svg"
                     role="img"
                     aria-label="Drapeau de la République Togolaise">

                    {{-- Bandes horizontales (5 bandes : vert jaune vert jaune vert) --}}
                    <rect x="0" y="0"   width="300" height="40"  fill="#006A4A"/>
                    <rect x="0" y="40"  width="300" height="40"  fill="#FFCE00"/>
                    <rect x="0" y="80"  width="300" height="40"  fill="#006A4A"/>
                    <rect x="0" y="120" width="300" height="40"  fill="#FFCE00"/>
                    <rect x="0" y="160" width="300" height="40"  fill="#006A4A"/>

                    {{-- Canton rouge (carré en haut à gauche, hauteur = 3 bandes) --}}
                    <rect x="0" y="0" width="120" height="120" fill="#D21034"/>

                    {{-- Étoile blanche à 5 branches centrée dans le canton --}}
                    <polygon
                        points="60,18 68,44 95,44 73,60 81,86 60,70 39,86 47,60 25,44 52,44"
                        fill="#FFFFFF"/>

                    {{-- Bordure décorative --}}
                    <rect x="0" y="0" width="300" height="200" fill="none" stroke="#ffffff18" stroke-width="2"/>
                </svg>
            </div>

            <div class="login-identity-text">
                <strong class="login-identity-country">RÉPUBLIQUE TOGOLAISE</strong>
                <span class="login-identity-motto">Travail &middot; Liberté &middot; Patrie</span>
                <div class="login-identity-divider"></div>
                <span class="login-identity-ministry">
                    Direction Générale<br>du Patrimoine de l'État
                </span>
                <div class="login-identity-colours" aria-label="Couleurs nationales">
                    <span style="background:#006A4A" title="Vert — espoir, agriculture"></span>
                    <span style="background:#FFCE00" title="Jaune — ressources naturelles"></span>
                    <span style="background:#D21034" title="Rouge — sang versé pour l'indépendance"></span>
                    <span style="background:#FFFFFF" title="Blanc — paix, pureté"></span>
                </div>
            </div>

        </aside>

        {{-- Colonne droite : formulaire --}}
        <section class="login-card">

            {{-- Header mobile uniquement --}}
            <div class="login-mobile-header">
                <svg class="login-coat-mini"
                     viewBox="0 0 300 200"
                     fill="none"
                     xmlns="http://www.w3.org/2000/svg"
                     aria-hidden="true">
                    <rect x="0" y="0"   width="300" height="40"  fill="#006A4A"/>
                    <rect x="0" y="40"  width="300" height="40"  fill="#FFCE00"/>
                    <rect x="0" y="80"  width="300" height="40"  fill="#006A4A"/>
                    <rect x="0" y="120" width="300" height="40"  fill="#FFCE00"/>
                    <rect x="0" y="160" width="300" height="40"  fill="#006A4A"/>
                    <rect x="0" y="0" width="120" height="120" fill="#D21034"/>
                    <polygon points="60,18 68,44 95,44 73,60 81,86 60,70 39,86 47,60 25,44 52,44" fill="#FFFFFF"/>
                </svg>
                <div>
                    <strong>RÉPUBLIQUE TOGOLAISE</strong>
                    <small>Patrimoine de l'État</small>
                </div>
            </div>

            <div class="login-card-inner">
                <p class="login-eyebrow">ESPACE DE GESTION SÉCURISÉ</p>
                <h1 class="login-title">Connexion</h1>
                <p class="login-subtitle">
                    Accédez au registre national du patrimoine public.
                </p>

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

                    <label class="login-remember">
                        <input type="checkbox" name="remember" value="1">
                        <span>Rester connecté sur cet appareil</span>
                    </label>

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
