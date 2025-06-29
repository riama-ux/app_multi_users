@extends('pages.admin.shared.layout')

@section('content')
    <h3>Stocks du magasin actif</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('module.stocks.create') }}" class="btn btn-primary mb-3">Ajouter un stock</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stocks as $stock)
                <tr>
                    <td>{{ $stock->produit ? $stock->produit->nom : 'Produit supprimé' }}</td>
                    <td>{{ $stock->quantite }}</td>
                    <td>
                        <a href="{{ route('module.stocks.edit', $stock->id) }}" class="btn btn-sm btn-info">Modifier</a>
                        <form action="{{ route('module.stocks.destroy', $stock->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce stock ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">Aucun stock trouvé pour ce magasin.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $stocks->links() }}
@endsection
