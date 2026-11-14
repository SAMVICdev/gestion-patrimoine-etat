<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion · Patrimoine de l'État · Togo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">

    {{-- Arrière-plan décoratif --}}
    <div class="login-bg" aria-hidden="true">
        <div class="login-bg-stripe login-bg-stripe-1"></div>
        <div class="login-bg-stripe login-bg-stripe-2"></div>
        <div class="login-bg-stripe login-bg-stripe-3"></div>
        <div class="login-bg-orb"></div>
    </div>

    <main class="login-wrap">

        {{-- Panneau gauche — identité nationale --}}
        <aside class="login-identity" aria-hidden="true">
            {{-- Armoiries de la République Togolaise --}}
            <div class="login-coat">
                <svg class="coat-svg" viewBox="0 0 200 240" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Armoiries de la République Togolaise">
                    <!-- Écu principal -->
                    <path d="M100 15 L185 55 L185 150 Q185 210 100 230 Q15 210 15 150 L15 55 Z" fill="#006B3F"/>
                    <!-- Bande rouge centrale -->
                    <path d="M100 15 L185 55 L185 150 Q185 210 100 230 Q15 210 15 150 L15 55 Z" fill="none" stroke="#CE1126" stroke-width="22" stroke-dasharray="1 1000" pathLength="10"/>
                    <!-- Canton supérieur gauche rouge -->
                    <path d="M15 55 L15 115 L80 115 L80 55 Z" fill="#CE1126"/>
                    <!-- Étoile blanche dans le canton -->
                    <polygon points="47,65 51,78 65,78 54,86 58,99 47,91 36,99 40,86 29,78 43,78" fill="#FFFFFF"/>
                    <!-- Bandes horizontales vertes et jaunes -->
                    <rect x="80" y="55" width="105" height="20" fill="#006B3F"/>
                    <rect x="80" y="75" width="105" height="20" fill="#FFCE00"/>
                    <rect x="80" y="95" width="105" height="20" fill="#006B3F"/>
                    <rect x="15" y="115" width="170" height="20" fill="#FFCE00"/>
                    <rect x="15" y="135" width="170" height="20" fill="#CE1126"/>
                    <!-- Zone inférieure verte -->
                    <path d="M15 155 L15 150 Q15 210 100 230 Q185 210 185 150 L185 155 Z" fill="#006B3F"/>
                    <!-- Bande jaune décorative bas -->
                    <path d="M20 185 Q100 220 180 185 L180 195 Q100 232 20 195 Z" fill="#FFCE00" opacity="0.6"/>
                    <!-- Bordure de l'écu -->
                    <path d="M100 15 L185 55 L185 150 Q185 210 100 230 Q15 210 15 150 L15 55 Z" fill="none" stroke="#FFCE00" stroke-width="3"/>
                    <!-- Symboles au centre bas -->
                    <text x="100" y="175" text-anchor="middle" fill="#FFCE00" font-size="11" font-weight="bold" font-family="serif">TOGO</text>
                    <!-- Palmes décoratives gauche -->
                    <path d="M30 170 Q20 155 25 140 Q35 155 30 170Z" fill="#FFCE00" opacity="0.8"/>
                    <path d="M25 165 Q12 155 15 140 Q28 152 25 165Z" fill="#FFCE00" opacity="0.6"/>
                    <!-- Palmes décoratives droite -->
                    <path d="M170 170 Q180 155 175 140 Q165 155 170 170Z" fill="#FFCE00" opacity="0.8"/>
                    <path d="M175 165 Q188 155 185 140 Q172 152 175 165Z" fill="#FFCE00" opacity="0.6"/>
                </svg>
            </div>
            <div class="login-identity-text">
                <div class="login-identity-flag" aria-hidden="true">
                    <span style="background:#006B3F"></span>
                    <span style="background:#FFCE00"></span>
                    <span style="background:#CE1126"></span>
                </div>
                <strong class="login-identity-country">RÉPUBLIQUE TOGOLAISE</strong>
                <span class="login-identity-motto">Travail · Liberté · Patrie</span>
                <div class="login-identity-divider"></div>
                <span class="login-identity-ministry">Direction Générale du Patrimoine de l'État</span>
            </div>
        </aside>

        {{-- Panneau droit — formulaire --}}
        <section class="login-card">

            {{-- En-tête mobile --}}
            <div class="login-mobile-header">
                <div class="login-coat-mini">
                    <svg viewBox="0 0 200 240" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M100 15 L185 55 L185 150 Q185 210 100 230 Q15 210 15 150 L15 55 Z" fill="#006B3F"/>
                        <path d="M15 55 L15 115 L80 115 L80 55 Z" fill="#CE1126"/>
                        <polygon points="47,65 51,78 65,78 54,86 58,99 47,91 36,99 40,86 29,78 43,78" fill="#FFFFFF"/>
                        <rect x="80" y="55" width="105" height="20" fill="#006B3F"/>
                        <rect x="80" y="75" width="105" height="20" fill="#FFCE00"/>
                        <rect x="80" y="95" width="105" height="20" fill="#006B3F"/>
                        <rect x="15" y="115" width="170" height="20" fill="#FFCE00"/>
                        <rect x="15" y="135" width="170" height="20" fill="#CE1126"/>
                        <path d="M100 15 L185 55 L185 150 Q185 210 100 230 Q15 210 15 150 L15 55 Z" fill="none" stroke="#FFCE00" stroke-width="3"/>
                    </svg>
                </div>
                <div>
                    <strong>RÉPUBLIQUE TOGOLAISE</strong>
                    <small>Patrimoine de l'État</small>
                </div>
            </div>

            <div class="login-card-inner">
                <p class="login-eyebrow">ESPACE DE GESTION SÉCURISÉ</p>
                <h1 class="login-title">Connexion</h1>
                <p class="login-subtitle">Accédez au registre national du patrimoine public.</p>

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
                    <span>Accès réservé aux agents habilités · Connexion chiffrée TLS</span>
                </div>
            </div>
        </section>
    </main>

</body>
</html>
