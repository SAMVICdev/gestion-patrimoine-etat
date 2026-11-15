<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configuration terminée — Gestion du Patrimoine de l'État</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(160deg, #006a4a 0%, #00382a 60%, #001f18 100%);
            padding: 1.5rem;
        }
        .card { background: #fff; border-radius: 18px; padding: 2.4rem 2rem; max-width: 460px; width: 100%; text-align: center; box-shadow: 0 25px 60px rgba(0,0,0,.35); }
        .blason { width: 80px; height: 80px; border-radius: 50%; border: 3px solid #ffce00; padding: 5px; background: #fff; }
        .rep { margin-top: .6rem; font-size: .7rem; letter-spacing: .2em; text-transform: uppercase; color: #006a4a; font-weight: 700; }
        h1 { font-size: 1.2rem; color: #111827; margin: 1rem 0 .5rem; }
        p { font-size: .88rem; color: #4b5563; line-height: 1.6; }
        .ok { color: #006a4a; font-weight: 700; }
        .warn { margin-top: 1rem; font-size: .78rem; color: #92400e; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: .6rem .8rem; }
    </style>
</head>
<body>
    <div class="card">
        <img class="blason" src="{{ asset('images/blason_rt.png') }}" alt="Blason">
        <div class="rep">République Togolaise</div>
        <h1><span class="ok">✓</span> Compte administrateur créé</h1>
        <p>Le premier administrateur national a été enregistré avec succès.<br>
        Vous pouvez maintenant vous connecter à l'application.</p>
        <div class="warn">Pour des raisons de sécurité, supprimez la variable <code>SETUP_TOKEN</code> des paramètres du service après cette étape.</div>
    </div>
</body>
</html>
