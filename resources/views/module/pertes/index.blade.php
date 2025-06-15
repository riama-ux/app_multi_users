@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Historique des pertes</h4>
            <a href="{{ route('module.pertes.create') }}" class="btn btn-primary">
                <em class="icon ni ni-plus"></em> Nouvelle perte
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
                        <th>Quantité perdue</th>
                        <th>Motif</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pertes as $perte)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $perte->produit->nom ?? '—' }}</td>
                            <td>{{ $perte->magasin->nom ?? '—' }}</td>
                            <td class="text-danger fw-bold">{{ $perte->quantite }}</td>
                            <td>{{ $perte->motif ?? '—' }}</td>
                            <td>{{ $perte->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <form action="{{ route('module.pertes.destroy', $perte->id) }}" method="POST" onsubmit="return confirm('Supprimer cette perte ?')">
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
                            <td colspan="7" class="text-center text-danger">Aucune perte enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $pertes->links() }}
        </div>
    </div>
</div>
@endsection
