@extends('pages.admin.shared.layout')

@section('content')
    <h3>Nouvelle vente</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('module.ventes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="produit_id">Produit</label>
            <select name="produit_id" class="form-select" required>
                <option value="">-- Choisir un produit --</option>
                @foreach ($produits as $produit)
                    <option value="{{ $produit->id }}" {{ old('produit_id') == $produit->id ? 'selected' : '' }}>
                        {{ $produit->nom }} ({{ number_format($produit->prix_vente) }} FCFA)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="client_id">Client (optionnel)</label>
            <select name="client_id" class="form-select">
                <option value="">-- Aucun --</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantite">Quantité</label>
            <input type="number" name="quantite" class="form-control" min="1" value="{{ old('quantite') }}" required>
        </div>

        <div class="mb-3">
            <label for="remise">Remise (FCFA)</label>
            <input type="number" name="remise" class="form-control" value="{{ old('remise', 0) }}">
        </div>

        <div class="mb-3">
            <label for="mode_paiement">Mode de paiement</label>
            <select name="mode_paiement" class="form-select" required>
                <option value="cash" {{ old('mode_paiement') == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="credit" {{ old('mode_paiement') == 'credit' ? 'selected' : '' }}>Crédit</option>
            </select>
        </div>

        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('module.ventes.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection

