@extends('pages.admin.shared.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="nk-block-title">Ajouter un fournisseur</h3>
        <a href="{{ route('module.fournisseurs.index') }}" class="btn btn-outline-primary shadow-sm d-flex align-items-center">
            <em class="icon ni ni-list me-2"></em>Liste des fournisseurs
        </a>
</div>

<form action="{{ route('module.fournisseurs.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="nom">Nom *</label>
        <input type="text" name="nom" class="form-control" required value="{{ old('nom') }}">
    </div>

    <div class="mb-3">
        <label for="telephone">Téléphone</label>
        <input type="text" name="telephone" class="form-control" value="{{ old('telephone') }}">
    </div>

    <div class="mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
    </div>

    <div class="mb-3">
        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" class="form-control" value="{{ old('adresse') }}">
    </div>

    <button type="submit" class="btn btn-success">Enregistrer</button>
    <a href="{{ route('module.fournisseurs.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@endsection
