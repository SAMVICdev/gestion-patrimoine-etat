@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
    <section class="page-intro">
        <div>
            <span class="eyebrow">COMPTE · PERSONNEL</span>
            <h1>Mon profil</h1>
            <p>Modifiez votre nom d'affichage ou changez votre mot de passe.</p>
        </div>
    </section>

    <section class="reference-layout">
        <form class="panel reference-form" method="POST" action="{{ route('parametres.profil.update') }}">
            @csrf
            @method('PUT')

            <span class="eyebrow">IDENTITÉ</span>
            <h2>Informations personnelles</h2>

            <div class="reference-row" style="cursor:default;margin-bottom:1.5rem">
                <div class="reference-icon" style="font-size:1.4rem">
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                </div>
                <div class="reference-fields">
                    <strong>{{ $user->email }}</strong>
                    <small>{{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        @if($user->service)· {{ $user->service->nom }}@endif
                    </small>
                </div>
            </div>

            <label class="field">
                <span>Nom d'affichage <b>*</b></span>
                <input name="name"
                       value="{{ old('name', $user->name) }}"
                       required
                       placeholder="Votre nom complet">
                @error('name')<small class="field-error">{{ $message }}</small>@enderror
            </label>

            <span class="eyebrow" style="margin-top:1.5rem;display:block">SÉCURITÉ</span>
            <h2>Changer le mot de passe</h2>
            <p style="color:var(--text-muted);font-size:.875rem;margin-bottom:1rem">
                Laissez vide pour conserver le mot de passe actuel. Un nouveau mot de passe doit comporter
                au moins 12 caractères, une majuscule, une minuscule et un chiffre.
            </p>

            <label class="field">
                <span>Nouveau mot de passe</span>
                <input type="password" name="password" autocomplete="new-password" placeholder="••••••••••••">
                @error('password')<small class="field-error">{{ $message }}</small>@enderror
            </label>

            <label class="field">
                <span>Confirmer le nouveau mot de passe</span>
                <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="••••••••••••">
            </label>

            <button class="button button-primary button-wide" type="submit">
                Enregistrer les modifications <span>→</span>
            </button>
        </form>

        <section class="panel reference-list">
            <div class="panel-heading">
                <div>
                    <span class="eyebrow">RÔLES ET ACCÈS</span>
                    <h2>Vos habilitations</h2>
                </div>
            </div>

            <div class="reference-row" style="cursor:default">
                <div class="reference-icon">♙</div>
                <div class="reference-fields">
                    <strong>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</strong>
                    <small>Rôle assigné par l'administrateur national</small>
                </div>
            </div>

            @if($user->service)
                <div class="reference-row" style="cursor:default">
                    <div class="reference-icon">⌂</div>
                    <div class="reference-fields">
                        <strong>{{ $user->service->nom }}</strong>
                        <small>Service de rattachement · {{ $user->service->ministere?->nom ?? 'Sans ministère' }}</small>
                    </div>
                </div>
            @else
                <div class="reference-row" style="cursor:default">
                    <div class="reference-icon">▧</div>
                    <div class="reference-fields">
                        <strong>Périmètre national</strong>
                        <small>Accès à l'ensemble des services et ministères</small>
                    </div>
                </div>
            @endif

            <div class="role-note" style="margin:0;border-top:1px solid var(--border)">
                <p>Le rôle et le service de rattachement ne peuvent être modifiés que par un administrateur national depuis l'écran <a class="text-link" href="{{ route('utilisateurs.index') }}">Utilisateurs & rôles</a>.</p>
            </div>
        </section>
    </section>
@endsection
