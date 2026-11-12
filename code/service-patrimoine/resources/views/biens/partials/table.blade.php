<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>CODE INVENTAIRE</th><th>DÉSIGNATION</th><th>CATÉGORIE</th><th>SERVICE RESPONSABLE</th><th>LOCALISATION</th><th>ÉTAT</th></tr></thead>
        <tbody>
        @forelse ($biens as $bien)
            <tr>
                <td><a class="inventory-code" href="{{ route('biens.show', $bien) }}">{{ $bien->numero_inventaire }}</a></td>
                <td><a class="item-name" href="{{ route('biens.show', $bien) }}">{{ $bien->designation }}</a>@if($bien->valeur_acquisition)<small class="cell-sub">{{ number_format((float) $bien->valeur_acquisition, 0, ',', ' ') }} FCFA</small>@endif</td>
                <td><span class="category-tag">{{ $bien->categorie->nom }}</span></td>
                <td>{{ $bien->service->nom }}</td>
                <td><span class="location-mark">⌖</span> {{ $bien->localisation }}</td>
                <td><span class="status-pill status-{{ $bien->etat }}">{{ str_replace('_', ' ', $bien->etat) }}</span></td>
            </tr>
        @empty
            <tr><td class="table-empty" colspan="6"><span>▤</span><strong>Aucun bien pour le moment</strong><small>Enregistrez le premier bien ou ajustez vos filtres.</small></td></tr>
        @endforelse
        </tbody>
    </table>
</div>
