@extends('layouts.app')

@section('title', 'Modifier un bien')

@section('content')
    <div class="breadcrumbs"><a href="{{ route('biens.index') }}">Biens & inventaire</a><span>/</span><a href="{{ route('biens.show', $bien) }}">{{ $bien->numero_inventaire }}</a><span>/</span><strong>Modifier</strong></div>
    <section class="page-intro"><div><span class="eyebrow">MISE À JOUR DE LA FICHE</span><h1>Modifier le bien</h1><p>{{ $bien->numero_inventaire }} · {{ $bien->designation }}</p></div></section>
    <form class="panel form-panel" method="POST" action="{{ route('biens.update', $bien) }}">
        @csrf
        @method('PUT')
        @include('biens.partials.form')
    </form>
@endsection
