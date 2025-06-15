@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Effectuer un transfert de stock</h4>

        @include('flash-message')

        <form action="{{ route('module.transferts.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                {{-- Produit --}}
                <div class="col-md-6">
                    <label class="form-label">Produit à transférer</label>
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

                {{-- Magasin source --}}
                <div class="col-md-6">
                    <label class="form-label">Magasin source</label>
                    <select name="source_id" class="form-control @error('source_id') is-invalid @enderror" required>
                        <option value="">-- Sélectionner --</option>
                        @foreach($magasins as $magasin)
                            <option value="{{ $magasin->id }}" {{ old('source_id') == $magasin->id ? 'selected' : '' }}>
                                {{ $magasin->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('source_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Magasin destination --}}
                <div class="col-md-6">
                    <label class="form-label">Magasin de destination</label>
                    <select name="destination_id" class="form-control @error('destination_id') is-invalid @enderror" required>
                        <option value="">-- Sélectionner --</option>
                        @foreach($magasins as $magasin)
                            <option value="{{ $magasin->id }}" {{ old('destination_id') == $magasin->id ? 'selected' : '' }}>
                                {{ $magasin->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('destination_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Quantité --}}
                <div class="col-md-6">
                    <label class="form-label">Quantité à transférer</label>
                    <input type="number" name="quantite" class="form-control @error('quantite') is-invalid @enderror"
                           value="{{ old('quantite') }}" required min="1">
                    @error('quantite') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Commentaire --}}
                <div class="col-md-12">
                    <label class="form-label">Commentaire (facultatif)</label>
                    <input type="text" name="commentaire" class="form-control @error('commentaire') is-invalid @enderror"
                           value="{{ old('commentaire') }}" placeholder="Ex : réapprovisionnement, transfert urgent...">
                    @error('commentaire') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary">Valider le transfert</button>
                <a href="{{ route('module.transferts.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
