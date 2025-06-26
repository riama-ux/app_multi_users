@extends('pages.admin.shared.layout')


@section('content')
    <h3>Ajouter un produit</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('module.produits.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nom">Nom</label>
            <input type="text" name="nom" value="{{ old('nom') }}" class="form-control" required>
        </div>


        <div class="mb-3">
            <label for="categorie_id">Catégorie</label>
            <select name="categorie_id" class="form-select" required>
                <option value="">-- Choisir --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('categorie_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="prix_achat">Prix d’achat</label>
            <input type="number" name="prix_achat" value="{{ old('prix_achat') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="cout_achat">Coût d'achat</label>
            <input type="number" name="cout_achat" class="form-control" required min="0" value="{{ old('cout_achat') }}">
        </div>

        <div class="mb-3">
            <label for="prix_vente">Prix de vente</label>
            <input type="number" name="prix_vente" value="{{ old('prix_vente') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description">Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('module.produits.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection
