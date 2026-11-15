<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuration initiale — Gestion du Patrimoine de l'État</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, #006a4a 0%, #00382a 60%, #001f18 100%);
            padding: 1.5rem;
        }
        .card {
            background: #fff; border-radius: 18px; padding: 2.2rem 2rem;
            max-width: 460px; width: 100%; box-shadow: 0 25px 60px rgba(0,0,0,.35);
        }
        .top { text-align: center; margin-bottom: 1.4rem; }
        .blason { width: 80px; height: 80px; border-radius: 50%; border: 3px solid #ffce00; padding: 5px; background: #fff; }
        .rep { margin-top: .6rem; font-size: .7rem; letter-spacing: .2em; text-transform: uppercase; color: #006a4a; font-weight: 700; }
        .motto { font-size: .66rem; color: #6b7280; letter-spacing: .1em; }
        h1 { font-size: 1.2rem; color: #111827; text-align: center; margin: 1rem 0 .3rem; }
        .sub { font-size: .85rem; color: #6b7280; text-align: center; margin-bottom: 1.4rem; }
        label { display: block; font-size: .8rem; font-weight: 600; color: #374151; margin: .9rem 0 .3rem; }
        input {
            width: 100%; padding: .7rem .8rem; border: 1px solid #d1d5db; border-radius: 8px;
            font-size: .9rem; outline: none;
        }
        input:focus { border-color: #006a4a; box-shadow: 0 0 0 3px rgba(0,106,74,.12); }
        .hint { font-size: .72rem; color: #9ca3af; margin-top: .25rem; }
        .err { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 8px; padding: .6rem .8rem; font-size: .8rem; margin-top: 1rem; }
        button {
            width: 100%; margin-top: 1.4rem; padding: .8rem; border: 0; border-radius: 10px;
            background: #006a4a; color: #fff; font-weight: 700; font-size: .95rem; cursor: pointer;
        }
        button:hover { background: #00573d; }
        .tricolor { display: flex; height: 5px; border-radius: 4px; overflow: hidden; margin-bottom: 1.2rem; }
        .tricolor span { flex: 1; } .v { background: #006a4a; } .j { background: #ffce00; } .r { background: #d21034; }
    </style>
</head>
<body>
    <div class="card">
        <div class="top">
            <img class="blason" src="{{ asset('images/blason_rt.png') }}" alt="Blason">
            <div class="rep">République Togolaise</div>
            <div class="motto">Travail · Liberté · Patrie</div>
        </div>
        <div class="tricolor"><span class="v"></span><span class="j"></span><span class="r"></span></div>
        <h1>Configuration initiale</h1>
        <p class="sub">Création du premier compte administrateur national.</p>

        @if ($errors->any())
            <div class="err">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('setup.store') }}">
            @csrf
            <label for="token">Jeton de configuration</label>
            <input id="token" name="token" type="password" required autocomplete="off" value="{{ old('token') }}">

            <label for="name">Nom complet</label>
            <input id="name" name="name" type="text" required value="{{ old('name') }}">

            <label for="email">Adresse e-mail</label>
            <input id="email" name="email" type="email" required value="{{ old('email') }}">

            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required>
            <div class="hint">12 caractères minimum, majuscules, minuscules et chiffres.</div>

            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required>

            <button type="submit">Créer le compte administrateur</button>
        </form>
    </div>
</body>
</html>
