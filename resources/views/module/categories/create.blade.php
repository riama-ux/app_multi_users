@extends('pages.admin.shared.layout')


@section('content')
    <div class="nk-block-head-content d-flex justify-content-between align-items-center mb-4">
        <h3 class="nk-block-title">Ajouter une catégorie</h3>
        <a href="{{ route('module.categories.index') }}" class="btn btn-outline-primary">
            <em class="icon ni ni-list"></em><span>Liste des catégorie</span>
        </a>
    </div>

    <form action="{{ route('module.categories.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nom" class="form-label">Nom de la catégorie</label>
            <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('module.categories.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection
