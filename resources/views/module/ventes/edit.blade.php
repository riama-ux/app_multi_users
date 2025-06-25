@extends('pages.admin.shared.layout')

@section('content')
<h3>Modifier une vente</h3>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form action="{{ route('module.ventes.update', $vente->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="produit_id">Produit</label>
        <select name="produit_id" class="form-select" required>
            @foreach ($produits as $produit)
                <option value="{{ $produit->id }}" {{ $vente->produit_id == $produit->id ? 'selected' : '' }}>
                    {{ $produit->nom }} ({{ number_format($produit->prix_vente) }} FCFA)
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="client_id">Client</label>
        <select name="client_id" class="form-select">
            <option value="">-- Aucun --</option>
            @foreach ($clients as $client)
                <option value="{{ $client->id }}" {{ $vente->client_id == $client->id ? 'selected' : '' }}>
                    {{ $client->nom }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="quantite">Quantité</label>
        <input type="number" name="quantite" class="form-control" value="{{ old('quantite', $vente->quantite) }}" required min="1">
    </div>

    <div class="mb-3">
        <label for="remise">Remise (FCFA)</label>
        <input type="number" name="remise" class="form-control" value="{{ old('remise', $vente->remise) }}">
    </div>

    <div class="mb-3">
        <label for="mode_paiement">Mode de paiement</label>
        <select name="mode_paiement" class="form-select" required>
            <option value="cash" {{ $vente->mode_paiement === 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="credit" {{ $vente->mode_paiement === 'credit' ? 'selected' : '' }}>Crédit</option>
        </select>
    </div>

    <button class="btn btn-primary">Modifier</button>
    <a href="{{ route('module.ventes.index') }}" class="btn btn-secondary">Annuler</a>
</form>
@endsection