@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Modifier le statut de la commande</h4>

        @include('flash-message')

        <form action="{{ route('module.commandes.update', $commande->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Produit --}}
            <div class="form-group">
                <label class="form-label">Produit</label>
                <input type="text" class="form-control" value="{{ $commande->produit->nom ?? '-' }}" disabled>
            </div>

            {{-- Magasin --}}
            <div class="form-group">
                <label class="form-label">Magasin</label>
                <input type="text" class="form-control" value="{{ $commande->magasin->nom ?? '-' }}" disabled>
            </div>

            {{-- Quantité --}}
            <div class="form-group">
                <label class="form-label">Quantité</label>
                <input type="number" class="form-control" value="{{ $commande->quantite }}" disabled>
            </div>

            {{-- Statut --}}
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-control @error('statut') is-invalid @enderror" required>
                    <option value="en attente" {{ $commande->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                    <option value="validée" {{ $commande->statut == 'validée' ? 'selected' : '' }}>Validée</option>
                    <option value="refusée" {{ $commande->statut == 'refusée' ? 'selected' : '' }}>Refusée</option>
                </select>
                @error('statut') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="{{ route('module.commandes.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
