@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Liste des ventes</h1>
        <a href="{{ route('ventes.create') }}" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Ajouter une vente</span>
        </a>
    </div>

    

    {{-- Filtre de statut --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrer les ventes</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('ventes.index') }}" class="row g-2">
                <div class="col-md-4">
                    <label for="statut-filter" class="form-label">Statut</label>
                    <select name="statut" id="statut-filter" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Tous les statuts --</option>
                        <option value="payee" {{ request('statut') == 'payee' ? 'selected' : '' }}>Payée</option>
                        <option value="partielle" {{ request('statut') == 'partielle' ? 'selected' : '' }}>Partielle</option>
                        <option value="credit" {{ request('statut') == 'credit' ? 'selected' : '' }}>Crédit</option>
                        <option value="retournee" {{ request('statut') == 'retournee' ? 'selected' : '' }}>Retournée</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau des ventes --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Détails des ventes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Total TTC</th>
                            <th>Montant payé</th>
                            <th>Reste à payer</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventes as $vente)
                        <tr>
                            <td>{{ $vente->id }}</td>
                            <td>{{ $vente->client->nom ?? 'N/A' }}</td>
                            <td>{{ $vente->date_vente->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($vente->total_ttc, 2, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($vente->montant_paye, 2, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($vente->reste_a_payer, 2, ',', ' ') }} FCFA</td>
                            <td>
                                @if($vente->statut === 'payee')
                                    <span class="badge bg-success">Payée</span>
                                @elseif($vente->statut === 'partielle')
                                    <span class="badge bg-warning text-dark">Partielle</span>
                                @elseif($vente->statut === 'credit')
                                    <span class="badge bg-danger">Crédit</span>
                                @elseif($vente->statut === 'retournee')
                                    <span class="badge bg-info">Retournée</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($vente->statut) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ventes.show', $vente) }}" class="btn btn-sm btn-primary" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('ventes.edit', $vente) }}" class="btn btn-sm btn-warning" title="Modifier la vente">
                                    <i class="fas fa-edit"></i>
                                </a>
                                {{-- Les opérations de suppression directes de ventes sont généralement évitées. --}}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="d-flex justify-content-center">
                {{ $ventes->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection