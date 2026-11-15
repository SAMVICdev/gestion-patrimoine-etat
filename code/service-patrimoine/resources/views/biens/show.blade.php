@extends('layouts.app')

@section('title', $bien->designation)

@section('content')
    <div class="breadcrumbs"><a href="{{ route('biens.index') }}">Biens & inventaire</a><span>/</span><strong>{{ $bien->numero_inventaire }}</strong></div>
    <section class="detail-hero">
        <div><span class="inventory-code">{{ $bien->numero_inventaire }}</span><h1>{{ $bien->designation }}</h1><p>{{ $bien->categorie->nom }} <span>·</span> {{ $bien->localisation }}</p></div>
        @if(auth()->user()->canManageAssets())
            <div class="detail-actions"><a class="button button-secondary" href="{{ route('biens.edit', $bien) }}">Modifier</a><form method="POST" action="{{ route('biens.archive', $bien) }}" onsubmit="return confirm('Archiver ce bien ? Il restera conservé dans l’historique.');">@csrf<button class="button button-danger" type="submit">Archiver</button></form></div>
        @endif
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
                <div><dt>Valeur résiduelle estimée</dt><dd>@if($bien->valeurResiduelle() !== null){{ number_format($bien->valeurResiduelle(), 0, ',', ' ').' FCFA' }} <small class="cell-sub">amortissement linéaire · durée de vie {{ $bien->categorie->duree_vie_annees }} ans</small>@else<span class="muted">Non calculable — définir une durée de vie sur la catégorie</span>@endif</dd></div>
                <div><dt>Source de financement</dt><dd>{{ $bien->source_financement ?: 'Non renseignée' }}</dd></div>
                <div class="detail-full"><dt>Description</dt><dd>{{ $bien->description ?: 'Aucune description.' }}</dd></div>
            </dl>
        </article>
        <aside class="panel detail-side"><span class="eyebrow">HISTORIQUE DES ÉTATS</span><h2>Évolutions enregistrées</h2><div class="timeline-entry"><span class="timeline-dot"></span><div><strong>Fiche créée</strong><small>{{ $bien->created_at->format('d/m/Y à H:i') }}</small></div></div>@forelse($etatHistories as $history)<div class="timeline-entry"><span class="timeline-dot gold-dot"></span><div><strong>{{ ucfirst(str_replace('_', ' ', $history->etat_origine)) }} → {{ ucfirst(str_replace('_', ' ', $history->etat_destination)) }}</strong><small>{{ $history->date_modification->format('d/m/Y à H:i') }} · {{ $history->agent?->name ?? 'Système' }}</small>@if($history->commentaire)<p>{{ $history->commentaire }}</p>@endif</div></div>@empty<p class="muted">Aucun changement d’état n’a été enregistré.</p>@endforelse</aside>
    </section>

    <section class="detail-grid">
        <article class="panel detail-panel">
            <span class="eyebrow">IDENTIFICATION</span>
            <h2>QR code & étiquette</h2>
            <div class="qr-box">{!! $qrSvg !!}</div>
            <p class="muted">Code QR unique pointant vers la fiche de ce bien. Imprimez l'étiquette pour l'apposer sur le bien physique.</p>
            <div class="qr-actions">
                <a class="button button-primary" href="{{ route('biens.etiquette', $bien) }}">Étiquette à imprimer →</a>
                <a class="button button-secondary" href="{{ route('biens.pdf', $bien) }}">Télécharger la fiche PDF ↓</a>
            </div>
        </article>

        <article class="panel detail-panel">
            <span class="eyebrow">PREUVES VISUELLES</span>
            <h2>Photos du bien</h2>
            @if($bien->photos->isNotEmpty())
                <div class="photo-grid">
                    @foreach($bien->photos as $photo)
                        <figure class="photo-item">
                            <a href="{{ route('biens.photos.file', [$bien, $photo]) }}" target="_blank" rel="noopener">
                                <img src="{{ route('biens.photos.file', [$bien, $photo]) }}"
                                     alt="{{ $photo->legende ?: $bien->designation }}"
                                     loading="lazy">
                            </a>
                            @if($photo->legende)<figcaption>{{ $photo->legende }}</figcaption>@endif
                            @if(auth()->user()->canManageAssets())
                                <form class="photo-delete-form" method="POST"
                                      action="{{ route('biens.photos.destroy', [$bien, $photo]) }}"
                                      onsubmit="return confirm('Supprimer cette photo ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="photo-delete" type="submit" title="Supprimer">✕</button>
                                </form>
                            @endif
                        </figure>
                    @endforeach
                </div>
            @else
                <p class="muted">Aucune photo n'a encore été jointe à ce bien.</p>
            @endif

            @if(auth()->user()->canManageAssets())
                <form class="photo-upload" method="POST"
                      action="{{ route('biens.photos.store', $bien) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp"
                           multiple required aria-label="Photos du bien">
                    <button class="button button-secondary" type="submit">Ajouter des photos</button>
                </form>
                @error('photos')<p class="field-error">{{ $message }}</p>@enderror
                @error('photos.*')<p class="field-error">{{ $message }}</p>@enderror
            @endif
        </article>
    </section>

    @if($etatHistories->contains(fn ($history) => $history->justificatif))
        <section class="panel recent-panel">
            <div class="panel-heading"><div><span class="eyebrow">PIÈCES JOINTES</span><h2>Justificatifs des changements d’état</h2></div></div>
            @foreach($etatHistories as $history)
                @if($history->justificatif)
                    <a class="text-link" href="{{ route('biens.historique.justificatif', [$bien, $history]) }}">
                        Télécharger le justificatif du {{ $history->date_modification->format('d/m/Y') }} →
                    </a>
                @endif
            @endforeach
        </section>
    @endif
    <section class="panel recent-panel"><div class="panel-heading"><div><span class="eyebrow">AFFECTATIONS SUCCESSIVES</span><h2>Historique des transferts</h2></div><a class="text-link" href="{{ route('transferts.index') }}">Tous les mouvements →</a></div><div class="table-wrap"><table class="data-table"><thead><tr><th>ORIGINE</th><th>DESTINATION</th><th>DEMANDE</th><th>DEMANDEUR</th><th>STATUT</th></tr></thead><tbody>@forelse($transferts as $transfert)<tr><td>{{ $transfert->serviceOrigine->nom }}<small class="cell-sub">{{ $transfert->localisation_origine }}</small></td><td>{{ $transfert->serviceDestination->nom }}<small class="cell-sub">{{ $transfert->localisation_destination }}</small></td><td>{{ $transfert->date_demande->format('d/m/Y') }}</td><td>{{ $transfert->demandeur->name }}</td><td><a class="status-pill status-{{ $transfert->statut }}" href="{{ route('transferts.show', $transfert) }}">{{ str_replace('_', ' ', $transfert->statut) }}</a></td></tr>@empty<tr><td class="table-empty" colspan="5"><strong>Aucun mouvement enregistré pour ce bien</strong></td></tr>@endforelse</tbody></table></div></section>
@endsection
