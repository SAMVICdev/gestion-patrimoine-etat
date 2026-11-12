@extends('layouts.app')

@section('title', 'Enregistrer un bien')

@section('content')
    <div class="breadcrumbs"><a href="{{ route('biens.index') }}">Biens & inventaire</a><span>/</span><strong>Nouveau bien</strong></div>
    <section class="page-intro"><div><span class="eyebrow">NOUVELLE FICHE PATRIMONIALE</span><h1>Enregistrer un bien</h1><p>Renseignez les informations principales pour intégrer le bien au registre.</p></div></section>
    <form class="panel form-panel" method="POST" action="{{ route('biens.store') }}">
        @csrf
        @include('biens.partials.form')
    </form>
@endsection
