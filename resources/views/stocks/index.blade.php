@extends('pages.admin.shared.layout')

@section('content')
    <h1>Stock actuel</h1>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Seuil d’alerte</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stocks as $stock)
                <tr>
                    <td>{{ $stock->produit->nom }}</td>
                    <td>{{ $stock->quantite }}</td>
                    <td>{{ $stock->produit->seuil_alerte }}</td>
                    <td>
                        @if($stock->quantite <= $stock->produit->seuil_alerte)
                            <span class="badge bg-danger">Stock faible</span>
                        @else
                            <span class="badge bg-success">OK</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('stock_lots.index', $stock->produit_id) }}" class="btn btn-sm btn-secondary">
                            Voir lots
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Aucun produit trouvé</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
