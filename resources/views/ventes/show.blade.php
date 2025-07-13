@extends('pages.admin.shared.layout')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Détails de la Vente #{{ $vente->id }}</h3>
                </div>
                <div class="nk-block-head-content">
                    <div class="d-flex gx-3">
                        <a href="{{ route('retours_clients.create', ['vente_id' => $vente->id]) }}" class="btn btn-primary me-2">
                            <em class="icon ni ni-undo"></em>
                            <span>Enregistrer un retour</span>
                        </a>
                        <a href="{{ route('ventes.edit', $vente) }}" class="btn btn-outline-primary me-2">
                            <em class="icon ni ni-edit"></em>
                            <span>Modifier la vente</span>
                        </a>
                        <a href="{{ route('ventes.index') }}" class="btn btn-outline-secondary">
                            <em class="icon ni ni-arrow-left"></em>
                            <span>Retour à la liste</span>
                        </a>
                    </div>
                </div>
            </div>
        </div><div class="nk-block nk-block-lg">
            <div class="card card-bordered">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-user-alt me-2"></em>
                                <div>
                                    <span class="text-muted">Client :</span>
                                    <h6 class="text-dark">{{ $vente->client->nom ?? 'N/A' }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-calendar-alt me-2"></em>
                                <div>
                                    <span class="text-muted">Date :</span>
                                    <h6 class="text-dark">{{ $vente->date_vente->format('d/m/Y H:i') }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-file-check me-2"></em>
                                <div>
                                    <span class="text-muted">Statut :</span>
                                    <h6 class="text-dark">{{ ucfirst($vente->statut) }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-wallet me-2"></em>
                                <div>
                                    <span class="text-muted">Mode de paiement :</span>
                                    <h6 class="text-dark">
                                        @switch($vente->mode_paiement)
                                            @case('especes') Espèces @break
                                            @case('mobile_money') Mobile Money @break
                                            @case('virement') Virement @break
                                            @case('cheque') Chèque @break
                                            @default {{ ucfirst($vente->mode_paiement) }}
                                        @endswitch
                                    </h6>
                                </div>
                            </div>
                        </div>
                        {{-- Nouvelle ligne pour la remise globale --}}
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-wallet me-2"></em> {{-- Icône pour la remise --}}
                                <div>
                                    <span class="text-muted">Remise Globale :</span>
                                    <h6 class="text-dark">{{ number_format($vente->remise, 0, ',', ' ') }} FCFA</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-sign-kobo me-2"></em>
                                <div>
                                    <span class="text-muted">Montant Total :</span>
                                    <h6 class="text-success">{{ number_format($vente->total_ttc, 0, ',', ' ') }} FCFA</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="d-flex align-items-center">
                                <em class="icon ni ni-sign-kobo me-2"></em>
                                <div>
                                    <span class="text-muted">Reste à payer :</span>
                                    <h6 class="text-danger">{{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h5 class="nk-block-title">Produits vendus</h5>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Total</th>
                                    <th>Lot ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vente->ligneVentes as $ligne)
                                    <tr>
                                        <td>{{ $ligne->produit->nom ?? 'N/A' }}</td>
                                        <td>{{ $ligne->quantite }}</td>
                                        <td>{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($ligne->prix_total, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $ligne->lot_id }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <em class="icon ni ni-info"></em> Aucune ligne de vente enregistrée.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><div class="nk-block nk-block-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h5 class="nk-block-title">Historique des paiements</h5>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                    <th>Date</th>
                                    <th>Encaisseur</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vente->paiements as $paiement)
                                    <tr @if($paiement->annule) class="table-secondary text-muted" @endif>
                                        <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</td>
                                        <td>{{ $paiement->date_paiement->format('d/m/Y H:i') }}</td>
                                        <td>{{ $paiement->user->name ?? 'N/A' }}</td>
                                        <td>
                                            @if(!$paiement->annule)
                                                <form action="{{ route('paiements.annuler', $paiement) }}" method="POST" onsubmit="return confirm('Confirmer l\'annulation de ce paiement ?');" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                                                </form>
                                            @else
                                                <span class="badge bg-danger">Annulé</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <em class="icon ni ni-info"></em> Aucun paiement enregistré.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>@if($vente->reste_a_payer > 0)
        <div class="nk-block">
            <div class="card card-bordered">
                <div class="card-inner">
                    <h5 class="mb-4">Ajouter un paiement</h5>
                    <form action="{{ route('paiements.store', $vente) }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="montant" class="form-label">Montant (Reste: {{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA)</label>
                                <input type="number" name="montant" id="montant" class="form-control" max="{{ $vente->reste_a_payer }}" min="1" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mode_paiement" class="form-label">Mode de paiement</label>
                                <select name="mode_paiement" class="form-select" required>
                                    <option value="especes">Espèces</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="virement">Virement</option>
                                    <option value="cheque">Chèque</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success mt-4 w-100">
                                    <em class="icon ni ni-coin"></em> Valider le paiement
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>@endif
        
    </div>
</div>
@endsection