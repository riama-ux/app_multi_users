@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark mb-0">Détails de la commande #{{ $commande->id }}</h1>
        <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary d-flex align-items-center shadow-sm">
            <i class="bi bi-arrow-left me-2"></i> Retour aux commandes
        </a>
    </div>

    <div class="card shadow-lg border-0 mb-5">
        <div class="card-body p-5">
            
            <div class="row g-5">
                
                <div class="col-lg-6">
                    <h5 class="text-primary mb-4 pb-2 border-bottom">Informations de la Commande</h5>
                    
                    <div class="mb-4">
                        <p class="mb-2"><strong class="text-muted">Fournisseur :</strong> <span class="fw-bold text-dark">{{ $commande->fournisseur->nom ?? 'N/A' }}</span></p>
                        <p class="mb-2"><strong class="text-muted">Date de commande :</strong> {{ $commande->date_commande->format('d/m/Y') }}</p>
                        <p class="mb-2"><strong class="text-muted">Date prévue de livraison :</strong> {{ $commande->date_prevue_livraison->format('d/m/Y') }}</p>
                        
                        <div class="d-flex align-items-center mt-3">
                            <strong class="text-muted me-2">Statut :</strong>
                            @php
                                $badgeClass = '';
                                switch ($commande->statut) {
                                    case 'en attente':
                                        $badgeClass = 'badge-dim bg-warning';
                                        break;
                                    case 'validee':
                                        $badgeClass = 'badge-dim bg-primary';
                                        break;
                                    case 'livree':
                                        $badgeClass = 'badge-dim bg-success';
                                        break;
                                    case 'annulee':
                                        $badgeClass = 'badge-dim bg-danger';
                                        break;
                                    default:
                                        $badgeClass = 'badge-dim bg-secondary';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($commande->statut) }}</span>
                        </div>
                    </div>

                    <h5 class="text-primary mb-3 pb-2 border-bottom">Statut de livraison</h5>
                    
                    @if ($commande->statut === 'livree')
                        <p class="mb-2"><strong class="text-muted">Date de réception :</strong> {{ $commande->date_reception->format('d/m/Y H:i') }}</p>
                        @if ($commande->is_late)
                            <p class="text-danger fw-bold"><i class="bi bi-clock-history me-2"></i> Livrée en retard de {{ $commande->days_late }} jour(s) !</p>
                        @else
                            <p class="text-success fw-bold"><i class="bi bi-check-circle me-2"></i> Livrée à temps.</p>
                        @endif
                    @else
                        <p class="mb-2"><strong class="text-muted">Date de réception :</strong> Non réceptionnée</p>
                        @if ($commande->date_prevue_livraison->isPast() && $commande->statut !== 'annulee')
                            @php
                                $currentDate = \Carbon\Carbon::now()->startOfDay();
                                $prevueDate = $commande->date_prevue_livraison->startOfDay();
                                $daysOverdue = $currentDate->diffInDays($prevueDate);
                            @endphp
                            <p class="text-danger fw-bold"><i class="bi bi-truck-flatbed me-2"></i> En retard (non réceptionnée) !</p>
                            <p class="text-danger"><i class="bi bi-exclamation-circle me-2"></i> Retard actuel : {{ $daysOverdue }} jour(s).</p>
                        @else
                            <p class="text-muted">En attente de livraison.</p>
                        @endif
                    @endif
                </div>

                <div class="col-lg-6 border-start ps-5">
                    <h5 class="text-success mb-4 pb-2 border-bottom">Détails des Coûts</h5>

                    <p class="mb-3"><strong class="text-muted">Coût transport :</strong> <span class="fw-bold text-success">{{ number_format($commande->cout_transport ?? 0, 0, ',', ' ') }} FCFA</span></p>
                    <p class="mb-3"><strong class="text-muted">Frais supplémentaires :</strong> <span class="fw-bold text-success">{{ number_format($commande->frais_suppl ?? 0, 0, ',', ' ') }} FCFA</span></p>
                    
                    <hr class="my-4">
                    
                    <h4 class="text-dark"><strong class="text-muted me-2">Coût total :</strong> <span class="text-success">{{ number_format($commande->cout_total ?? 0, 0, ',', ' ') }} FCFA</span></h4>
                </div>
            </div>

        </div>
    </div>

    <div class="card shadow-lg border-0 mb-5">
        <div class="card-header bg-white border-bottom py-4">
            <h4 class="mb-0 text-dark">Produits commandés</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="py-3 ps-4">Produit</th>
                            <th scope="col" class="py-3">Référence</th>
                            <th scope="col" class="py-3">Quantité</th>
                            <th scope="col" class="py-3">Prix unitaire</th>
                            <th scope="col" class="py-3 text-end pe-4">Sous-total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commande->lignesCommande as $ligne)
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-dark">{{ $ligne->produit->nom }}</strong>
                                    <div class="text-muted small">Code: {{ $ligne->produit->code ?? 'N/A' }}</div>
                                </td>
                                <td>{{ $ligne->produit->reference ?? 'N/A' }}</td>
                                <td>{{ $ligne->quantite }}</td>
                                <td>{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end pe-4">
                                    <span class="fw-bold">{{ number_format($ligne->quantite * $ligne->prix_unitaire, 0, ',', ' ') }} FCFA</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-start gap-3 mt-4">
        @if ($commande->statut !== 'livree')
            <button class="btn btn-primary px-4 py-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#receptionModal">
                <i class="bi bi-truck me-2"></i> Réceptionner la commande
            </button>
        @endif
        
        <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary px-4 py-4">
            <i class="bi bi-x-circle me-2"></i> Annuler
        </a>
    </div>

    <div class="modal fade" id="receptionModal" tabindex="-1" aria-labelledby="receptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('commandes.reception', $commande) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="receptionModalLabel">Réception commande #{{ $commande->id }}</h5>
                    <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <p>Confirmez-vous la réception de cette commande ?</p>
                    <div class="mb-3">
                        <label for="cout_transport" class="form-label">Coût transport <span class="text-danger">*</span></label>
                        <input type="number" name="cout_transport" id="cout_transport" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="frais_suppl" class="form-label">Frais supplémentaires <span class="text-danger">*</span></label>
                        <input type="number" name="frais_suppl" id="frais_suppl" class="form-control" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center align itels center">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i> Valider la réception</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection