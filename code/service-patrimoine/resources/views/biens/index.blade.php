@extends('layouts.app')

@section('title', 'Biens & inventaire')

@section('content')
    <section class="page-intro inventory-intro">
        <div><span class="eyebrow">REGISTRE DOMANIAL · INVENTAIRE</span><h1>Biens & inventaire</h1><p>Le registre central des biens publics et de leurs affectations.</p></div>
        @if(auth()->user()->canManageAssets())
            <a class="button button-primary" href="{{ route('biens.create') }}"><span>＋</span> Enregistrer un bien</a>
        @endif
    </section>

    <section class="metric-grid compact-metrics">
        <article class="metric-card"><span class="metric-label">RÉSULTATS</span><strong>{{ number_format($biens->total(), 0, ',', ' ') }}</strong><small>Biens correspondant à la recherche</small></article>
        <article class="metric-card"><span class="metric-label">PAGE ACTUELLE</span><strong>{{ $biens->count() }}</strong><small>Biens affichés</small></article>
        <article class="metric-card"><span class="metric-label">RÉFÉRENTIELS</span><strong>{{ $categories->count() }} <small>catégories</small></strong><small>{{ $services->count() }} services enregistrés</small></article>
        @if(auth()->user()->canManageAssets())
            <article class="metric-card metric-cta"><span class="metric-icon">＋</span><span class="metric-label">BESOIN D’AJOUTER ?</span><a href="{{ route('biens.create') }}">Créer une fiche de bien →</a></article>
        @endif
    </section>

    <section class="panel inventory-panel">
        <form class="filter-bar" method="GET" action="{{ route('biens.index') }}">
            <label class="search-field"><span>⌕</span><input name="q" value="{{ request('q') }}" placeholder="Code inventaire, désignation, localisation…" aria-label="Rechercher un bien"></label>
            <select name="categorie_id" aria-label="Filtrer par catégorie"><option value="">Toutes les catégories</option>@foreach($categories as $categorie)<option value="{{ $categorie->id }}" @selected(request('categorie_id') == $categorie->id)>{{ $categorie->nom }}</option>@endforeach</select>
            <select name="service_id" aria-label="Filtrer par service"><option value="">Tous les services</option>@foreach($services as $service)<option value="{{ $service->id }}" @selected(request('service_id') == $service->id)>{{ $service->nom }}</option>@endforeach</select>
            <select name="etat" aria-label="Filtrer par état"><option value="">Tous les états</option>@foreach($etats as $etat)<option value="{{ $etat }}" @selected(request('etat') === $etat)>{{ str_replace('_', ' ', $etat) }}</option>@endforeach</select>
            <button class="button button-dark" type="submit">Filtrer</button>
            @if(request()->hasAny(['q', 'categorie_id', 'service_id', 'etat']))<a class="reset-link" href="{{ route('biens.index') }}">Effacer</a>@endif
        </form>
        @include('biens.partials.table', ['biens' => $biens, 'compact' => false])
        <div class="pagination-wrap"><span>{{ $biens->firstItem() ?? 0 }}–{{ $biens->lastItem() ?? 0 }} sur {{ $biens->total() }} biens</span>{{ $biens->links() }}</div>
    </section>
    <div class="compliance-note"><span>◆</span><p><strong>Registre patrimonial</strong><br>Les biens archivés restent conservés dans l’historique et ne sont plus affichés dans l’inventaire actif.</p></div>
@endsection
