@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Modifier la quantité en stock</h4>

        @include('flash-message')

        <form action="{{ route('module.stocks.update', $stock->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Produit</label>
                <input type="text" class="form-control" value="{{ $stock->produit->nom }}" disabled>
            </div>

            <div class="form-group">
                <label class="form-label">Magasin</label>
                <input type="text" class="form-control" value="{{ $stock->magasin->nom }}" disabled>
            </div>

            <div class="form-group">
                <label class="form-label">Quantité actuelle</label>
                <input type="number" name="quantite" class="form-control @error('quantite') is-invalid @enderror"
                       value="{{ old('quantite', $stock->quantite) }}" required min="0">
                @error('quantite') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="{{ route('module.stocks.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
