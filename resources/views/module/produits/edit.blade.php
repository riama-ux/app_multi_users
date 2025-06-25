@extends('pages.admin.shared.layout')


@section('content')
    <h3>Modifier le produit</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('module.produits.update', $produit) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nom">Nom</label>
            <input type="text" name="nom" value="{{ old('nom', $produit->nom) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="code">Code</label>
            <input type="text" name="code" value="{{ old('code', $produit->code) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="categorie_id">Catégorie</label>
            <select name="categorie_id" class="form-select" required>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $produit->categorie_id == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="prix_achat">Prix d’achat</label>
            <input type="number" name="prix_achat" value="{{ old('prix_achat', $produit->prix_achat) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="prix_vente">Prix de vente</label>
            <input type="number" name="prix_vente" value="{{ old('prix_vente', $produit->prix_vente) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description">Description</label>
            <textarea name="description" class="form-control">{{ old('description', $produit->description) }}</textarea>
        </div>

        <button class="btn btn-primary">Modifier</button>
        <a href="{{ route('module.produits.index') }}" class="btn btn-secondary">Retour</a>
    </form>
@endsection
