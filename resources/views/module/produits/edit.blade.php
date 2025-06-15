@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Modifier un produit</h4>

        @include('flash-message')

        <form action="{{ route('module.produits.update', $produit->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4">
                {{-- Nom du produit --}}
                <div class="col-md-6">
                    <label class="form-label">Nom du produit</label>
                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror"
                           value="{{ old('nom', $produit->nom) }}" required>
                    @error('nom') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Catégorie --}}
                <div class="col-md-6">
                    <label class="form-label">Catégorie</label>
                    <select name="categorie_id" class="form-control @error('categorie_id') is-invalid @enderror" required>
                        <option value="">-- Choisir une catégorie --</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ $produit->categorie_id == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="{{ route('module.produits.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
