@extends('layouts.app')

@section('title', 'Services publics')

@section('content')
    <section class="page-intro"><div><span class="eyebrow">ORGANISATION ADMINISTRATIVE</span><h1>Services responsables</h1><p>Gérez les services auxquels les biens publics sont rattachés.</p></div></section>
    <section class="reference-layout">
        <form class="panel reference-form" method="POST" action="{{ route('services.store') }}">@csrf<span class="eyebrow">NOUVEAU SERVICE</span><h2>Ajouter au référentiel</h2><label class="field"><span>Nom <b>*</b></span><input name="nom" value="{{ old('nom') }}" required placeholder="Ex. Direction du patrimoine">@error('nom')<small class="field-error">{{ $message }}</small>@enderror</label><label class="field"><span>Description</span><textarea name="description" rows="4" placeholder="Description facultative">{{ old('description') }}</textarea></label><button class="button button-primary button-wide" type="submit">Créer le service <span>→</span></button></form>
        <section class="panel reference-list"><div class="panel-heading"><div><span class="eyebrow">SERVICES ACTIFS</span><h2>{{ $services->count() }} service(s)</h2></div></div>@forelse($services as $service)<form class="reference-row" method="POST" action="{{ route('services.update', $service) }}">@csrf @method('PUT')<div class="reference-icon">⌂</div><div class="reference-fields"><input aria-label="Nom du service" name="nom" value="{{ $service->nom }}" required><small>{{ $service->biens_count }} bien(s) · {{ $service->description ?: 'Aucune description' }}</small></div><button class="icon-button" type="submit" aria-label="Enregistrer le service">✓</button></form>@empty<p class="empty-note">Aucun service enregistré. Ajoutez-en un pour commencer.</p>@endforelse</section>
    </section>
@endsection
