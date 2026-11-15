@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    @php
        $stateLabels = ['bon_etat' => 'Bon état', 'etat_moyen' => 'État moyen', 'a_reparer' => 'À réparer', 'en_reparation' => 'En réparation', 'hors_service' => 'Hors service', 'introuvable' => 'Introuvable'];
        $widgetMeta = [
            'welcome' => 'Bienvenue',
            'metrics' => 'Indicateurs clés',
            'actions' => 'Actions rapides',
            'states' => 'État des biens',
            'categories' => 'Par catégorie',
            'depreciation' => 'Dépréciation',
            'recent' => 'Biens récents',
        ];
        $wideWidgets = ['welcome', 'metrics', 'actions', 'recent'];
    @endphp

    <div class="dash-toolbar">
        <span class="dash-toolbar-hint" id="dash-hint">Organisation de votre espace de pilotage.</span>
        <div class="dash-toolbar-actions">
            <button type="button" class="button button-secondary" id="dash-customize">Personnaliser</button>
            <button type="button" class="button button-primary" id="dash-save" hidden>Enregistrer</button>
            <button type="button" class="button button-secondary" id="dash-reset" hidden>Réinitialiser</button>
            <button type="button" class="button button-secondary" id="dash-cancel" hidden>Annuler</button>
        </div>
    </div>

    <div class="dashboard-widgets" id="dashboard-widgets">

        @foreach($widgets as $widget)
            @php($isHidden = in_array($widget, $hiddenWidgets, true))
            <div class="dash-widget {{ in_array($widget, $wideWidgets, true) ? 'dash-widget--wide' : '' }} {{ $isHidden ? 'dash-widget--off' : '' }}"
                 data-widget="{{ $widget }}">

                @if($widget === 'welcome')
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

                @elseif($widget === 'metrics')
                    <section class="metric-grid" aria-label="Indicateurs du patrimoine">
                        <article class="metric-card"><span class="metric-icon">▤</span><span class="metric-label">BIENS AU REGISTRE</span><strong>{{ number_format($totalBiens, 0, ',', ' ') }}</strong><small>Biens actifs recensés</small></article>
                        <article class="metric-card"><span class="metric-icon gold">₣</span><span class="metric-label">VALEUR D’ACQUISITION</span><strong>{{ number_format((float) $totalValeur, 0, ',', ' ') }} <small>FCFA</small></strong><small>Valeur connue cumulée</small></article>
                        <article class="metric-card"><span class="metric-icon green">◫</span><span class="metric-label">VALEUR RÉSIDUELLE</span><strong>{{ number_format($valeurResiduelle, 0, ',', ' ') }} <small>FCFA</small></strong><small>Après dépréciation linéaire</small></article>
                        <article class="metric-card"><span class="metric-icon blue">⌂</span><span class="metric-label">SERVICES</span><strong>{{ number_format($totalServices, 0, ',', ' ') }}</strong><small>Services responsables</small></article>
                    </section>

                @elseif($widget === 'actions')
                    <section class="quick-actions"><a href="{{ route('transferts.index') }}"><span>⇄</span><strong>Affectations & transferts</strong><small>Suivre les mouvements →</small></a><a href="{{ route('inventaires.index') }}"><span>◷</span><strong>Inventaires physiques</strong><small>Contrôler les biens →</small></a><a href="{{ route('rapports.index') }}"><span>▥</span><strong>Rapports patrimoniaux</strong><small>Consulter les indicateurs →</small></a></section>

                @elseif($widget === 'states')
                    <article class="panel state-panel">
                        <div class="panel-heading"><div><span class="eyebrow">SITUATION DU PARC</span><h2>État des biens</h2></div><a class="text-link" href="{{ route('biens.index') }}">Voir l’inventaire →</a></div>
                        <div class="state-list">
                            @foreach ($stateLabels as $key => $label)
                                @php($count = (int) ($stateCounts[$key] ?? 0))
                                @php($percentage = $totalBiens > 0 ? round($count / $totalBiens * 100) : 0)
                                <div class="state-row"><span class="state-dot state-{{ $key }}"></span><span>{{ $label }}</span><div class="progress-track"><span style="width: {{ $percentage }}%"></span></div><strong>{{ $count }}</strong></div>
                            @endforeach
                        </div>
                    </article>

                @elseif($widget === 'categories')
                    <article class="panel category-panel">
                        <div class="panel-heading"><div><span class="eyebrow">RÉPARTITION</span><h2>Par catégorie</h2></div>@if(auth()->user()->canManageAssets())<a class="text-link" href="{{ route('categories.index') }}">Gérer →</a>@endif</div>
                        @forelse ($categories as $categorie)
                            @php($share = $totalBiens > 0 ? round($categorie->biens_count / $totalBiens * 100) : 0)
                            <div class="category-row"><span>{{ $categorie->nom }}</span><strong>{{ $categorie->biens_count }}</strong><div class="mini-track"><i style="width: {{ $share }}%"></i></div></div>
                        @empty
                            <p class="empty-note">Les catégories apparaîtront ici dès leur création.</p>
                        @endforelse
                    </article>

                @elseif($widget === 'depreciation')
                    <article class="panel depreciation-panel">
                        <div class="panel-heading"><div><span class="eyebrow">AMORTISSEMENT</span><h2>Dépréciation linéaire</h2></div><a class="text-link" href="{{ route('categories.index') }}">Durées de vie →</a></div>
                        <div class="depreciation-summary">
                            <div><span class="metric-label">VALEUR D’ACQUISITION</span><strong>{{ number_format((float) $totalValeur, 0, ',', ' ') }} FCFA</strong></div>
                            <div><span class="metric-label">VALEUR RÉSIDUELLE</span><strong>{{ number_format($valeurResiduelle, 0, ',', ' ') }} FCFA</strong></div>
                            <div><span class="metric-label">DÉPRÉCIATION</span><strong>{{ number_format(max(0, (float) $totalValeur - $valeurResiduelle), 0, ',', ' ') }} FCFA</strong></div>
                        </div>
                        @if(count($depreciation['par_categorie']))
                            <div class="state-list">
                                @foreach($depreciation['par_categorie'] as $nom => $montant)
                                    <div class="state-row"><span>{{ $nom }}</span><div class="progress-track"><span style="width: {{ $totalValeur > 0 ? min(100, round($montant / max(1, (float) $totalValeur) * 100)) : 0 }}%"></span></div><strong>{{ number_format($montant, 0, ',', ' ') }}</strong></div>
                                @endforeach
                            </div>
                            <p class="muted" style="margin-top:10px; font-size:11px;">Valeur résiduelle estimée selon la durée de vie définie pour chaque catégorie (amortissement linéaire).</p>
                        @else
                            <p class="empty-note">Aucune catégorie dotée d'une durée de vie. Renseignez-la dans les catégories pour activer la dépréciation.</p>
                        @endif
                    </article>

                @elseif($widget === 'recent')
                    <section class="panel recent-panel">
                        <div class="panel-heading"><div><span class="eyebrow">DERNIÈRES INSCRIPTIONS</span><h2>Biens récemment enregistrés</h2></div><a class="button button-secondary" href="{{ route('biens.index') }}">Tout l’inventaire <span>→</span></a></div>
                        @include('biens.partials.table', ['biens' => $recentBiens, 'compact' => true])
                    </section>
                @endif

                <div class="dash-widget-controls">
                    <span class="dash-widget-name">{{ $widgetMeta[$widget] }}</span>
                    <button type="button" class="dash-widget-btn" data-action="up" title="Monter">↑</button>
                    <button type="button" class="dash-widget-btn" data-action="down" title="Descendre">↓</button>
                    <button type="button" class="dash-widget-btn" data-action="toggle" title="Afficher / masquer">👁</button>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const container = document.getElementById('dashboard-widgets');
    const customizeBtn = document.getElementById('dash-customize');
    const saveBtn = document.getElementById('dash-save');
    const resetBtn = document.getElementById('dash-reset');
    const cancelBtn = document.getElementById('dash-cancel');
    const hint = document.getElementById('dash-hint');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const url = @json(route('dashboard.preferences'));
    const defaultOrder = @json(array_keys($widgetMeta));
    let originalHTML = null;

    function widgets() {
        return Array.from(container.querySelectorAll('.dash-widget'));
    }

    function setCustomizing(on) {
        container.classList.toggle('is-customizing', on);
        customizeBtn.hidden = on;
        saveBtn.hidden = !on;
        resetBtn.hidden = !on;
        cancelBtn.hidden = !on;
        hint.textContent = on
            ? 'Mode personnalisation : montez, descendez ou masquez les blocs, puis enregistrez.'
            : 'Organisation de votre espace de pilotage.';
    }

    customizeBtn.addEventListener('click', function () {
        originalHTML = container.innerHTML;
        setCustomizing(true);
    });

    cancelBtn.addEventListener('click', function () {
        if (originalHTML !== null) container.innerHTML = originalHTML;
        setCustomizing(false);
    });

    container.addEventListener('click', function (event) {
        const btn = event.target.closest('.dash-widget-btn');
        if (!btn) return;
        const widget = btn.closest('.dash-widget');
        const action = btn.dataset.action;

        if (action === 'up' && widget.previousElementSibling) {
            container.insertBefore(widget, widget.previousElementSibling);
        } else if (action === 'down' && widget.nextElementSibling) {
            container.insertBefore(widget.nextElementSibling, widget);
        } else if (action === 'toggle') {
            widget.classList.toggle('dash-widget--off');
        }
    });

    function persist(order, hidden) {
        const body = new URLSearchParams();
        order.forEach(w => body.append('widgets[]', w));
        hidden.forEach(w => body.append('hidden[]', w));
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: body.toString(),
        });
    }

    saveBtn.addEventListener('click', function () {
        const order = widgets().map(w => w.dataset.widget);
        const hidden = widgets().filter(w => w.classList.contains('dash-widget--off')).map(w => w.dataset.widget);
        persist(order, hidden).then(() => window.location.reload());
    });

    resetBtn.addEventListener('click', function () {
        persist(defaultOrder, []).then(() => window.location.reload());
    });
})();
</script>
@endpush
