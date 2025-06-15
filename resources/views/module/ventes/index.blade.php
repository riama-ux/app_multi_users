@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Historique des ventes</h4>
            <a href="{{ route('module.ventes.create') }}" class="btn btn-primary">
                <em class="icon ni ni-plus"></em> Nouvelle vente
            </a>
        </div>

        @include('flash-message')

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Produit</th>
                        <th>Client</th>
                        <th>Magasin</th>
                        <th>Qté</th>
                        <th>PU</th>
                        <th>Total</th>
                        <th>Paiement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventes as $vente)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $vente->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $vente->produit->nom ?? '—' }}</td>
                            <td>{{ $vente->client->nom ?? '—' }}</td>
                            <td>{{ $vente->magasin->nom ?? '—' }}</td>
                            <td>{{ $vente->quantite }}</td>
                            <td>{{ number_format($vente->prix_unitaire) }} F</td>
                            <td class="text-success fw-bold">{{ number_format($vente->total) }} F</td>
                            <td>
                                <span class="badge bg-{{ $vente->mode_paiement == 'espece' ? 'success' : 'warning' }}">
                                    {{ ucfirst($vente->mode_paiement) }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('module.ventes.destroy', $vente->id) }}" method="POST"
                                      onsubmit="return confirm('Confirmer la suppression de cette vente ?')" class="d-inline">
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
                            <td colspan="10" class="text-center text-danger">Aucune vente enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $ventes->links() }}
        </div>
    </div>
</div>
@endsection
