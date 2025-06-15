@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Liste des commandes</h4>
            <a href="{{ route('module.commandes.create') }}" class="btn btn-primary">
                <em class="icon ni ni-plus"></em> Nouvelle commande
            </a>
        </div>

        @include('flash-message')

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Produit</th>
                        <th>Magasin</th>
                        <th>Quantité</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($commandes as $commande)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $commande->produit->nom ?? '—' }}</td>
                            <td>{{ $commande->magasin->nom ?? '—' }}</td>
                            <td>{{ $commande->quantite }}</td>
                            <td>{{ $commande->created_at->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $commande->statut === 'validée' ? 'success' : (
                                        $commande->statut === 'refusée' ? 'danger' : 'warning'
                                    ) }}">
                                    {{ ucfirst($commande->statut) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('module.commandes.edit', $commande->id) }}" class="btn btn-sm btn-outline-primary">
                                    <em class="icon ni ni-edit"></em>
                                </a>
                                <form action="{{ route('module.commandes.destroy', $commande->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette commande ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <em class="icon ni ni-trash"></em>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-danger">Aucune commande enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $commandes->links() }}
        </div>
    </div>
</div>
@endsection
