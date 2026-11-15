<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Erreur') — Gestion du Patrimoine de l'État</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background:
                linear-gradient(160deg, rgba(0,77,53,.92) 0%, rgba(0,45,32,.96) 55%, rgba(0,30,22,.97) 100%),
                url('{{ asset('images/place_independance.jpg') }}') center/cover fixed;
            color: #1f2937;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: url('{{ asset('images/blason_rt.png') }}') center/420px no-repeat;
            opacity: .07;
            pointer-events: none;
        }
        .tricolor {
            height: 6px;
            display: flex;
        }
        .tricolor span { flex: 1; }
        .tricolor .v { background: #006a4a; }
        .tricolor .j { background: #ffce00; }
        .tricolor .r { background: #d21034; }

        .wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            z-index: 1;
        }
        .card {
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,.35);
            max-width: 560px;
            width: 100%;
            overflow: hidden;
            text-align: center;
        }
        .card-top { padding: 2.2rem 2rem 0; }
        .blason {
            width: 96px; height: 96px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #ffce00;
            padding: 6px;
            object-fit: contain;
            box-shadow: 0 6px 18px rgba(0,77,53,.25);
        }
        .rep {
            margin-top: .9rem;
            font-size: .72rem;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: #006a4a;
            font-weight: 700;
        }
        .motto {
            font-size: .68rem;
            color: #6b7280;
            letter-spacing: .12em;
            margin-top: .15rem;
        }
        .code {
            font-size: 4.2rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(90deg, #006a4a, #d21034);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin: 1.2rem 0 .3rem;
        }
        h1 {
            font-size: 1.35rem;
            color: #111827;
            margin-bottom: .6rem;
        }
        .msg { color: #4b5563; font-size: .95rem; line-height: 1.55; margin-bottom: 1.6rem; }
        .actions { display: flex; gap: .7rem; justify-content: center; flex-wrap: wrap; padding: 0 2rem 2rem; }
        .btn {
            display: inline-block;
            padding: .7rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: .9rem;
            text-decoration: none;
            transition: transform .15s, box-shadow .15s;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-primary { background: #006a4a; color: #fff; box-shadow: 0 6px 16px rgba(0,106,74,.3); }
        .btn-ghost { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
        .footer {
            text-align: center;
            padding: 1rem;
            font-size: .72rem;
            color: rgba(255,255,255,.75);
            position: relative;
            z-index: 1;
        }
        .footer .inner { border-top: 1px solid rgba(255,255,255,.15); padding-top: .8rem; }
        @media (max-width: 480px) {
            .code { font-size: 3.2rem; }
            h1 { font-size: 1.15rem; }
        }
    </style>
</head>
<body>
    <div class="tricolor"><span class="v"></span><span class="j"></span><span class="r"></span></div>
    <div class="wrap">
        <div class="card">
            <div class="card-top">
                <img class="blason" src="{{ asset('images/blason_rt.png') }}" alt="Blason de la République Togolaise">
                <div class="rep">République Togolaise</div>
                <div class="motto">Travail · Liberté · Patrie</div>
                <div class="code">@yield('code')</div>
                <h1>@yield('title')</h1>
                <p class="msg">@yield('message')</p>
            </div>
            <div class="actions">
                <a class="btn btn-primary" href="{{ url('/') }}">Retour à l'accueil</a>
                <a class="btn btn-ghost" href="{{ url('/connexion') }}">Connexion</a>
            </div>
        </div>
    </div>
    <div class="footer"><div class="inner">
        Gestion du Patrimoine de l'État — Direction Générale du Patrimoine<br>
        Système d'Information Domaniale · Charte graphique de la République Togolaise
    </div></div>
</body>
</html>
