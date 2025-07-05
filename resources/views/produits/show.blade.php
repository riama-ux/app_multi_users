@extends('pages.admin.shared.layout')

@section('content')
    <h1>Détails du produit</h1>

    <table class="table table-bordered">
        <tr>
            <th>Nom</th>
            <td>{{ $produit->nom }}</td>
        </tr>
        <tr>
            <th>Référence</th>
            <td>{{ $produit->reference }}</td>
        </tr>
        <tr>
            <th>Catégorie</th>
            <td>{{ $produit->categorie->nom ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Coût d'achat par défaut</th>
            <td>{{ number_format($produit->cout_achat, 2) }}</td>
        </tr>
        <tr>
            <th>Prix de vente par défaut</th>
            <td>{{ number_format($produit->prix_vente, 2) }}</td>
        </tr>
        <tr>
            <th>Marge (%)</th>
            <td>{{ $produit->marge }}</td>
        </tr>
        <tr>
            <th>Seuil d'alerte (quantité)</th>
            <td>{{ $produit->seuil_alerte ?? 'Non défini' }}</td>
        </tr>
        <tr>
            <th>Statut</th>
            <td>{{ $produit->trashed() ? 'Supprimé' : 'Actif' }}</td>
        </tr>
    </table>

    <a href="{{ route('produits.index') }}" class="btn btn-secondary">Retour à la liste</a>
    @if(!$produit->trashed())
        <a href="{{ route('produits.edit', $produit) }}" class="btn btn-warning">Modifier</a>
    @endif
@endsection
