@extends('pages.admin.shared.layout')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Détails du Transfert #{{ $transfert->id }}</h3>
                </div>
                <div class="nk-block-head-content">
                    <a href="{{ route('transferts.index') }}" class="btn btn-secondary">
                        <em class="icon ni ni-arrow-left"></em>
                        <span>Retour à la liste</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="nk-block nk-block-lg">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="row gy-3">
                        <div class="col-sm-6">
                            <p class="text-muted mb-1"><strong>Magasin Source :</strong></p>
                            <h6 class="text-dark">{{ $transfert->magasinSource->nom }}</h6>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1"><strong>Magasin Destination :</strong></p>
                            <h6 class="text-dark">{{ $transfert->magasinDestination->nom }}</h6>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1"><strong>Date :</strong></p>
                            <h6 class="text-dark">{{ $transfert->date_transfert->format('d/m/Y') }}</h6>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted mb-1"><strong>Utilisateur :</strong></p>
                            <h6 class="text-dark">{{ $transfert->user->name ?? 'N/A' }}</h6>
                        </div>
                        <div class="col-12">
                            <p class="text-muted mb-1"><strong>Statut :</strong></p>
                            @php
                                $statusClass = 'secondary';
                                $statusText = 'Inconnu';
                                if ($transfert->statut === 'envoye') {
                                    $statusClass = 'success';
                                    $statusText = 'Validé';
                                } elseif ($transfert->statut === 'refuse') {
                                    $statusClass = 'danger';
                                    $statusText = 'Refusé';
                                } elseif ($transfert->statut === 'attente') {
                                    $statusClass = 'warning text-dark';
                                    $statusText = 'En attente';
                                }
                            @endphp
                            <span class="badge bg-{{ $statusClass }} rounded-pill">{{ $statusText }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div><div class="nk-block nk-block-lg">
            <h5 class="title">Produits transférés</h5>
            <div class="card card-preview">
                <div class="card-inner">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead>
                                <tr class="table-light">
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfert->ligneTransferts as $ligne)
                                <tr>
                                    <td>{{ $ligne->produit->nom }}</td>
                                    <td>{{ $ligne->quantite }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle me-2"></i> Aucun produit dans ce transfert.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>@if($transfert->statut === 'attente' && session('magasin_actif_id') == $transfert->magasin_destination_id)
            <div class="nk-block">
                <div class="d-flex justify-content-end">
                    <form action="{{ route('transferts.valider', $transfert->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Confirmer la réception du transfert ?')">
                            <em class="icon ni ni-check-circle"></em>
                            <span>Réceptionner le transfert</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection