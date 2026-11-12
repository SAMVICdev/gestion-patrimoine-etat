@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <section class="welcome-panel">
        <div>
            <span class="eyebrow">DIRECTION GÉNÉRALE DU PATRIMOINE</span>
            <h1>Le patrimoine public,<br>en toute clarté.</h1>
            <p>Suivez les biens, les affectations et la valeur de votre parc administratif.</p>
        </div>
        @if(auth()->user()->canManageAssets())
            <a class="button button-primary" href="{{ route('biens.create') }}"><span>＋</span> Enregistrer un bien</a>
        @endif
    </section>

    <section class="metric-grid" aria-label="Indicateurs du patrimoine">
        <article class="metric-card"><span class="metric-icon">▤</span><span class="metric-label">BIENS AU REGISTRE</span><strong>{{ number_format($totalBiens, 0, ',', ' ') }}</strong><small>Biens actifs recensés</small></article>
        <article class="metric-card"><span class="metric-icon gold">₣</span><span class="metric-label">VALEUR D’ACQUISITION</span><strong>{{ number_format((float) $totalValeur, 0, ',', ' ') }} <small>FCFA</small></strong><small>Valeur connue cumulée</small></article>
        <article class="metric-card"><span class="metric-icon blue">◫</span><span class="metric-label">CATÉGORIES</span><strong>{{ number_format($totalCategories, 0, ',', ' ') }}</strong><small>Types de biens suivis</small></article>
        <article class="metric-card"><span class="metric-icon green">⌂</span><span class="metric-label">SERVICES</span><strong>{{ number_format($totalServices, 0, ',', ' ') }}</strong><small>Services responsables</small></article>
    </section>
    <section class="quick-actions"><a href="{{ route('transferts.index') }}"><span>⇄</span><strong>Affectations & transferts</strong><small>Suivre les mouvements →</small></a><a href="{{ route('inventaires.index') }}"><span>◷</span><strong>Inventaires physiques</strong><small>Contrôler les biens →</small></a><a href="{{ route('rapports.index') }}"><span>▥</span><strong>Rapports patrimoniaux</strong><small>Consulter les indicateurs →</small></a></section>

    <section class="dashboard-grid">
        <article class="panel state-panel">
            <div class="panel-heading"><div><span class="eyebrow">SITUATION DU PARC</span><h2>État des biens</h2></div><a class="text-link" href="{{ route('biens.index') }}">Voir l’inventaire →</a></div>
            @php($stateLabels = ['bon_etat' => 'Bon état', 'etat_moyen' => 'État moyen', 'a_reparer' => 'À réparer', 'en_reparation' => 'En réparation', 'hors_service' => 'Hors service', 'introuvable' => 'Introuvable'])
            <div class="state-list">
                @foreach ($stateLabels as $key => $label)
                    @php($count = (int) ($stateCounts[$key] ?? 0))
                    @php($percentage = $totalBiens > 0 ? round($count / $totalBiens * 100) : 0)
                    <div class="state-row"><span class="state-dot state-{{ $key }}"></span><span>{{ $label }}</span><div class="progress-track"><span style="width: {{ $percentage }}%"></span></div><strong>{{ $count }}</strong></div>
                @endforeach
            </div>
        </article>
        <article class="panel category-panel">
            <div class="panel-heading"><div><span class="eyebrow">RÉPARTITION</span><h2>Par catégorie</h2></div>@if(auth()->user()->canManageAssets())<a class="text-link" href="{{ route('categories.index') }}">Gérer →</a>@endif</div>
            @forelse ($categories as $categorie)
                @php($share = $totalBiens > 0 ? round($categorie->biens_count / $totalBiens * 100) : 0)
                <div class="category-row"><span>{{ $categorie->nom }}</span><strong>{{ $categorie->biens_count }}</strong><div class="mini-track"><i style="width: {{ $share }}%"></i></div></div>
            @empty
                <p class="empty-note">Les catégories apparaîtront ici dès leur création.</p>
            @endforelse
        </article>
    </section>

    <section class="panel recent-panel">
        <div class="panel-heading"><div><span class="eyebrow">DERNIÈRES INSCRIPTIONS</span><h2>Biens récemment enregistrés</h2></div><a class="button button-secondary" href="{{ route('biens.index') }}">Tout l’inventaire <span>→</span></a></div>
        @include('biens.partials.table', ['biens' => $recentBiens, 'compact' => true])
    </section>
@endsection
