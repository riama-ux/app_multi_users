@extends('pages.admin.shared.layout') {{-- Assurez-vous que ce layout inclut les CSS de DashLite et Bootstrap --}}

@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">Détails du Produit : <span class="text-primary">{{ $produit->nom }}</span></h3>
                <div class="nk-block-des text-soft">
                    <p>Informations complètes sur {{ $produit->nom }}.</p>
                </div>
            </div><div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li>
                                <a href="{{ route('produits.edit', $produit->id) }}" class="btn btn-white btn-dim btn-outline-warning">
                                    <em class="icon ni ni-edit"></em><span>Modifier</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('produits.index') }}" class="btn btn-white btn-dim btn-outline-secondary">
                                    <em class="icon ni ni-arrow-left"></em><span>Retour à la liste</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div></div></div>{{-- Alertes de succès ou d'erreur --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="nk-block">
        <div class="row g-gs"> {{-- g-gs pour l'espacement des colonnes DashLite --}}

            {{-- Colonne Informations Générales --}}
            <div class="col-md-6">
                <div class="card card-bordered h-100"> {{-- card-bordered pour les bordures fines DashLite --}}
                    <div class="card-inner">
                        <div class="card-title-group align-items-start mb-3"> {{-- Groupement titre et badge --}}
                            <div class="card-title">
                                <h6 class="title"><em class="icon ni ni-info"></em> Informations Générales</h6>
                            </div>
                            <div class="card-tools">
                                @php
                                    $statusClass = 'success';
                                    $statusIcon = 'check-circle';
                                    $statusText = 'En Stock';

                                    if ($produit->quantite <= $produit->seuil_alerte && $produit->quantite > 0) {
                                        $statusClass = 'warning';
                                        $statusIcon = 'alert-fill';
                                        $statusText = 'Stock Bas';
                                    } elseif ($produit->quantite <= 0) {
                                        $statusClass = 'danger';
                                        $statusIcon = 'cross-circle-fill';
                                        $statusText = 'En Rupture';
                                    }
                                @endphp
                                <span class="badge bg-{{ $statusClass }} rounded-pill p-1 px-2">
                                    <em class="icon ni ni-{{ $statusIcon }}"></em> {{ $statusText }}
                                </span>
                            </div>
                        </div>
                        <ul class="data-list is-compact"> {{-- Liste compacte stylée DashLite --}}
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Référence</span>
                                    <span class="data-value">{{ $produit->reference ?? '-' }}</span>
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Code</span>
                                    <span class="data-value">{{ $produit->code ?? '-' }}</span>
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Catégorie</span>
                                    <span class="data-value">{{ $produit->categorie->nom ?? '-' }}</span>
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Magasin</span>
                                    <span class="data-value">{{ $produit->magasin->nom ?? '-' }}</span>
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Marque</span>
                                    <span class="data-value">{{ $produit->marque ?? '-' }}</span>
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Unité</span>
                                    <span class="data-value">{{ $produit->unite ?? '-' }}</span>
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Description</span>
                                    <span class="data-value text-wrap text-break">{{ $produit->description ?? '-' }}</span> {{-- Pour les longues descriptions --}}
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Seuil d'alerte</span>
                                    <span class="data-value text-danger fw-bold">{{ $produit->seuil_alerte ?? 0 }} {{ $produit->unite }}</span>
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label fs-5 fw-bold text-dark">Quantité totale actuelle</span>
                                    <span class="data-value fs-5 fw-bold
                                        @if ($produit->quantite <= $produit->seuil_alerte && $produit->quantite > 0)
                                            text-warning
                                        @elseif ($produit->quantite <= 0)
                                            text-danger
                                        @else
                                            text-success
                                        @endif
                                    ">
                                        {{ $produit->quantite }} {{ $produit->unite }}
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>{{-- Colonne Informations Financières --}}
            <div class="col-md-6">
                <div class="card card-bordered h-100">
                    <div class="card-inner">
                        <div class="card-title">
                            <h6 class="title"><em class="icon ni ni-money"></em> Informations Financières</h6>
                        </div>
                        <ul class="data-list is-compact">
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Coût d'achat</span>
                                    <span class="data-value">{{ number_format($produit->cout_achat, 0) }} {{ config('app.currency', 'CFA') }}</span>
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Prix de vente</span>
                                    <span class="data-value">{{ number_format($produit->prix_vente, 0) }} {{ config('app.currency', 'CFA') }}</span>
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label">Marge</span>
                                    <span class="data-value">{{ number_format($produit->marge, 0) }} %</span>
                                </div>
                            </li>
                            <li class="data-item">
                                <div class="data-col">
                                    <span class="data-label fs-5 fw-bold text-dark">Valeur estimée du stock</span>
                                    <span class="data-value fs-5 fw-bold text-primary">
                                        {{ number_format($produit->quantite * $produit->cout_achat, 0) }} {{ config('app.currency', 'CFA') }}
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div></div></div><div class="nk-block nk-block-lg">
        <div class="nk-block-head">
            <div class="nk-block-head-content">
                <h4 class="nk-block-title"><em class="icon ni ni-box"></em> Lots de Stock pour ce Produit</h4>
                <div class="nk-block-des">
                    <p>Détail de chaque lot de stock enregistré pour {{ $produit->nom }}.</p>
                </div>
            </div>
        </div>
        <div class="card card-bordered card-preview"> {{-- Card pour la table --}}
            <div class="card-inner">
                @if ($produit->stockLots->isEmpty())
                    <div class="alert alert-info bg-lighter d-flex align-items-center" role="alert">
                        <em class="icon ni ni-info-fill me-2"></em>
                        <span>Aucun lot de stock enregistré pour ce produit.</span>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table nk-tb-list nk-tb-ulog"> {{-- Classes de tableau DashLite --}}
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col"><span>ID Lot</span></th>
                                    <th class="nk-tb-col tb-col-md"><span>Quantité Restante</span></th>
                                    <th class="nk-tb-col tb-col-md"><span>Quantité Initiale</span></th>
                                    <th class="nk-tb-col tb-col-md"><span>Coût d'Achat Unitaire</span></th>
                                    <th class="nk-tb-col tb-col-lg"><span>Date de Réception</span></th>
                                    <th class="nk-tb-col nk-tb-col-tools">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($produit->stockLots->sortByDesc('created_at') as $lot)
                                    <tr class="nk-tb-item">
                                        <td class="nk-tb-col"><span>#{{ $lot->id }}</span></td>
                                        <td class="nk-tb-col tb-col-md"><span>{{ $lot->quantite }} {{ $produit->unite }}</span></td>
                                        <td class="nk-tb-col tb-col-md">
                                            @php
                                                $remaining_qty = $lot->quantite_restante ?? $lot->quantite;
                                                $lotStatusClass = 'primary';
                                                $lotStatusIcon = '';
                                                $lotStatusText = '';

                                                if ($remaining_qty <= $produit->seuil_alerte && $remaining_qty > 0) {
                                                    $lotStatusClass = 'warning';
                                                    $lotStatusIcon = 'alert-fill';
                                                    $lotStatusText = ' (Bas)';
                                                } elseif ($remaining_qty <= 0) {
                                                    $lotStatusClass = 'danger';
                                                    $lotStatusIcon = 'cross-circle-fill';
                                                    $lotStatusText = ' (Épuisé)';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $lotStatusClass }} rounded-pill p-1 px-2">
                                                @if($lotStatusIcon)<em class="icon ni ni-{{ $lotStatusIcon }}"></em>@endif
                                                {{ $remaining_qty }} {{ $produit->unite }}{{ $lotStatusText }}
                                            </span>
                                        </td>
                                        <td class="nk-tb-col tb-col-md"><span>{{ number_format($lot->cout_achat, 0) }} {{ config('app.currency', 'CFA') }}</span></td>
                                        <td class="nk-tb-col tb-col-lg"><span>{{ $lot->date_reception ? $lot->date_reception->format('d/m/Y H:i') : '-' }}</span></td>
                                        <td class="nk-tb-col nk-tb-col-tools">
                                            <ul class="nk-tb-actions gx-1">
                                                <li>
                                                    <a href="#" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir les mouvements de ce lot">
                                                        <em class="icon ni ni-package"></em>
                                                    </a>
                                                </li>
                                                {{-- Ajoutez d'autres actions ici, par exemple un bouton pour ajuster ce lot --}}
                                                {{-- <li>
                                                    <a href="#" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Ajuster le lot">
                                                        <em class="icon ni ni-reload"></em>
                                                    </a>
                                                </li> --}}
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>@endif
            </div></div></div>
            <script>
    // Initialiser les tooltips DashLite (basés sur Bootstrap)
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        // Pour le toggle du menu d'actions en haut (si DashLite l'utilise avec des classes JS)
        // $('.toggle-expand-content').each(function() {
        //     var target = $(this).data('content');
        //     $('[data-target="'+ target +'"]').on('click', function(e) {
        //         e.preventDefault();
        //         $(this).toggleClass('active');
        //         $('#'+ target).slideToggle(); // ou une autre méthode d'affichage DashLite
        //     });
        // });
    });
</script>
@endsection

@push('scripts')

@endpush