@extends('pages.admin.shared.layout')


@section('content')
    <h3>Produits du magasin actif</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('module.produits.create') }}" class="btn btn-primary mb-3">Ajouter un produit</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Code</th>
                <th>Prix achat</th>
                <th>Prix vente</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produits as $produit)
                <tr>
                    <td>{{ $produit->nom }}</td>
                    <td>{{ $produit->categorie->nom ?? '-' }}</td>
                    <td>{{ $produit->code }}</td>
                    <td>{{ number_format($produit->prix_achat) }} FCFA</td>
                    <td>{{ number_format($produit->prix_vente) }} FCFA</td>
                    <td>
                        <a href="{{ route('module.produits.edit', $produit) }}" class="btn btn-sm btn-info">Modifier</a>
                        <form action="{{ route('module.produits.destroy', $produit) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce produit ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Aucun produit trouvé.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $produits->links() }}
@endsection

