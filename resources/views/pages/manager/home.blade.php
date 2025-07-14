@extends('pages.manager.shared.layout')

@section('header')
    
<div class="toggle-expand-content" data-content="pageMenu">
    <ul class="nk-block-tools g-3">
        <li>
            {{-- This PHP block for store switching is kept as it's useful for managers --}}
            @php
                $user = auth()->user();
                // Assuming managers are associated with specific stores, not all stores like an Admin
                $magasins = $user->magasins; 
                $magasinActifId = session('magasin_actif_id') ?? ($magasins->first()->id ?? null);
            @endphp

            @if($magasins->count())
                <form action="{{ route('switch.magasin') }}" method="POST" class="d-inline-block">
                    @csrf
                    <select name="magasin_id" onchange="this.form.submit()" class="form-select form-select-sm">
                        @foreach($magasins as $magasin)
                            <option value="{{ $magasin->id }}" {{ $magasin->id == $magasinActifId ? 'selected' : '' }}>
                                {{ $magasin->nom }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </li>

        {{-- Removed "Rapport" button as it might be an admin-only feature --}}
    </ul>
</div>

@endsection

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        {{-- Quick action buttons for manager if needed --}}
                                    </ul>
                                </div>
                            </div>
                        </div><!-- .nk-block-head-content -->
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->

                <div class="nk-block">
                    <div class="row g-gs">
                        {{-- Top 5 des produits les plus vendus (Top 5 Best Sellers) --}}
                        <div class="col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group mb-3">
                                        <div class="card-title">
                                            <h6 class="title"><em class="icon ni ni-star-fill me-1 text-primary"></em> Top 5 Meilleurs ventes (Ce Mois)</h6>
                                        </div>
                                        <div class="card-tools">
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="nk-tb-list nk-tb-ulog">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span>Produit</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>Quantité</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>CA</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>Description</span></div>
                                        </div><!-- .nk-tb-item (Header) -->
                                        
                                        @forelse($topSoldProducts as $product)
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col"><span class="fw-bold">{{ $product->product_name }}</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>{{ number_format($product->total_quantity_sold, 0, ',', ' ') }}</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span class="text-success">{{ number_format($product->total_revenue_from_product, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>{{ Str::limit($product->product_description, 50) ?? 'N/A' }}</span></div>
                                        </div><!-- .nk-tb-item -->
                                        @empty
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col text-center text-muted" colspan="4">Aucun produit vendu ce mois.</div>
                                        </div>
                                        @endforelse
                                    </div><!-- .nk-tb-list -->
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->

                        {{-- Dettes (Debts) --}}
                        <div class="col-lg-6">
                            <div class="card card-bordered custom-card-height h-100 dash-card-debt">
                                <div class="card-inner d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="dash-icon me-3">
                                            <em class="icon ni ni-file-docs text-danger"></em>
                                        </div>
                                        <h5 class="text-dark mb-0">Dettes</h5>
                                    </div>
                                    
                                    <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                        <p class="text-dark fs-2 fw-bold m-0">{{ number_format($totalDebts ?? 0, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- .row -->
                </div><!-- .nk-block -->

                <div class="nk-block nk-block-lg">
                    <div class="row g-gs">
                        {{-- Section des Ventes Récentes --}}
                        <div class="col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group align-items-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title"><em class="icon ni ni-cart me-1"></em> Ventes Récentes</h6>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('ventes.index') }}" class="link link-light">Voir tout <em class="icon ni ni-arrow-right"></em></a>
                                        </div>
                                    </div>
                                    
                                    <div class="nk-tb-list nk-tb-ulog">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span>ID Vente</span></div>
                                            <div class="nk-tb-col tb-col-sm"><span>Client</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>Montant</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>Date</span></div>
                                        </div><!-- .nk-tb-item -->
                                        @forelse($recentSales as $sale)
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col"><span class="fw-bold text-primary">#{{ $sale->id }}</span></div>
                                            <div class="nk-tb-col tb-col-sm"><span>{{ $sale->client->nom ?? 'N/A' }}</span></div>
                                            <div class="nk-tb-col tb-col-md"><span class="text-success">{{ number_format($sale->total_ttc, 2, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>{{ $sale->date_vente->format('d/m/Y H:i') }}</span></div>
                                        </div><!-- .nk-tb-item -->
                                        @empty
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col text-center text-muted" colspan="4">Aucune vente récente.</div>
                                        </div>
                                        @endforelse
                                    </div><!-- .nk-tb-list -->
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->

                        {{-- Section des Retours Récents --}}
                        <div class="col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group align-items-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title"><em class="icon ni ni-undo me-1"></em> Retours Récents</h6>
                                        </div>
                                        <div class="card-tools">
                                            
                                        </div>
                                    </div>
                                    <div class="nk-tb-list nk-tb-ulog">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span>ID Retour</span></div>
                                            <div class="nk-tb-col tb-col-sm"><span>Client</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>Remboursé</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>Date</span></div>
                                        </div><!-- .nk-tb-item -->
                                        @forelse($recentReturns as $retour)
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col"><span class="fw-bold text-info">#{{ $retour->id }}</span></div>
                                            <div class="nk-tb-col tb-col-sm"><span>{{ $retour->client->nom ?? 'N/A' }}</span></div>
                                            <div class="nk-tb-col tb-col-md"><span class="text-info">{{ number_format($retour->montant_rembourse, 2, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>{{ $retour->date_retour->format('d/m/Y H:i') }}</span></div>
                                        </div><!-- .nk-tb-item -->
                                        @empty
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col text-center text-muted" colspan="4">Aucun retour récent.</div>
                                        </div>
                                        @endforelse
                                    </div><!-- .nk-tb-list -->
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->
                    </div><!-- .row -->
                </div><!-- .nk-block -->
            </div>
        </div>
    </div>
</div>
@endsection
