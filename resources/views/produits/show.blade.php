@extends('pages.admin.shared.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails du Produit : {{ $produit->nom }}</h1>
        <div>
            <a href="{{ route('produits.edit', $produit->id) }}" class="btn btn-warning me-2">Modifier</a>
            <a href="{{ route('produits.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Informations Générales
                </div>
                <div class="card-body">
                    <p><strong>Référence :</strong> {{ $produit->reference ?? '-' }}</p>
                    <p><strong>Code :</strong> {{ $produit->code ?? '-' }}</p>
                    <p><strong>Catégorie :</strong> {{ $produit->categorie->nom ?? '-' }}</p>
                    <p><strong>Magasin :</strong> {{ $produit->magasin->nom ?? '-' }}</p>
                    <p><strong>Marque :</strong> {{ $produit->marque }}</p>
                    <p><strong>Unité :</strong> {{ $produit->unite }}</p>
                    <p><strong>Description :</strong> {{ $produit->description }}</p>
                    <p><strong>Seuil d'alerte :</strong> {{ $produit->seuil_alerte }}</p>
                    <p><strong>Quantité totale actuelle :</strong> <span class="badge bg-info">{{ $produit->quantite }} {{ $produit->unite }}</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    Informations Financières
                </div>
                <div class="card-body">
                    <p><strong>Coût d'achat moyen :</strong> {{ number_format($produit->cout_achat, 2) }} {{ config('app.currency') }}</p>
                    <p><strong>Prix de vente :</strong> {{ number_format($produit->prix_vente, 2) }} {{ config('app.currency') }}</p>
                    <p><strong>Marge :</strong> {{ number_format($produit->marge, 2) }} %</p>
                </div>
            </div>
        </div>
    </div>

    ---

    <h2 class="mb-3">Lots de Stock pour ce Produit</h2>

    @if ($produit->stockLots->isEmpty())
        <div class="alert alert-info" role="alert">
            Aucun lot de stock enregistré pour ce produit.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID Lot</th>
                        <th>Quantité Initiale</th>
                        <th>Quantité Restante</th>
                        <th>Coût d'Achat Unitaire</th>
                        <th>Date de Réception</th>
                        <th>Actions</th> {{-- Pour des actions futures sur le lot --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produit->stockLots as $lot)
                        <tr>
                            <td>{{ $lot->id }}</td>
                            <td>{{ $lot->quantite }} {{ $produit->unite }}</td>
                            <td>
                                @if ($lot->quantite_restante <= $produit->seuil_alerte && $lot->quantite_restante > 0)
                                    <span class="badge bg-warning">{{ $lot->quantite_restante }} {{ $produit->unite }} (Bas)</span>
                                @elseif ($lot->quantite_restante <= 0)
                                    <span class="badge bg-danger">{{ $lot->quantite_restante }} {{ $produit->unite }} (Épuisé)</span>
                                @else
                                    <span class="badge bg-primary">{{ $lot->quantite_restante }} {{ $produit->unite }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($lot->cout_achat, 2) }} {{ config('app.currency') }}</td>
                            <td>{{ $lot->date_reception->format('d/m/Y H:i') }}</td>
                            <td>
                                {{-- Vous pouvez ajouter ici des boutons d'action si besoin (ex: voir détails du lot, ajuster) --}}
                                <a href="#" class="btn btn-sm btn-outline-info" title="Voir Lot">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

@push('scripts')
{{-- Si vous avez des scripts JS spécifiques pour cette page --}}
@endpush