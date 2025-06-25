@extends('pages.admin.shared.layout')

@section('content')
<div class="container">
    <h1>Ajouter une perte</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('module.pertes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="produit_id" class="form-label">Produit</label>
            <select name="produit_id" id="produit_id" class="form-control" required>
                <option value="">-- Choisir un produit --</option>
                @foreach($produits as $produit)
                    <option value="{{ $produit->id }}" {{ old('produit_id') == $produit->id ? 'selected' : '' }}>
                        {{ $produit->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantite" class="form-label">Quantité</label>
            <input type="number" name="quantite" id="quantite" min="1" class="form-control" value="{{ old('quantite') }}" required>
        </div>

        <div class="mb-3">
            <label for="motif" class="form-label">Motif (optionnel)</label>
            <input type="text" name="motif" id="motif" class="form-control" value="{{ old('motif') }}">
        </div>

        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('module.pertes.index') }}" class="btn btn-secondary">Retour</a>
    </form>
</div>
@endsection
