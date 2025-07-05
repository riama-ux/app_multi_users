@extends('pages.admin.shared.layout')

@section('content')
    <h1>Lots de stock pour : {{ $produit->nom }}</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Lot ID</th>
                <th>Quantité</th>
                <th>Coût unitaire</th>
                <th>Date de réception</th>
                <th>Créé le</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lots as $lot)
                <tr>
                    <td>#{{ $lot->id }}</td>
                    <td>{{ $lot->quantite }}</td>
                    <td>{{ number_format($lot->cout_achat, 0, ',', ' ') }} F</td>
                    <td>{{ \Carbon\Carbon::parse($lot->date_reception)->format('d/m/Y') }}</td>
                    <td>{{ $lot->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Aucun lot trouvé pour ce produit</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('stocks.index') }}" class="btn btn-secondary">← Retour au stock</a>
@endsection
