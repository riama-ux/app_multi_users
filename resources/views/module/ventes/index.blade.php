@extends('pages.admin.shared.layout')

@section('content')
<h3>Ventes du magasin actif</h3>

@include('flash-message')

<a href="{{ route('module.ventes.create') }}" class="btn btn-primary mb-3">Nouvelle vente</a>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Date</th>
            <th>Produits</th>
            <th>Remise</th>
            <th>Total</th>
            <th>Paiement</th>
            <th>Client</th>
            <th>Vendeur</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($ventes as $vente)
            <tr>
                <td>{{ $vente->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    {{ $vente->lignes->count() }} produit{{ $vente->lignes->count() > 1 ? 's' : '' }} <br>
                    <a href="{{ route('module.ventes.show', $vente) }}" class="btn btn-sm btn-outline-secondary mt-1">Voir détails</a>
                </td>
                <td>{{ number_format($vente->remise) }} FCFA</td>
                <td><strong>{{ number_format($vente->total) }} FCFA</strong></td>
                <td>{{ ucfirst($vente->mode_paiement) }}</td>
                <td>{{ $vente->client?->nom ?? '-' }}</td>
                <td>{{ $vente->user->name }}</td>
                <td>
                    <a href="{{ route('module.ventes.edit', $vente) }}" class="btn btn-sm btn-info">Modifier</a>
                    <a href="{{ route('module.ventes.recu', $vente) }}" target="_blank" class="btn btn-sm btn-warning">Imprimer reçu</a>
                    <form action="{{ route('module.ventes.destroy', $vente) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette vente ?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8">Aucune vente enregistrée pour ce magasin.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $ventes->links() }}

@endsection
