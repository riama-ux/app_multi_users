@extends('pages.admin.shared.layout')

@section('content')
    <h3>Ajouter un magasin</h3>

    <form action="{{ route('admin.magasins.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nom">Nom du magasin *</label>
            <input type="text" name="nom" class="form-control" required value="{{ old('nom') }}">
        </div>

        <div class="mb-3">
            <label for="adresse">Adresse</label>
            <input type="text" name="adresse" class="form-control" value="{{ old('adresse') }}">
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('admin.magasins.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection
