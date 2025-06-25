@extends('pages.admin.shared.layout')

@section('content')
<h3>Modifier le fournisseur</h3>

<form action="{{ route('module.fournisseurs.update', $fournisseur->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="nom">Nom *</label>
        <input type="text" name="nom" class="form-control" required value="{{ old('nom', $fournisseur->nom) }}">
    </div>

    <div class="mb-3">
        <label for="telephone">Téléphone</label>
        <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $fournisseur->telephone) }}">
    </div>

    <div class="mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $fournisseur->email) }}">
    </div>

    <div class="mb-3">
        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $fournisseur->adresse) }}">
    </div>

    <button type="submit" class="btn btn-primary">Mettre à jour</button>
    <a href="{{ route('module.fournisseurs.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@endsection
