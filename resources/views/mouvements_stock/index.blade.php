@extends('pages.admin.shared.layout')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Historique des mouvements de stock</h3>
                    <div class="nk-block-desc">
                        <p>Consulter et filtrer l'historique détaillé des mouvements de stock.</p>
                    </div>
                </div>
            </div>
        </div><div class="nk-block">
            <div class="card card-bordered">
                <div class="card-inner">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label visually-hidden" for="type">Type de mouvement</label>
                                <select name="type" class="form-select form-control">
                                    <option value="">-- Type de mouvement --</option>
                                    <option value="entree" {{ request('type') === 'entree' ? 'selected' : '' }}>Entrée</option>
                                    <option value="sortie" {{ request('type') === 'sortie' ? 'selected' : '' }}>Sortie</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label visually-hidden" for="produit">Nom produit</label>
                                <input type="text" name="produit" class="form-control" placeholder="Nom produit" value="{{ request('produit') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label visually-hidden" for="date">Date</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary w-100">
                                    <em class="icon ni ni-filter-alt"></em><span>Filtrer</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div><div class="nk-block nk-block-lg">
            <div class="card card-preview">
                <div class="card-inner">
                    <div class="table-responsive">
                        <table class="nk-tb-list nk-tb-ulist is-separate">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col"><span>Date</span></th>
                                    <th class="nk-tb-col tb-col-md"><span>Produit</span></th>
                                    <th class="nk-tb-col"><span>Type</span></th>
                                    <th class="nk-tb-col tb-col-sm"><span>Quantité</span></th>
                                    <th class="nk-tb-col tb-col-md"><span>Lot</span></th>
                                    <th class="nk-tb-col tb-col-md"><span>Motif</span></th>
                                    <th class="nk-tb-col tb-col-md"><span>Source</span></th>
                                    <th class="nk-tb-col tb-col-lg"><span>Statut Retard</span></th>
                                    <th class="nk-tb-col tb-col-lg"><span>Utilisateur</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mouvements as $mouvement)
                                <tr class="nk-tb-item">
                                    <td class="nk-tb-col"><span>{{ $mouvement->date->format('d/m/Y H:i') }}</span></td>
                                    <td class="nk-tb-col tb-col-md"><span>{{ $mouvement->produit->nom }}</span></td>
                                    <td class="nk-tb-col">
                                        @php
                                            $badgeClass = $mouvement->type === 'entree' ? 'success' : 'danger';
                                            $badgeIcon = $mouvement->type === 'entree' ? 'arrow-down-fill' : 'arrow-up-fill';
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">
                                            <em class="icon ni ni-{{ $badgeIcon }}"></em> {{ ucfirst($mouvement->type) }}
                                        </span>
                                    </td>
                                    <td class="nk-tb-col tb-col-sm"><span>{{ $mouvement->quantite }}</span></td>
                                    <td class="nk-tb-col tb-col-md">
                                        @if($mouvement->lot)
                                            <span>Lot #{{ $mouvement->lot->id }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="nk-tb-col tb-col-md"><span>{{ $mouvement->motif ?? '-' }}</span></td>
                                    <td class="nk-tb-col tb-col-md">
                                        @if($mouvement->source_type && $mouvement->source_id)
                                            @php
                                                $route = match($mouvement->source_type) {
                                                    'commande' => route('commandes.show', $mouvement->source_id),
                                                    'vente' => route('ventes.show', $mouvement->source_id),
                                                    'transfert' => route('transferts.show', $mouvement->source_id),
                                                    'ajustement' => route('ajustements.show', $mouvement->source_id),
                                                    default => null
                                                };
                                                $sourceType = ucfirst($mouvement->source_type);
                                            @endphp

                                            @if($route)
                                                <a href="{{ $route }}" target="_blank">
                                                    {{ $sourceType }} #{{ $mouvement->source_id }}
                                                </a>
                                            @else
                                                <span>{{ $sourceType }} #{{ $mouvement->source_id }}</span>
                                            @endif
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>
                                    <td class="nk-tb-col tb-col-lg">
                                        @if ($mouvement->type === 'entree')
                                            @if ($mouvement->is_commande_late)
                                                <span class="badge badge-dim bg-danger">Retard ({{ $mouvement->days_commande_late }}j)</span>
                                            @else
                                                <span class="badge badge-dim bg-success">À temps</span>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="nk-tb-col tb-col-lg"><span>{{ $mouvement->user->name ?? '-' }}</span></td>
                                </tr>
                                @empty
                                <tr class="nk-tb-item">
                                    <td class="nk-tb-col text-center" colspan="9">
                                        <div class="py-4">
                                            <em class="icon ni ni-info-fill fs-30px text-muted mb-2"></em>
                                            <p class="text-muted">Aucun mouvement de stock trouvé pour les critères sélectionnés.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div></div>@if ($mouvements->hasPages())
                <div class="card-inner">
                    <div class="d-flex justify-content-center justify-content-md-between align-items-center flex-wrap">
                        {{ $mouvements->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            </div></div></div>
</div>
@endsection