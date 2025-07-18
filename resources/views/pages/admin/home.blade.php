@extends('pages.admin.shared.layout')

@section('header')
    
<div class="toggle-expand-content" data-content="pageMenu">
    <ul class="nk-block-tools g-3">
        <li>
            @php
                $user = auth()->user();
                $magasins = $user->role === 'Admin' ? \App\Models\Magasin::all() : $user->magasins;
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

        <li class="nk-block-tools-opt">
            <a href="#" class="btn btn-primary">
                <em class="icon ni ni-reports"></em>
            </a>
        </li>
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
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        {{-- Ajoutez ici des boutons d'action rapide si nécessaire --}}
                                    </ul>
                                </div>
                            </div>
                        </div><!-- .nk-block-head-content -->
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->

                <div class="nk-block">
                    <div class="row g-gs">
                        {{-- Carte des Meilleures Ventes (Total des ventes) --}}
                        <div class="col-lg-3 col-sm-6">
                            <div class="card custom-card-height2 h-100 bg-primary">
                                <div class="nk-cmwg nk-cmwg1">
                                    <div class="card-inner pt-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="flex-item">
                                                <div class="text-white d-flex flex-wrap">
                                                    <span class="fs-2 me-1">{{ number_format($lowMarginProductsCount ?? 0, 0, ',', ' ') }}</span>
                                                    <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-down"></em> (Produits)</span> {{-- Ajustez l'icône et le texte --}}
                                                </div>
                                                <h6 class="text-white">Alerte Marge</h6> {{-- Nouveau titre --}}
                                            </div>
                                            {{-- ... reste du code de la carte ... --}}
                                        </div>
                                    </div>
                                    <div class="position-absolute bottom-0 end-0 p-2">
                                        <em class="icon ni ni-alert-fill couleur_icone3 fs-big-icon"></em> {{-- Nouvelle icône suggérée --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Carte du Produit le plus rentable (Valeur du Stock) --}}
                        <div class="col-lg-3 col-sm-6">
                            <div class="card custom-card-height2 h-100 bg-info">
                                <div class="nk-cmwg nk-cmwg1">
                                    <div class="card-inner pt-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="flex-item">
                                                <div class="text-white d-flex flex-wrap">
                                                    {{-- Utilise totalStockValue du contrôleur --}}
                                                    <span class="fs-2 me-1">{{ number_format($totalStockValue ?? 0, 0, ',', ' ') }}</span>
                                                    {{-- Pourcentage statique, à rendre dynamique si besoin --}}
                                                    <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-up"></em>FCFA</span>
                                                </div>
                                                <h6 class="text-white">Valeur du Stock</h6> {{-- Renommé pour correspondre à la variable --}}
                                            </div>
                                            <div class="card-tools me-n1">
                                               
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                    <div class="position-absolute bottom-0 end-0 p-2">
                                        <em class="icon ni ni-bar-chart couleur_icone2 fs-big-icon"></em>
                                    </div>
                                </div><!-- .nk-cmwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->

                        {{-- Carte des Pertes (à adapter si vous avez une variable pour les pertes réelles) --}}
                        <div class="col-lg-3 col-sm-6">
                            <div class="card custom-card-height2 position-relative h-100 bg-warning">
                                <div class="nk-cmwg nk-cmwg1">
                                    <div class="card-inner pt-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="flex-item">
                                                <div class="text-white d-flex flex-wrap">
                                                    {{-- Utilise totalLosses du contrôleur --}}
                                                    <span class="fs-2 me-1">{{ number_format($totalLosses ?? 0, 0, ',', ' ') }}</span>
                                                    {{-- Pourcentage statique, à rendre dynamique si besoin --}}
                                                    <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-down"></em>(Produits)</span>
                                                </div>
                                                <h6 class="text-white">Pertes</h6>
                                            </div>
                                            <div class="card-tools me-n1">
                                                
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                    <div class="position-absolute bottom-0 end-0 p-2">
                                        <em class="icon ni ni-coins couleur_icone fs-big-icon"></em>
                                    </div>
                                </div><!-- .nk-cmwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->

                        {{-- Carte Alerte Stock --}}
                        <div class="col-lg-3 col-sm-6">
                            <div class="card custom-card-height2 position-relative h-100 bg-danger">
                                <div class="nk-cmwg nk-cmwg1">
                                    <div class="card-inner pt-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="flex-item">
                                                <div class="text-white d-flex flex-wrap">
                                                    {{-- Utilise totalAlerts du contrôleur --}}
                                                    <span class="fs-2 me-1">{{ number_format($totalAlerts ?? 0, 0, ',', ' ') }}</span>
                                                    {{-- Pourcentage statique, à rendre dynamique si besoin --}}
                                                    <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-down"></em>(Produits)</span>
                                                </div>
                                                <h6 class="text-white">Alerte Stock</h6>
                                            </div>
                                            <div class="card-tools me-n1">
                                                
                                            </div>
                                        </div>
                                    </div><!-- .card-inner -->
                                    <div class="position-absolute bottom-0 end-0 p-2">
                                        <em class="icon ni ni-alert-circle-fill text-pink fs-big-icon"></em>
                                    </div>
                                </div><!-- .nk-cmwg -->
                            </div><!-- .card -->
                        </div><!-- .col -->

                        {{-- Nouvelle section pour les cartes de bénéfices, dettes, chiffre d'affaires et commandes --}}
                        <div class="col-lg-9">
                            <div class="d-flex flex-column h-100">
                                <div class="row mb-3">
                                    {{-- Bénéfices du mois --}}
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <div class="card position-relative custom-card-height h-100">
                                            <div class="card-inner h-100 w-100 d-flex flex-column">
                                                <h5 class="text-dark text-start">Bénéfices du mois</h5>
                                                <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                    {{-- Utilise monthlyProfit du contrôleur --}}
                                                    <p class="text-dark responsive-text text-center m-0">{{ number_format($monthlyProfit ?? 0, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</p>
                                                </div>
                                            </div>
                                            <div class="position-absolute bottom-0 end-0 p-2">
                                                <em class="icon ni ni-coins card-couleur fs-big-icon"></em>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Dettes --}}
                                    <div class="col-md-6">
                                        <div class="card position-relative custom-card-height h-100">
                                            <div class="card-inner h-100 w-100 d-flex flex-column">
                                                <h5 class="text-dark text-start">Dettes</h5>
                                                <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                    {{-- Utilise totalDebts du contrôleur --}}
                                                    <p class="text-dark responsive-text text-center m-0">{{ number_format($totalDebts ?? 0, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</p>
                                                </div>
                                            </div>
                                            <div class="position-absolute bottom-0 end-0 p-2">
                                                <em class="icon ni ni-file-docs card-couleur fs-big-icon"></em>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Chiffre d'affaire du mois --}}
                                {{-- Top 5 des produits les plus vendus (remplace Chiffre d'affaires du mois) --}}
                                <div class="card card-bordered h-100">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-3">
                                            <div class="card-title">
                                                <h6 class="title">Top 5 Meilleurs ventes (Ce Mois)</h6>
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
                                            </div>@forelse($topSoldProducts as $product)
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col"><span class="fw-bold">{{ $product->product_name }}</span></div>
                                                <div class="nk-tb-col tb-col-md"><span>{{ number_format($product->total_quantity_sold, 0, ',', ' ') }}</span></div>
                                                <div class="nk-tb-col tb-col-lg"><span class="text-success">{{ number_format($product->total_revenue_from_product, 0, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</span></div>
                                                <div class="nk-tb-col tb-col-md"><span>{{ Str::limit($product->product_description, 50) ?? 'N/A' }}</span></div>
                                            </div>@empty
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col text-center text-muted" colspan="4">Aucun produit vendu ce mois.</div>
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Commandes en cours --}}
                        <div class="col-lg-3">
                            <div class="card position-relative custom-card-full-height h-100">
                                <div class="card-inner d-flex flex-column w-100 h-100">
                                    <h5 class="text-dark text-start">Commandes en cours</h5>
                                    <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                        {{-- Utilise pendingOrdersCount du contrôleur --}}
                                        <p class="text-dark responsive-text3 text-center m-0">{{ number_format($pendingOrdersCount ?? 0, 0, ',', ' ') }}</p>
                                    </div>
                                </div>
                                <div class="position-absolute bottom-0 end-0 p-2">
                                    <em class="icon ni ni-reload card-couleur fs-big-icon"></em>
                                </div>
                            </div>
                        </div>
                    </div><!-- .row -->
                </div><!-- .nk-block -->

                {{-- Anciennes sections (Ventes Récentes, Retours Récents, Ajustements Récents) --}}
                <div class="nk-block nk-block-lg">
                    <div class="row g-gs">
                        {{-- Section des Ventes Récentes --}}
                        <div class="col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group align-items-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title"><em class="icon ni ni-cart"></em> Ventes Récentes</h6>
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
                                            <div class="nk-tb-col"><span>#{{ $sale->id }}</span></div>
                                            <div class="nk-tb-col tb-col-sm"><span>{{ $sale->client->nom ?? 'N/A' }}</span></div>
                                            <div class="nk-tb-col tb-col-md"><span class="text-success">{{ number_format($sale->total_ttc, 2, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>{{ $sale->date_vente->format('d/m/Y H:i') }}</span></div>
                                        </div><!-- .nk-tb-item -->
                                        @empty
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col text-center" colspan="4">Aucune vente récente.</div>
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
                                            <h6 class="title"><em class="icon ni ni-undo"></em> Retours Récents</h6>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('retours_clients.index') }}" class="link link-light">Voir tout <em class="icon ni ni-arrow-right"></em></a>
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
                                            <div class="nk-tb-col"><span>#{{ $retour->id }}</span></div>
                                            <div class="nk-tb-col tb-col-sm"><span>{{ $retour->client->nom ?? 'N/A' }}</span></div>
                                            <div class="nk-tb-col tb-col-md"><span class="text-info">{{ number_format($retour->montant_rembourse, 2, ',', ' ') }} {{ config('app.currency', 'FCFA') }}</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>{{ $retour->date_retour->format('d/m/Y H:i') }}</span></div>
                                        </div><!-- .nk-tb-item -->
                                        @empty
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col text-center" colspan="4">Aucun retour récent.</div>
                                        </div>
                                        @endforelse
                                    </div><!-- .nk-tb-list -->
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->

                        {{-- Section des Ajustements Récents --}}
                        <div class="col-lg-12">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group align-items-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title"><em class="icon ni ni-setting"></em> Ajustements Récents</h6>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('ajustements.index') }}" class="link link-light">Voir tout <em class="icon ni ni-arrow-right"></em></a>
                                        </div>
                                    </div>
                                    <div class="nk-tb-list nk-tb-ulog">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span>ID Ajustement</span></div>
                                            <div class="nk-tb-col tb-col-sm"><span>Type</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>Motif Global</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>Date</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>Par</span></div>
                                        </div><!-- .nk-tb-item -->
                                        @forelse($recentAjustements as $ajustement)
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col"><span>#{{ $ajustement->id }}</span></div>
                                            <div class="nk-tb-col tb-col-sm">
                                                <span class="badge {{ $ajustement->type == 'entree' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($ajustement->type) }}
                                                </span>
                                            </div>
                                            <div class="nk-tb-col tb-col-md"><span>{{ $ajustement->motif_global ?? 'N/A' }}</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>{{ $ajustement->date_ajustement->format('d/m/Y H:i') }}</span></div>
                                            <div class="nk-tb-col tb-col-lg"><span>{{ $ajustement->user->name ?? 'N/A' }}</span></div>
                                        </div><!-- .nk-tb-item -->
                                        @empty
                                        <div class="nk-tb-item">
                                            <div class="nk-tb-col text-center" colspan="5">Aucun ajustement récent.</div>
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