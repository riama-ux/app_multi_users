@extends('pages.admin.shared.layout')

@section('content')
    <h1>Liste des produits</h1>

    <a href="{{ route('produits.create') }}" class="btn btn-primary mb-3">Ajouter un produit</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Référence</th>
                <th>Catégorie</th>
                <th>Coût achat</th>
                <th>Prix vente</th>
                <th>Actions</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produits as $produit)
                <tr @if($produit->trashed()) class="table-danger" @endif>
                    <td>{{ $produit->nom }}</td>
                    <td>{{ $produit->reference }}</td>
                    <td>{{ $produit->categorie->nom ?? 'N/A' }}</td>
                    <td>{{ number_format($produit->cout_achat, 2) }}</td>
                    <td>{{ number_format($produit->prix_vente, 2) }}</td>
                    <td>
                        @if(!$produit->trashed())
                            <a href="{{ route('produits.show', $produit) }}" class="btn btn-info btn-sm">Voir</a>
                            <a href="{{ route('produits.edit', $produit) }}" class="btn btn-warning btn-sm">Modifier</a>
                            <form action="{{ route('produits.destroy', $produit) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Confirmer la suppression ?')" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        @else
                            <form action="{{ route('produits.restore', $produit->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button class="btn btn-success btn-sm">Restaurer</button>
                            </form>
                            <form action="{{ route('produits.forceDelete', $produit->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Supprimer définitivement ?')" class="btn btn-danger btn-sm">Supprimer définitivement</button>
                            </form>
                        @endif
                    </td>
                    <td>
                        @if($produit->trashed())
                            Supprimé
                        @else
                            Actif
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">Aucun produit trouvé.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $produits->links() }}
@endsection
