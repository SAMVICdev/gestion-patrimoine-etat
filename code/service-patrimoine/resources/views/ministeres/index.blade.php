@extends('layouts.app')

@section('title', 'Ministères')

@section('content')
    <section class="page-intro"><div><span class="eyebrow">ORGANISATION DE L’ÉTAT</span><h1>Ministères</h1><p>Structurez le patrimoine par portefeuille et rattachement institutionnel.</p></div></section>
    <section class="reference-layout">
        <form class="panel reference-form" method="POST" action="{{ route('ministeres.store') }}">@csrf<span class="eyebrow">NOUVEAU PORTEFEUILLE</span><h2>Ajouter un ministère</h2><label class="field"><span>Nom <b>*</b></span><input name="nom" value="{{ old('nom') }}" required placeholder="Ex. Ministère de la Santé">@error('nom')<small class="field-error">{{ $message }}</small>@enderror</label><label class="field"><span>Code administratif</span><input name="code" value="{{ old('code') }}" placeholder="MIN-SANTE">@error('code')<small class="field-error">{{ $message }}</small>@enderror</label><label class="field"><span>Description</span><textarea name="description" rows="3">{{ old('description') }}</textarea></label><button class="button button-primary button-wide" type="submit">Créer le ministère <span>→</span></button></form>
        <section class="panel reference-list"><div class="panel-heading"><div><span class="eyebrow">PORTEFEUILLES ENREGISTRÉS</span><h2>{{ $ministeres->count() }} ministère(s)</h2></div></div>@forelse($ministeres as $ministere)<form class="reference-row" method="POST" action="{{ route('ministeres.update', $ministere) }}">@csrf @method('PUT')<div class="reference-icon">⌂</div><div class="reference-fields"><input aria-label="Nom du ministère" name="nom" value="{{ $ministere->nom }}" required><small>{{ $ministere->services_count }} service(s) · {{ $ministere->code ?: 'Sans code' }}</small></div><input type="hidden" name="code" value="{{ $ministere->code }}"><input type="hidden" name="description" value="{{ $ministere->description }}"><button class="icon-button" type="submit" aria-label="Enregistrer le ministère">✓</button></form>@empty<p class="empty-note">Aucun ministère. Ajoutez-en un pour structurer les services.</p>@endforelse</section>
    </section>
@endsection
