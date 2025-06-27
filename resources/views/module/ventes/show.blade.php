@extends('pages.admin.shared.layout')

@section('content')
<h3>Détails de la vente du {{ $vente->created_at->format('d/m/Y H:i') }}</h3>

<a href="{{ route('module.ventes.index') }}" class="btn btn-secondary mb-3">← Retour</a>

<div class="card">
    <div class="card-body">
        <p><strong>Client :</strong> {{ $vente->client?->nom ?? '—' }}</p>
        <p><strong>Vendeur :</strong> {{ $vente->user->name }}</p>
        <p><strong>Mode de paiement :</strong> {{ ucfirst($vente->mode_paiement) }}</p>
        <p><strong>Remise :</strong> {{ number_format($vente->remise) }} FCFA</p>
        <p><strong>Total :</strong> <strong>{{ number_format($vente->total) }} FCFA</strong></p>

        <hr>

        <h5>Produits</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Sous-total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vente->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->produit->nom }}</td>
                    <td>{{ $ligne->quantite }}</td>
                    <td>{{ number_format($ligne->prix_unitaire) }} FCFA</td>
                    <td>{{ number_format($ligne->quantite * $ligne->prix_unitaire) }} FCFA</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
