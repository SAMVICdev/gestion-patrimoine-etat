<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 18mm 16mm 22mm 16mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            color: #1c2b24;
            margin: 0;
        }
        .header { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .blason { width: 70px; }
        .blason img { width: 62px; height: auto; }
        .institution { text-align: center; }
        .institution .country {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #004d35;
        }
        .institution .service {
            font-size: 10px;
            color: #5e6d64;
            margin-top: 2px;
        }
        .tricolor { height: 5px; }
        .tricolor td { height: 5px; padding: 0; }
        .tricolor .v { background: #006A4A; width: 33.33%; }
        .tricolor .j { background: #FFCE00; width: 33.34%; }
        .tricolor .r { background: #D21034; width: 33.33%; }

        .doc-title {
            text-align: center;
            margin: 16px 0 4px;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #004d35;
            text-transform: uppercase;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 9.5px;
            color: #6b7a72;
            margin-bottom: 14px;
        }
        .doc-subtitle .num {
            font-weight: bold;
            color: #D21034;
            letter-spacing: 1px;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            color: #fff;
            background: #006A4A;
            padding: 5px 9px;
            margin: 14px 0 8px;
            text-transform: uppercase;
        }

        table.fields { width: 100%; border-collapse: collapse; }
        table.fields td {
            border: 1px solid #d9ddd6;
            padding: 6px 9px;
            vertical-align: top;
        }
        table.fields td.label {
            width: 32%;
            background: #f4f6f2;
            font-weight: bold;
            color: #004d35;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }
        table.fields td.value { background: #fff; }
        .etat-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: bold;
            background: #e8f2ec;
            color: #006A4A;
        }

        .qr-block { text-align: center; margin-top: 6px; }
        .qr-block img { width: 110px; height: 110px; }
        .qr-block .qr-num { font-size: 8.5px; color: #6b7a72; margin-top: 4px; }

        .photos { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .photos td { width: 25%; text-align: center; padding: 4px; vertical-align: top; }
        .photos img { max-width: 100%; max-height: 120px; border: 1px solid #d9ddd6; }
        .photos .legende { font-size: 8px; color: #6b7a72; margin-top: 3px; }

        table.history { width: 100%; border-collapse: collapse; }
        table.history th {
            background: #004d35;
            color: #fff;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: .4px;
            padding: 5px 7px;
            text-align: left;
        }
        table.history td {
            border: 1px solid #d9ddd6;
            padding: 5px 7px;
            font-size: 9.5px;
        }
        table.history tr:nth-child(even) td { background: #f7f8f5; }

        .signature {
            margin-top: 34px;
            width: 100%;
            border-collapse: collapse;
        }
        .signature td { width: 50%; text-align: center; font-size: 9px; color: #5e6d64; padding-top: 40px; }
        .signature .line {
            border-top: 1px solid #9aa79e;
            padding-top: 4px;
            margin: 0 30px;
        }

        .footer {
            position: fixed;
            bottom: -8mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #9aa79e;
            border-top: 1px solid #e3e6df;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    {{-- En-tête institutionnel --}}
    <table class="header">
        <tr>
            <td class="blason" rowspan="2"><img src="{{ $blason }}" alt=""></td>
            <td class="institution">
                <div class="country">RÉPUBLIQUE TOGOLAISE</div>
                <div class="service">Direction Générale du Patrimoine de l'État</div>
                <div class="service">Système d'Information Domaniale &middot; Travail · Liberté · Patrie</div>
            </td>
            <td class="blason" rowspan="2"><img src="{{ $blason }}" alt=""></td>
        </tr>
    </table>
    <table class="tricolor"><tr><td class="v"></td><td class="j"></td><td class="r"></td></tr></table>

    <div class="doc-title">Fiche patrimoniale du bien</div>
    <div class="doc-subtitle">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
        &nbsp;·&nbsp; <span class="num">N° {{ $bien->numero_inventaire }}</span>
    </div>

    {{-- Identification --}}
    <div class="section-title">Identification</div>
    <table class="fields">
        <tr>
            <td class="label">Numéro d'inventaire</td>
            <td class="value"><strong>{{ $bien->numero_inventaire }}</strong></td>
            <td class="label">Désignation</td>
            <td class="value">{{ $bien->designation }}</td>
        </tr>
        <tr>
            <td class="label">Catégorie</td>
            <td class="value">{{ $bien->categorie?->nom }}</td>
            <td class="label">État actuel</td>
            <td class="value"><span class="etat-badge">{{ ucfirst(str_replace('_', ' ', $bien->etat)) }}</span></td>
        </tr>
        <tr>
            <td class="label">Ministère</td>
            <td class="value">{{ $bien->service?->ministere?->nom ?? '—' }}</td>
            <td class="label">Service responsable</td>
            <td class="value">{{ $bien->service?->nom }}</td>
        </tr>
        <tr>
            <td class="label">Localisation</td>
            <td class="value">{{ $bien->localisation }}</td>
            <td class="label">Responsable / détenteur</td>
            <td class="value">{{ $bien->responsable ?: 'Non renseigné' }}</td>
        </tr>
        <tr>
            <td class="label">Date d'acquisition</td>
            <td class="value">{{ $bien->date_acquisition?->format('d/m/Y') ?? 'Non renseignée' }}</td>
            <td class="label">Valeur d'acquisition</td>
            <td class="value">{{ $bien->valeur_acquisition ? number_format((float) $bien->valeur_acquisition, 0, ',', ' ').' FCFA' : 'Non renseignée' }}</td>
        </tr>
        <tr>
            <td class="label">Source de financement</td>
            <td class="value" colspan="3">{{ $bien->source_financement ?: 'Non renseignée' }}</td>
        </tr>
        <tr>
            <td class="label">Description</td>
            <td class="value" colspan="3">{{ $bien->description ?: 'Aucune description.' }}</td>
        </tr>
    </table>

    {{-- QR code --}}
    <div class="section-title">Identification automatique</div>
    <table style="width:100%; border-collapse:collapse;"><tr>
        <td style="width:30%; vertical-align:top;">
            <div class="qr-block">
                <img src="{{ $qrPng }}" alt="QR code">
                <div class="qr-num">Scannez pour ouvrir la fiche</div>
            </div>
        </td>
        <td style="vertical-align:top; padding-left:14px;">
            <p style="font-size:9.5px; color:#5e6d64;">
                Ce code QR est unique et renvoie vers la fiche numérique du bien
                dans le Système d'Information Domaniale. Il permet de vérifier
                l'identité du bien lors des inventaires et contrôles terrain.
            </p>
        </td>
    </tr></table>

    {{-- Photos --}}
    @if(count($photos))
    <div class="section-title">Photos du bien</div>
    <table class="photos">
        <tr>
            @foreach($photos as $photo)
            <td>
                <img src="{{ $photo['dataUri'] }}" alt="">
                @if($photo['legende'])<div class="legende">{{ $photo['legende'] }}</div>@endif
            </td>
            @endforeach
        </tr>
    </table>
    @endif

    {{-- Historique des états --}}
    @if(count($histories))
    <div class="section-title">Historique des états</div>
    <table class="history">
        <thead><tr><th>Date</th><th>État</th><th>Agent</th><th>Commentaire</th></tr></thead>
        <tbody>
            @foreach($histories as $history)
            <tr>
                <td>{{ $history->date_modification?->format('d/m/Y H:i') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $history->etat_origine)) }} → {{ ucfirst(str_replace('_', ' ', $history->etat_destination)) }}</td>
                <td>{{ $history->agent?->name ?? 'Système' }}</td>
                <td>{{ $history->commentaire ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Signatures --}}
    <table class="signature">
        <tr>
            <td><div class="line">Le responsable du service</div></td>
            <td><div class="line">La Direction Générale du Patrimoine de l'État</div></td>
        </tr>
    </table>

    <div class="footer">
        République Togolaise — Direction Générale du Patrimoine de l'État ·
        Système d'Information Domaniale · Document généré automatiquement
    </div>

</body>
</html>
