@extends('layouts.app')

@section('title', $bien->designation)

@section('content')
    <div class="breadcrumbs"><a href="{{ route('biens.index') }}">Biens & inventaire</a><span>/</span><strong>{{ $bien->numero_inventaire }}</strong></div>
    <section class="detail-hero">
        <div><span class="inventory-code">{{ $bien->numero_inventaire }}</span><h1>{{ $bien->designation }}</h1><p>{{ $bien->categorie->nom }} <span>·</span> {{ $bien->localisation }}</p></div>
        <div class="detail-actions"><a class="button button-secondary" href="{{ route('biens.edit', $bien) }}">Modifier</a><form method="POST" action="{{ route('biens.archive', $bien) }}" onsubmit="return confirm('Archiver ce bien ? Il restera conservé dans l’historique.');">@csrf<button class="button button-danger" type="submit">Archiver</button></form></div>
    </section>
    <section class="detail-grid">
        <article class="panel detail-panel"><span class="eyebrow">INFORMATIONS DU BIEN</span><h2>Fiche patrimoniale</h2>
            <dl class="detail-list">
                <div><dt>État actuel</dt><dd><span class="status-pill status-{{ $bien->etat }}">{{ str_replace('_', ' ', $bien->etat) }}</span></dd></div>
                <div><dt>Catégorie</dt><dd>{{ $bien->categorie->nom }}</dd></div>
                <div><dt>Service responsable</dt><dd>{{ $bien->service->nom }}</dd></div>
                <div><dt>Localisation</dt><dd>{{ $bien->localisation }}</dd></div>
                <div><dt>Responsable / détenteur</dt><dd>{{ $bien->responsable ?: 'Non renseigné' }}</dd></div>
                <div><dt>Date d’acquisition</dt><dd>{{ $bien->date_acquisition?->format('d/m/Y') ?? 'Non renseignée' }}</dd></div>
                <div><dt>Valeur d’acquisition</dt><dd>{{ $bien->valeur_acquisition ? number_format((float) $bien->valeur_acquisition, 0, ',', ' ').' FCFA' : 'Non renseignée' }}</dd></div>
                <div><dt>Source de financement</dt><dd>{{ $bien->source_financement ?: 'Non renseignée' }}</dd></div>
                <div class="detail-full"><dt>Description</dt><dd>{{ $bien->description ?: 'Aucune description.' }}</dd></div>
            </dl>
        </article>
        <aside class="panel detail-side"><span class="eyebrow">TRAÇABILITÉ</span><h2>Historique de la fiche</h2><div class="timeline-entry"><span class="timeline-dot"></span><div><strong>Fiche créée</strong><small>{{ $bien->created_at->format('d/m/Y à H:i') }}</small></div></div>@if($bien->updated_at->ne($bien->created_at))<div class="timeline-entry"><span class="timeline-dot gold-dot"></span><div><strong>Dernière mise à jour</strong><small>{{ $bien->updated_at->format('d/m/Y à H:i') }}</small></div></div>@endif<p class="muted">L’historique détaillé des changements sera disponible avec le journal d’audit.</p></aside>
    </section>
@endsection
