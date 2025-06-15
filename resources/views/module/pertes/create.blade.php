@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Déclarer une perte de stock</h4>

        @include('flash-message')

        <form action="{{ route('module.pertes.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                {{-- Magasin --}}
                <div class="col-md-6">
                    <label class="form-label">Magasin concerné</label>
                    <select name="magasin_id" class="form-control @error('magasin_id') is-invalid @enderror" required>
                        <option value="">-- Choisir un magasin --</option>
                        @foreach($magasins as $magasin)
                            <option value="{{ $magasin->id }}" {{ old('magasin_id') == $magasin->id ? 'selected' : '' }}>
                                {{ $magasin->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('magasin_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Produit --}}
                <div class="col-md-6">
                    <label class="form-label">Produit concerné</label>
                    <select name="produit_id" class="form-control @error('produit_id') is-invalid @enderror" required>
                        <option value="">-- Choisir un produit --</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id }}" {{ old('produit_id') == $produit->id ? 'selected' : '' }}>
                                {{ $produit->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('produit_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Quantité --}}
                <div class="col-md-6">
                    <label class="form-label">Quantité perdue</label>
                    <input type="number" name="quantite" class="form-control @error('quantite') is-invalid @enderror"
                           value="{{ old('quantite') }}" required min="1">
                    @error('quantite') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Motif --}}
                <div class="col-md-6">
                    <label class="form-label">Motif (facultatif)</label>
                    <input type="text" name="motif" class="form-control @error('motif') is-invalid @enderror"
                           value="{{ old('motif') }}" placeholder="ex: produit expiré, vol, erreur...">
                    @error('motif') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary">Enregistrer la perte</button>
                <a href="{{ route('module.pertes.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
