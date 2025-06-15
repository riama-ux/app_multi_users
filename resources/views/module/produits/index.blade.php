@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Liste des produits</h4>
            @if(auth()->user()->role !== 'Manager')
                <a href="{{ route('module.produits.create') }}" class="btn btn-primary">
                    <em class="icon ni ni-plus"></em> Nouveau produit
                </a>
            @endif
        </div>

        @include('flash-message')

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Code</th>
                        <th>Prix achat</th>
                        <th>Coût achat</th>
                        <th>Prix vente</th>
                        @if(auth()->user()->role !== 'Manager')
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produits as $produit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $produit->nom }}</td>
                            <td>{{ $produit->categorie->nom ?? 'N/A' }}</td>
                            <td>{{ $produit->code }}</td>
                            <td>{{ number_format($produit->prix_achat) }} F</td>
                            <td>{{ number_format($produit->cout_achat) }} F</td>
                            <td>{{ number_format($produit->prix_vente) }} F</td>
                            @if(auth()->user()->role !== 'Manager')
                                <td>
                                    <a href="{{ route('module.produits.edit', $produit->id) }}" class="btn btn-sm btn-outline-info">
                                        <em class="icon ni ni-edit-alt"></em>
                                    </a>
                                    <form action="{{ route('module.produits.destroy', $produit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <em class="icon ni ni-trash"></em>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'Manager' ? 7 : 8 }}" class="text-center text-danger">Aucun produit trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $produits->links() }}
        </div>
    </div>
</div>
@endsection
