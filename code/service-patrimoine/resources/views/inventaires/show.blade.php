@extends('layouts.app')

@section('title', $campagne->titre)

@section('content')
    <div class="breadcrumbs"><a href="{{ route('inventaires.index') }}">Inventaires physiques</a><span>/</span><strong>{{ $campagne->titre }}</strong></div>
    <section class="page-intro"><div><span class="status-pill status-{{ $campagne->statut }}">{{ str_replace('_', ' ', $campagne->statut) }}</span><h1>{{ $campagne->titre }}</h1><p>{{ $campagne->service?->nom ?? 'Tous les services' }} · Débutée le {{ $campagne->date_debut->format('d/m/Y') }} · {{ $campagne->constats->count() }} constat(s)</p></div>@if($campagne->statut === 'en_cours' && auth()->user()->canManageAssets())<form method="POST" action="{{ route('inventaires.close', $campagne) }}" onsubmit="return confirm('Clôturer définitivement cette campagne ?');">@csrf<button class="button button-secondary" type="submit">Clôturer la campagne</button></form>@endif</section>
    @if($campagne->statut === 'en_cours')
    <form class="panel form-panel" id="constat-form" method="POST" action="{{ route('inventaires.constats', $campagne) }}">@csrf<span class="eyebrow">NOUVEAU CONSTAT · {{ now()->format('d/m/Y H:i') }}</span><div id="offline-queue-notice" class="offline-queue-notice" hidden></div><div class="form-grid inventory-check-form"><label class="field"><span>Bien contrôlé <b>*</b></span><select name="bien_id" required><option value="">Sélectionner un bien non vérifié</option>@foreach($biens->whereNotIn('id', $campagne->constats->pluck('bien_id')) as $bien)<option value="{{ $bien->id }}">{{ $bien->numero_inventaire }} · {{ $bien->designation }}</option>@endforeach</select></label><label class="field"><span>Résultat <b>*</b></span><select name="resultat" required><option value="present">Présent et conforme</option><option value="degrade">Présent mais dégradé</option><option value="introuvable">Introuvable · vérification requise</option></select></label><label class="field field-full"><span>Commentaire</span><textarea name="commentaire" rows="2" placeholder="Observations, état ou emplacement constaté…"></textarea></label></div><div class="form-actions"><button class="button button-primary" type="submit">Enregistrer le constat <span>→</span></button></div></form>
    @endif
    <section class="panel inventory-panel"><div class="panel-heading"><div><span class="eyebrow">RÉSULTATS HORODATÉS</span><h2>Constats de la campagne</h2></div></div><div class="table-wrap"><table class="data-table"><thead><tr><th>BIEN</th><th>RÉSULTAT</th><th>COMMENTAIRE</th><th>AGENT</th><th>DATE & HEURE</th></tr></thead><tbody>@forelse($campagne->constats as $constat)<tr><td><a class="item-name" href="{{ route('biens.show', $constat->bien) }}">{{ $constat->bien->numero_inventaire }} · {{ $constat->bien->designation }}</a></td><td><span class="status-pill status-{{ $constat->resultat === 'present' ? 'bon_etat' : ($constat->resultat === 'introuvable' ? 'introuvable' : 'etat_moyen') }}">{{ ucfirst($constat->resultat) }}</span></td><td>{{ $constat->commentaire ?: '—' }}</td><td>{{ $constat->agent->name }}</td><td>{{ $constat->date_constat->format('d/m/Y H:i') }}</td></tr>@empty<tr><td class="table-empty" colspan="5"><strong>Aucun constat pour le moment</strong><small>Les vérifications terrain apparaîtront ici, avec l’agent et l’heure.</small></td></tr>@endforelse</tbody></table></div></section>
@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('constat-form');
    if (!form) return;

    const QUEUE_KEY = 'constats_hors_ligne_{{ $campagne->id }}';
    const notice = document.getElementById('offline-queue-notice');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    function queue() {
        try { return JSON.parse(localStorage.getItem(QUEUE_KEY) || '[]'); }
        catch (e) { return []; }
    }

    function saveQueue(items) {
        localStorage.setItem(QUEUE_KEY, JSON.stringify(items));
        updateNotice();
    }

    function updateNotice() {
        const pending = queue().length;
        if (pending > 0) {
            notice.hidden = false;
            notice.textContent = pending + ' constat(s) en attente de synchronisation (mode hors ligne).';
        } else {
            notice.hidden = true;
        }
    }

    function syncQueue() {
        if (!navigator.onLine) return;
        const items = queue();
        if (!items.length) return;

        const remaining = [];
        let index = 0;

        function next() {
            if (index >= items.length) {
                saveQueue(remaining);
                if (!remaining.length) {
                    notice.hidden = true;
                    window.location.reload();
                }
                return;
            }
            const item = items[index++];
            const body = new URLSearchParams(item.fields);
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: body.toString(),
            }).then(function (response) {
                if (response.status === 422) {
                    // Conflit métier (doublon, campagne clôturée…) : on conserve
                    // l'élément pour que l'agent puisse corriger manuellement.
                    remaining.push(item);
                }
                next();
            }).catch(function () {
                remaining.push(item);
                next();
            });
        }
        next();
    }

    form.addEventListener('submit', function (event) {
        if (navigator.onLine) return; // envoi normal

        event.preventDefault();
        const data = new FormData(form);
        const fields = {};
        data.forEach(function (value, key) {
            if (key !== '_token') fields[key] = value;
        });
        const items = queue();
        items.push({ fields: fields, queued_at: new Date().toISOString() });
        saveQueue(items);
        form.reset();
        notice.textContent = 'Hors ligne : constat enregistré localement, il sera synchronisé automatiquement.';
        notice.hidden = false;
    });

    window.addEventListener('online', syncQueue);
    document.addEventListener('DOMContentLoaded', function () {
        updateNotice();
        syncQueue();
    });
})();
</script>
@endpush
