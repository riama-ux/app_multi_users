@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Enregistrer une vente</h4>

        @include('flash-message')

        <form action="{{ route('module.ventes.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                {{-- Magasin --}}
                <div class="col-md-6">
                    <label class="form-label">Magasin</label>
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
                    <label class="form-label">Produit</label>
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

                {{-- Client --}}
                <div class="col-md-6">
                    <label class="form-label">Client</label>
                    <select name="client_id" class="form-control @error('client_id') is-invalid @enderror" required>
                        <option value="">-- Choisir un client --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Quantité --}}
                <div class="col-md-3">
                    <label class="form-label">Quantité</label>
                    <input type="number" name="quantite" class="form-control @error('quantite') is-invalid @enderror"
                           value="{{ old('quantite') }}" required min="1">
                    @error('quantite') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Prix unitaire --}}
                <div class="col-md-3">
                    <label class="form-label">Prix unitaire (F)</label>
                    <input type="number" name="prix_unitaire" class="form-control @error('prix_unitaire') is-invalid @enderror"
                           value="{{ old('prix_unitaire') }}" required min="0">
                    @error('prix_unitaire') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Mode de paiement --}}
                <div class="col-md-6">
                    <label class="form-label">Mode de paiement</label>
                    <select name="mode_paiement" class="form-control @error('mode_paiement') is-invalid @enderror" required>
                        <option value="">-- Choisir un mode --</option>
                        <option value="espece" {{ old('mode_paiement') == 'espece' ? 'selected' : '' }}>Espèce</option>
                        <option value="credit" {{ old('mode_paiement') == 'credit' ? 'selected' : '' }}>Crédit</option>
                    </select>
                    @error('mode_paiement') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary">Valider</button>
                <a href="{{ route('module.ventes.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
