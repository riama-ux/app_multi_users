@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Liste des crédits en attente</h4>

        @include('flash-message')

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Montant restant</th>
                        <th>Échéance</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($credits as $credit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $credit->vente->client->nom ?? '—' }}</td>
                            <td class="fw-bold text-danger">{{ number_format($credit->montant_restant) }} F</td>
                            <td>{{ \Carbon\Carbon::parse($credit->date_echeance)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $credit->statut == 'payé' ? 'success' : 'warning' }}">
                                    {{ ucfirst($credit->statut) }}
                                </span>
                            </td>
                            <td>
                                @if($credit->montant_restant > 0)
                                    <a href="{{ route('module.credits.edit', $credit->id) }}" class="btn btn-sm btn-outline-primary">
                                        <em class="icon ni ni-cash"></em> Rembourser
                                    </a>
                                @else
                                    <span class="text-success">Soldé</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-danger">Aucun crédit actif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $credits->links() }}
        </div>
    </div>
</div>
@endsection
