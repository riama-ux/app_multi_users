@extends('pages.admin.shared.layout')

@section('content')
    <h3>Modifier le magasin</h3>

    <form action="{{ route('admin.magasins.update', $magasin->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nom">Nom du magasin *</label>
            <input type="text" name="nom" class="form-control" required value="{{ old('nom', $magasin->nom) }}">
        </div>

        <div class="mb-3">
            <label for="adresse">Adresse</label>
            <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $magasin->adresse) }}">
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('admin.magasins.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection
