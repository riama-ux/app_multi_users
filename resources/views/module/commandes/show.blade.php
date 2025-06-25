@extends('pages.admin.shared.layout')

@section('content')
    <h3>Détails de la commande</h3>

    <p><strong>Fournisseur :</strong> {{ $commande->fournisseur->nom ?? '-' }}</p>
    <p><strong>Date :</strong> {{ $commande->date_commande }}</p>
    <p><strong>Statut :</strong> {{ ucfirst($commande->statut) }}</p>

    @if($commande->statut === 'en attente')
        <form action="{{ route('module.commandes.recevoir', $commande->id) }}" method="POST" style="display:inline-block">
            @csrf
            <button type="submit" class="btn btn-success" onclick="return confirm('Confirmer la réception de cette commande ?')">
                Recevoir la commande
            </button>
        </form>
    @endif


    <h5>Produits commandés</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($commande->lignes as $ligne)
                <tr>
                    <td>{{ $ligne->produit->nom ?? '—' }}</td>
                    <td>{{ $ligne->quantite }}</td>
                    <td>{{ number_format($ligne->prix_unitaire) }} FCFA</td>
                    <td>{{ number_format($ligne->prix_unitaire * $ligne->quantite) }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
