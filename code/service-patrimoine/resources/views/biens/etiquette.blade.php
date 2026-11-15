<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Étiquette · {{ $bien->numero_inventaire }} · Patrimoine de l'État</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #eceae4;
            color: #1c2b24;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 32px 16px;
        }
        .toolbar {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .toolbar a, .toolbar button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-print { background: #006A4A; color: #fff; }
        .btn-back { background: #fff; color: #006A4A; border: 1.5px solid #006A4A; }
        .etiquette {
            width: 100%;
            max-width: 720px;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 14px 44px rgba(0,0,0,.16);
        }
        .etiquette-bar {
            height: 10px;
            display: flex;
        }
        .etiquette-bar span { flex: 1; }
        .etiquette-bar span:nth-child(1) { background: #006A4A; }
        .etiquette-bar span:nth-child(2) { background: #FFCE00; }
        .etiquette-bar span:nth-child(3) { background: #D21034; }
        .etiquette-body {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 28px;
            padding: 30px 34px 26px;
            align-items: center;
        }
        .etiquette-qr {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .etiquette-qr svg { width: 170px; height: 170px; }
        .etiquette-qr small {
            font-size: 8px;
            letter-spacing: .14em;
            color: #7a8a82;
            text-transform: uppercase;
        }
        .etiquette-info { display: grid; gap: 10px; }
        .etiquette-country {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .22em;
            color: #006A4A;
        }
        .etiquette-rule { width: 44px; height: 3px; background: #FFCE00; border-radius: 2px; }
        .etiquette-code {
            font-family: ui-monospace, 'Courier New', monospace;
            font-size: 26px;
            font-weight: 800;
            color: #0d2e1f;
            letter-spacing: .04em;
        }
        .etiquette-designation {
            font-size: 19px;
            font-weight: 700;
            color: #1c2b24;
            line-height: 1.3;
        }
        .etiquette-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 24px;
            margin-top: 6px;
        }
        .etiquette-meta div { font-size: 11px; color: #5e6d64; }
        .etiquette-meta strong { display: block; color: #233b33; font-size: 12px; }
        .etiquette-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 34px;
            border-top: 1px solid #eee;
            font-size: 9px;
            letter-spacing: .1em;
            color: #8a968e;
            text-transform: uppercase;
        }
        .etiquette-footer img { height: 34px; width: auto; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .etiquette { box-shadow: none; border-radius: 0; max-width: none; }
            @page { size: A4; margin: 12mm; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <button class="btn-print" type="button" onclick="window.print()">🖨 Imprimer l'étiquette</button>
        <a class="btn-back" href="{{ route('biens.show', $bien) }}">← Retour à la fiche</a>
    </div>

    <div class="etiquette">
        <div class="etiquette-bar"><span></span><span></span><span></span></div>
        <div class="etiquette-body">
            <div class="etiquette-qr">
                {!! $qrSvg !!}
                <small>Scannez pour ouvrir la fiche</small>
            </div>
            <div class="etiquette-info">
                <span class="etiquette-country">RÉPUBLIQUE TOGOLAISE</span>
                <div class="etiquette-rule"></div>
                <span class="etiquette-code">{{ $bien->numero_inventaire }}</span>
                <span class="etiquette-designation">{{ $bien->designation }}</span>
                <div class="etiquette-meta">
                    <div><strong>Catégorie</strong>{{ $bien->categorie->nom }}</div>
                    <div><strong>Service</strong>{{ $bien->service->nom }}</div>
                    <div><strong>Localisation</strong>{{ $bien->localisation }}</div>
                    <div><strong>État</strong>{{ ucfirst(str_replace('_', ' ', $bien->etat)) }}</div>
                </div>
            </div>
        </div>
        <div class="etiquette-footer">
            <span>Patrimoine de l'État · Domaine public</span>
            <img src="{{ asset('images/blason_rt.png') }}" alt="Blason de la République Togolaise">
        </div>
    </div>

</body>
</html>
