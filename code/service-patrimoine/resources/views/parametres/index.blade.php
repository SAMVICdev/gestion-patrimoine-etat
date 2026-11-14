@extends('layouts.app')

@section('title', 'Paramètres de la plateforme')

@section('content')
    <section class="page-intro">
        <div>
            <span class="eyebrow">ADMINISTRATION NATIONALE · CONFIGURATION</span>
            <h1>Paramètres de la plateforme</h1>
            <p>Configurez les informations institutionnelles affichées dans les rapports et les exports.</p>
        </div>
    </section>

    <section class="reference-layout">
        <form class="panel reference-form" method="POST" action="{{ route('parametres.update') }}">
            @csrf
            @method('PUT')

            <span class="eyebrow">INFORMATIONS INSTITUTIONNELLES</span>
            <h2>Identité de l'organisme</h2>

            <label class="field">
                <span>Nom de l'organisme <b>*</b></span>
                <input name="nom_organisme"
                       value="{{ old('nom_organisme', $parametres->firstWhere('cle', 'nom_organisme')?->valeur) }}"
                       required
                       placeholder="Ex. Direction Générale du Patrimoine de l'État">
                @error('nom_organisme')<small class="field-error">{{ $message }}</small>@enderror
            </label>

            <label class="field">
                <span>Pays <b>*</b></span>
                <input name="pays"
                       value="{{ old('pays', $parametres->firstWhere('cle', 'pays')?->valeur) }}"
                       required
                       placeholder="Ex. Togo">
                @error('pays')<small class="field-error">{{ $message }}</small>@enderror
            </label>

            <label class="field">
                <span>Devise <b>*</b></span>
                <input name="devise"
                       value="{{ old('devise', $parametres->firstWhere('cle', 'devise')?->valeur) }}"
                       required
                       placeholder="Ex. FCFA">
                @error('devise')<small class="field-error">{{ $message }}</small>@enderror
            </label>

            <label class="field">
                <span>Préfixe des numéros d'inventaire <b>*</b></span>
                <input name="prefixe_inventaire"
                       value="{{ old('prefixe_inventaire', $parametres->firstWhere('cle', 'prefixe_inventaire')?->valeur) }}"
                       required
                       maxlength="10"
                       placeholder="Ex. PAT">
                @error('prefixe_inventaire')<small class="field-error">{{ $message }}</small>@enderror
            </label>

            <label class="field">
                <span>Année d'exercice <b>*</b></span>
                <input name="annee_exercice"
                       type="number"
                       min="2000" max="2100"
                       value="{{ old('annee_exercice', $parametres->firstWhere('cle', 'annee_exercice')?->valeur) }}"
                       required>
                @error('annee_exercice')<small class="field-error">{{ $message }}</small>@enderror
            </label>

            <span class="eyebrow" style="margin-top:1.5rem;display:block">COORDONNÉES DE CONTACT</span>

            <label class="field">
                <span>Adresse e-mail de contact</span>
                <input type="email"
                       name="email_contact"
                       value="{{ old('email_contact', $parametres->firstWhere('cle', 'email_contact')?->valeur) }}"
                       placeholder="patrimoine@gouvernement.tg">
                @error('email_contact')<small class="field-error">{{ $message }}</small>@enderror
            </label>

            <label class="field">
                <span>Téléphone</span>
                <input name="telephone_contact"
                       value="{{ old('telephone_contact', $parametres->firstWhere('cle', 'telephone_contact')?->valeur) }}"
                       placeholder="+228 00 00 00 00">
                @error('telephone_contact')<small class="field-error">{{ $message }}</small>@enderror
            </label>

            <label class="field">
                <span>Adresse physique du siège</span>
                <textarea name="adresse" rows="3" placeholder="Avenue de la République, Lomé">{{ old('adresse', $parametres->firstWhere('cle', 'adresse')?->valeur) }}</textarea>
                @error('adresse')<small class="field-error">{{ $message }}</small>@enderror
            </label>

            <button class="button button-primary button-wide" type="submit">
                Enregistrer les paramètres <span>→</span>
            </button>
        </form>

        <section class="panel reference-list">
            <div class="panel-heading">
                <div>
                    <span class="eyebrow">RÉCAPITULATIF</span>
                    <h2>Paramètres actifs</h2>
                </div>
            </div>
            @foreach($parametres as $param)
                <div class="reference-row" style="cursor:default">
                    <div class="reference-icon" style="font-size:.7rem;width:3rem">{{ strtoupper(substr($param->cle, 0, 3)) }}</div>
                    <div class="reference-fields">
                        <strong>{{ $param->valeur ?: '—' }}</strong>
                        <small>{{ $param->description }}</small>
                    </div>
                </div>
            @endforeach

            <div style="padding:1.25rem 1.5rem;border-top:1px solid var(--border)">
                <a class="text-link" href="{{ route('parametres.profil') }}">
                    ♙ Modifier mon profil et mon mot de passe →
                </a>
            </div>
        </section>
    </section>
@endsection
