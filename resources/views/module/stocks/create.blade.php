@extends('pages.admin.shared.layout')

@section('content')
    <h3>Ajouter un stock</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('module.stocks.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="produit_id">Produit</label>
            <select name="produit_id" class="form-select" required>
                <option value="">-- Choisir --</option>
                @foreach ($produits as $produit)
                    <option value="{{ $produit->id }}" {{ old('produit_id') == $produit->id ? 'selected' : '' }}>
                        {{ $produit->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantite">Quantité</label>
            <input type="number" name="quantite" value="{{ old('quantite', 0) }}" class="form-control" required>
        </div>

        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('module.stocks.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection
