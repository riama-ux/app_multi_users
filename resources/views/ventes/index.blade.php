@extends('pages.admin.shared.layout')

@section('content')

<div class="nk-block nk-block-lg">
    
    <div class="nk-block-head-content d-flex justify-content-between align-items-center mb-4">
        <h1 class="nk-block-title">Liste des ventes</h1>
        <a href="{{ route('ventes.create') }}" class="btn btn-primary">
            <em class="icon ni ni-plus"></em><span>Ajouter une vente</span>
        </a>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <div class="mb-4">
                <form method="GET" action="{{ route('ventes.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="statut-filter" class="form-label">Statut</label>
                            <select name="statut" id="statut-filter" class="form-select form-control" onchange="this.form.submit()">
                                <option value="">-- Tous les statuts --</option>
                                <option value="payee" {{ request('statut') == 'payee' ? 'selected' : '' }}>Payée</option>
                                <option value="partielle" {{ request('statut') == 'partielle' ? 'selected' : '' }}>Partielle</option>
                                <option value="credit" {{ request('statut') == 'credit' ? 'selected' : '' }}>Crédit</option>
                                <option value="retournee" {{ request('statut') == 'retournee' ? 'selected' : '' }}>Retournée</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <table class="nk-tb-list nk-tb-ulgy" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col"><span>ID</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Client</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Date</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Total TTC</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Montant payé</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Reste à payer</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Statut</span></th>
                        <th class="nk-tb-col nk-tb-col-tools text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventes as $vente)
                    <tr class="nk-tb-item">
                        <td class="nk-tb-col">{{ $vente->id }}</td>
                        <td class="nk-tb-col tb-col-md">{{ $vente->client->nom ?? 'N/A' }}</td>
                        <td class="nk-tb-col tb-col-md">{{ $vente->date_vente->format('d/m/Y H:i') }}</td>
                        <td class="nk-tb-col tb-col-md">{{ number_format($vente->total_ttc, 2, ',', ' ') }} FCFA</td>
                        <td class="nk-tb-col tb-col-md">{{ number_format($vente->montant_paye, 2, ',', ' ') }} FCFA</td>
                        <td class="nk-tb-col tb-col-md">{{ number_format($vente->reste_a_payer, 2, ',', ' ') }} FCFA</td>
                        <td class="nk-tb-col tb-col-md">
                            @if($vente->statut === 'payee')
                                <span class="badge badge-dim bg-success">Payée</span>
                            @elseif($vente->statut === 'partielle')
                                <span class="badge badge-dim bg-warning">Partielle</span>
                            @elseif($vente->statut === 'credit')
                                <span class="badge badge-dim bg-danger">Crédit</span>
                            @elseif($vente->statut === 'retournee')
                                <span class="badge badge-dim bg-info">Retournée</span>
                            @else
                                <span class="badge badge-dim bg-secondary">{{ ucfirst($vente->statut) }}</span>
                            @endif
                        </td>
                        <td class="nk-tb-col nk-tb-col-tools">
                            <ul class="nk-tb-actions gx-1">
                                <li>
                                    <a href="{{ route('ventes.show', $vente) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir les détails">
                                        <em class="icon ni ni-eye"></em>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('ventes.edit', $vente) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier la vente">
                                        <em class="icon ni ni-edit-alt"></em>
                                    </a>
                                </li>
                                {{-- La suppression directe est généralement évitée pour les ventes. --}}
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div></div><div class="card-inner-sm">
        <div class="d-flex justify-content-center">
            {{ $ventes->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>@endsection