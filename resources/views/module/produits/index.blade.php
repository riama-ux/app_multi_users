@extends('pages.admin.shared.layout')

@section('content')
    <h3>Produits du magasin actif</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('module.produits.create') }}" class="btn btn-primary mb-3">Ajouter un produit</a>
    <a href="{{ request()->has('deleted') ? route('module.produits.index') : route('module.produits.index', ['deleted' => 1]) }}"
        class="btn btn-secondary mb-3">
        {{ request()->has('deleted') ? 'Voir les produits actifs' : 'Voir les produits supprimés' }}
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Code</th>
                <th>Prix achat</th>
                <th>Coût d’achat</th>
                <th>Prix vente</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produits as $produit)
                <tr>
                    <td>{{ $produit->nom ?? 'Produit supprimé' }}</td>
                    <td>{{ $produit->categorie->nom ?? 'Catégorie supprimée' }}</td>
                    <td><span class="badge bg-secondary">{{ $produit->code }}</span></td>
                    <td>{{ number_format($produit->prix_achat) }} FCFA</td>
                    <td>{{ number_format($produit->cout_achat) }} FCFA</td>
                    <td>{{ number_format($produit->prix_vente) }} FCFA</td>
                    <td>
                        @if($produit->trashed())
                            <form action="{{ route('module.produits.restore', $produit->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Restaurer</button>
                            </form>
                        @else
                            <a href="{{ route('module.produits.edit', $produit) }}" class="btn btn-sm btn-info">Modifier</a>
                            <form action="{{ route('module.produits.destroy', $produit) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce produit ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Supprimer</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Aucun produit trouvé.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $produits->links() }}
@endsection
