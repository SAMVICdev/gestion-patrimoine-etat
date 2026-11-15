<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 14mm 12mm 20mm 12mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.5px;
            color: #1c2b24;
            margin: 0;
        }
        .header { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .blason { width: 56px; }
        .blason img { width: 50px; height: auto; }
        .institution { text-align: center; }
        .institution .country {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #004d35;
        }
        .institution .service { font-size: 9px; color: #5e6d64; margin-top: 2px; }
        .tricolor { height: 4px; }
        .tricolor td { height: 4px; padding: 0; }
        .tricolor .v { background: #006A4A; width: 33.33%; }
        .tricolor .j { background: #FFCE00; width: 33.34%; }
        .tricolor .r { background: #D21034; width: 33.33%; }

        .doc-title {
            text-align: center;
            margin: 12px 0 2px;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #004d35;
            text-transform: uppercase;
        }
        .doc-subtitle {
            text-align: center;
            font-size: 8.5px;
            color: #6b7a72;
            margin-bottom: 10px;
        }

        table.registre { width: 100%; border-collapse: collapse; }
        table.registre th {
            background: #004d35;
            color: #fff;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: .3px;
            padding: 5px 4px;
            text-align: left;
        }
        table.registre td {
            border: 1px solid #d9ddd6;
            padding: 4px;
            font-size: 8px;
            vertical-align: top;
        }
        table.registre tr:nth-child(even) td { background: #f7f8f5; }
        .num { text-align: center; }
        .valeur { text-align: right; white-space: nowrap; }
        tfoot td {
            background: #e8f2ec;
            font-weight: bold;
            color: #004d35;
            border: 1px solid #d9ddd6;
            padding: 5px 4px;
        }

        .footer {
            position: fixed;
            bottom: -6mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5px;
            color: #9aa79e;
            border-top: 1px solid #e3e6df;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td class="blason" rowspan="2"><img src="{{ $blason }}" alt=""></td>
            <td class="institution">
                <div class="country">RÉPUBLIQUE TOGOLAISE</div>
                <div class="service">Direction Générale du Patrimoine de l'État</div>
                <div class="service">Système d'Information Domaniale · Travail · Liberté · Patrie</div>
            </td>
            <td class="blason" rowspan="2"><img src="{{ $blason }}" alt=""></td>
        </tr>
    </table>
    <table class="tricolor"><tr><td class="v"></td><td class="j"></td><td class="r"></td></tr></table>

    <div class="doc-title">Registre patrimonial de l'État</div>
    <div class="doc-subtitle">
        Édité le {{ now()->format('d/m/Y à H:i') }} · {{ count($biens) }} bien(s) ·
        Valeur totale déclarée : {{ number_format($totalValeur, 0, ',', ' ') }} FCFA
    </div>

    <table class="registre">
        <thead>
            <tr>
                <th style="width:3%">N°</th>
                <th style="width:9%">Inventaire</th>
                <th style="width:17%">Désignation</th>
                <th style="width:9%">Catégorie</th>
                <th style="width:11%">Ministère</th>
                <th style="width:11%">Service</th>
                <th style="width:10%">Localisation</th>
                <th style="width:8%">État</th>
                <th style="width:8%">Acquisition</th>
                <th style="width:9%">Valeur (FCFA)</th>
                <th style="width:9%">Responsable</th>
            </tr>
        </thead>
        <tbody>
            @forelse($biens as $i => $bien)
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ $bien['numero_inventaire'] }}</td>
                <td>{{ $bien['designation'] }}</td>
                <td>{{ $bien['categorie'] }}</td>
                <td>{{ $bien['ministere'] }}</td>
                <td>{{ $bien['service'] }}</td>
                <td>{{ $bien['localisation'] }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $bien['etat'])) }}</td>
                <td>{{ $bien['date_acquisition'] ? \Carbon\Carbon::parse($bien['date_acquisition'])->format('d/m/Y') : '—' }}</td>
                <td class="valeur">{{ $bien['valeur_acquisition'] !== null && $bien['valeur_acquisition'] !== '' ? number_format((float) $bien['valeur_acquisition'], 0, ',', ' ') : '—' }}</td>
                <td>{{ $bien['responsable'] }}</td>
            </tr>
            @empty
            <tr><td colspan="11" style="text-align:center; padding:14px;">Aucun bien enregistré.</td></tr>
            @endforelse
        </tbody>
        @if(count($biens))
        <tfoot>
            <tr>
                <td colspan="9" style="text-align:right;">Total — {{ count($biens) }} bien(s)</td>
                <td class="valeur">{{ number_format($totalValeur, 0, ',', ' ') }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        République Togolaise — Direction Générale du Patrimoine de l'État ·
        Système d'Information Domaniale · Document généré automatiquement
    </div>

</body>
</html>
