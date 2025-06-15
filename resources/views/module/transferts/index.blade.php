@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Historique des transferts</h4>
            <a href="{{ route('module.transferts.create') }}" class="btn btn-primary">
                <em class="icon ni ni-plus"></em> Nouveau transfert
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
                        <th>De</th>
                        <th>Vers</th>
                        <th>Quantité</th>
                        <th>Commentaire</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transferts as $transfert)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transfert->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $transfert->produit->nom ?? '—' }}</td>
                            <td>{{ $transfert->source->nom ?? '—' }}</td>
                            <td>{{ $transfert->destination->nom ?? '—' }}</td>
                            <td class="fw-bold text-info">{{ $transfert->quantite }}</td>
                            <td>{{ $transfert->commentaire ?? '—' }}</td>
                            <td>
                                <form action="{{ route('admin.transferts.destroy', $transfert->id) }}" method="POST"
                                      onsubmit="return confirm('Supprimer ce transfert ?')">
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
                            <td colspan="8" class="text-center text-danger">Aucun transfert enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $transferts->links() }}
        </div>
    </div>
</div>
@endsection
