@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Ajouter un produit</h4>

        @include('flash-message')

        <form action="{{ route('module.produits.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                {{-- Nom du produit --}}
                <div class="col-md-6">
                    <label class="form-label">Nom du produit</label>
                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                    @error('nom') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Code (ex : code-barres) --}}
                <div class="col-md-6">
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                    @error('code') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Catégorie --}}
                <div class="col-md-6">
                    <label class="form-label">Catégorie</label>
                    <select name="categorie_id" class="form-control @error('categorie_id') is-invalid @enderror" required>
                        <option value="">-- Choisir une catégorie --</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('categorie_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Prix d'achat --}}
                <div class="col-md-3">
                    <label class="form-label">Prix d'achat (CFA)</label>
                    <input type="number" name="prix_achat" class="form-control @error('prix_achat') is-invalid @enderror" value="{{ old('prix_achat') }}" required>
                    @error('prix_achat') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- cout d'achat --}}
                <div class="col-md-3">
                    <label class="form-label">Coût d'achat (CFA)</label>
                    <input type="number" name="prix_achat" class="form-control @error('cout_achat') is-invalid @enderror" value="{{ old('cout_achat') }}" required>
                    @error('cout_achat') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Prix de vente --}}
                <div class="col-md-3">
                    <label class="form-label">Prix de vente (CFA)</label>
                    <input type="number" name="prix_vente" class="form-control @error('prix_vente') is-invalid @enderror" value="{{ old('prix_vente') }}" required>
                    @error('prix_vente') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Description (facultative) --}}
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                </div>

                {{-- Magasins --}}
                <div class="col-md-12">
                    <label class="form-label">Attribuer aux magasins</label>
                    <select name="magasins[]" multiple class="form-control @error('magasins') is-invalid @enderror" required>
                        @foreach($magasins as $magasin)
                            <option value="{{ $magasin->id }}" {{ collect(old('magasins'))->contains($magasin->id) ? 'selected' : '' }}>
                                {{ $magasin->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('magasins') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Bouton --}}
                <div class="col-12 d-flex justify-content-center mt-3">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('module.produits.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
