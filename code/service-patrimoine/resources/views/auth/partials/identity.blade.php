{{-- Colonne identité institutionnelle commune aux écrans d'authentification --}}
<aside class="login-identity">

    <div class="login-identity-body">

        <div class="login-identity-head">
            <div class="login-coat-wrap">
                <img src="{{ asset('images/blason_rt.png') }}"
                     alt="Blason de la République Togolaise"
                     class="login-coat-img"/>
            </div>
            <div class="login-identity-text">
                <strong class="login-identity-country">RÉPUBLIQUE TOGOLAISE</strong>
                <div class="login-identity-rule"></div>
                <span class="login-identity-motto">Travail &middot; Liberté &middot; Patrie</span>
            </div>
        </div>

        <div class="login-identity-ministry-block">
            <span class="login-identity-ministry-label">SERVICE GESTIONNAIRE</span>
            <span class="login-identity-ministry">
                Direction Générale<br>du Patrimoine de l'État
            </span>
        </div>

        <figure class="login-monument">
            <div class="login-monument-frame">
                <img src="{{ asset('images/monument_independance.jpg') }}"
                     alt="Monument de l'Indépendance, Place de l'Indépendance, Lomé"
                     class="login-monument-img"/>
                <span class="login-monument-badge">◆</span>
            </div>
            <figcaption class="login-monument-caption">
                Monument de l'Indépendance<br>
                <small>Place de l'Indépendance · Lomé</small>
            </figcaption>
        </figure>

        <div class="login-mini-flag" aria-label="Drapeau togolais">
            <svg viewBox="0 0 300 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="0" y="0"   width="300" height="40"  fill="#006A4A"/>
                <rect x="0" y="40"  width="300" height="40"  fill="#FFCE00"/>
                <rect x="0" y="80"  width="300" height="40"  fill="#006A4A"/>
                <rect x="0" y="120" width="300" height="40"  fill="#FFCE00"/>
                <rect x="0" y="160" width="300" height="40"  fill="#006A4A"/>
                <rect x="0" y="0"   width="120" height="120" fill="#D21034"/>
                <polygon points="60,18 68,44 95,44 73,60 81,86 60,70 39,86 47,60 25,44 52,44" fill="#FFFFFF"/>
            </svg>
        </div>

    </div>

    <div class="login-identity-footer">
        <span>Système d'Information Domaniale &middot; Charte graphique de la République Togolaise</span>
        <span class="login-identity-secure">◆ TLS</span>
    </div>

</aside>
