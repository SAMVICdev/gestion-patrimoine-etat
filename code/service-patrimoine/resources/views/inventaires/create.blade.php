@extends('layouts.app')

@section('title', 'Démarrer un inventaire')

@section('content')
    <div class="breadcrumbs"><a href="{{ route('inventaires.index') }}">Inventaires</a><span>/</span><strong>Nouvelle campagne</strong></div>
    <section class="page-intro"><div><span class="eyebrow">CAMPAGNE DE CONTRÔLE</span><h1>Démarrer un inventaire</h1><p>Choisissez le périmètre, puis enregistrez les constats au fur et à mesure.</p></div></section>
    <form class="panel form-panel" method="POST" action="{{ route('inventaires.store') }}">@csrf<div class="form-grid"><label class="field field-full"><span>Nom de la campagne <b>*</b></span><input name="titre" value="{{ old('titre') }}" required placeholder="Ex. Inventaire annuel 2026"></label><label class="field"><span>Service concerné</span><select name="service_id"><option value="">Tous les services autorisés</option>@foreach($services as $service)<option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>{{ $service->nom }}</option>@endforeach</select></label><label class="field"><span>Description</span><textarea name="description" rows="3">{{ old('description') }}</textarea></label></div><div class="form-actions"><a class="button button-secondary" href="{{ route('inventaires.index') }}">Annuler</a><button class="button button-primary" type="submit">Démarrer la campagne <span>→</span></button></div></form>
@endsection
