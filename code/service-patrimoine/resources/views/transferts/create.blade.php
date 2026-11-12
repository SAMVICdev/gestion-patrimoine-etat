@extends('layouts.app')

@section('title', 'Demander un transfert')

@section('content')
    <div class="breadcrumbs"><a href="{{ route('transferts.index') }}">Transferts</a><span>/</span><strong>Nouvelle demande</strong></div>
    <section class="page-intro"><div><span class="eyebrow">MOUVEMENT SOUMIS À VALIDATION</span><h1>Demander un transfert</h1><p>L’affectation du bien ne change qu’après approbation par un administrateur national.</p></div></section>
    <form class="panel form-panel" method="POST" enctype="multipart/form-data" action="{{ route('transferts.store') }}">@csrf
        <div class="form-grid">
            <label class="field field-full"><span>Bien à transférer <b>*</b></span><select name="bien_id" required><option value="">Choisir un bien de votre périmètre</option>@foreach($biens as $bien)<option value="{{ $bien->id }}" @selected(old('bien_id') == $bien->id)>{{ $bien->numero_inventaire }} · {{ $bien->designation }} — {{ $bien->service->nom }}</option>@endforeach</select>@error('bien_id')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="field"><span>Service destinataire <b>*</b></span><select name="service_destination_id" required><option value="">Choisir le service</option>@foreach($servicesDestination as $service)<option value="{{ $service->id }}" @selected(old('service_destination_id') == $service->id)>{{ $service->ministere?->nom ? $service->ministere->nom.' · ' : '' }}{{ $service->nom }}</option>@endforeach</select>@error('service_destination_id')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="field"><span>Nouvelle localisation <b>*</b></span><input name="localisation_destination" value="{{ old('localisation_destination') }}" required placeholder="Ville, bâtiment, bureau…">@error('localisation_destination')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="field"><span>Date d’effet souhaitée</span><input type="date" name="date_effet" value="{{ old('date_effet') }}" min="{{ now()->toDateString() }}">@error('date_effet')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="field"><span>Pièce justificative (PDF ou image, 5 Mo max.)</span><input type="file" name="justificatif" accept=".pdf,.jpg,.jpeg,.png">@error('justificatif')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="field field-full"><span>Motif du transfert <b>*</b></span><textarea name="motif" rows="4" required placeholder="Expliquez la raison et le contexte du mouvement…">{{ old('motif') }}</textarea>@error('motif')<small class="field-error">{{ $message }}</small>@enderror</label>
        </div>
        <div class="form-actions"><a class="button button-secondary" href="{{ route('transferts.index') }}">Annuler</a><button class="button button-primary" type="submit">Soumettre à validation <span>→</span></button></div>
    </form>
@endsection
