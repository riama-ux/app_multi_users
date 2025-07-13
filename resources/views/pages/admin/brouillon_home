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
                <em class="icon ni ni-reports"></em><span>Rapport</span>
            </a>
        </li>
    </ul>
</div>


@endsection

@section('content')

            <div class="nk-block">
                <div class="row g-gs">
                    <div class="col-lg-3 col-sm-6">
                        <div class="card custom-card-height2 h-100 bg-primary">
                            <div class="nk-cmwg nk-cmwg1">
                                <div class="card-inner pt-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-item">
                                            <div class="text-white d-flex flex-wrap">
                                                <span class="fs-2 me-1">56.8K</span>
                                                <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-up"></em>12.4%</span>
                                            </div>
                                            <h6 class="text-white">Meilleurs ventes</h6>
                                        </div>
                                        <div class="card-tools me-n1">
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-sm btn-trigger on-dark" data-bs-toggle="dropdown"><em class="icon ni ni-more-v"></em></a>
                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                        <li><a href="#"><span>30 Days</span></a></li>
                                                        <li><a href="#"><span>3 Months</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- .card-inner -->
                                <div class="position-absolute bottom-0 end-0 p-2">
                                    <em class="icon ni ni-tranx couleur_icone3 fs-big-icon"></em>
                                </div>
                            </div><!-- .nk-cmwg -->
                        </div><!-- .card -->
                    </div><!-- .col -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card custom-card-height2 h-100 bg-info">
                            <div class="nk-cmwg nk-cmwg1">
                                <div class="card-inner pt-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-item">
                                            <div class="text-white d-flex flex-wrap">
                                                <span class="fs-2 me-1">857.6K</span>
                                                <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-up"></em>12.4%</span>
                                            </div>
                                            <h6 class="text-white">Produit le plus rentable</h6>
                                        </div>
                                        <div class="card-tools me-n1">
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-sm btn-trigger on-dark" data-bs-toggle="dropdown"><em class="icon ni ni-more-v"></em></a>
                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                        <li><a href="#"><span>30 Days</span></a></li>
                                                        <li><a href="#"><span>3 Months</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- .card-inner -->
                                <div class="position-absolute bottom-0 end-0 p-2">
                                    <em class="icon ni ni-bar-chart couleur_icone2 fs-big-icon"></em>
                                </div>
                            </div><!-- .nk-cmwg -->
                                            
                        </div><!-- .card -->
                    </div><!-- .col -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card custom-card-height2 position-relative h-100 bg-warning">
                            <div class="nk-cmwg nk-cmwg1">
                                <div class="card-inner pt-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-item">
                                            <div class="text-white d-flex flex-wrap">
                                                <span class="fs-2 me-1">9.3K</span>
                                                <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-up"></em>12.4%</span>
                                            </div>
                                            <h6 class="text-white">Pertes</h6>
                                        </div>
                                        <div class="card-tools me-n1">
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-sm btn-trigger on-dark" data-bs-toggle="dropdown"><em class="icon ni ni-more-v"></em></a>
                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                        <li><a href="#"><span>30 Days</span></a></li>
                                                        <li><a href="#"><span>3 Months</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- .card-inner -->
                                <div class="position-absolute bottom-0 end-0 p-2">
                                    <em class="icon ni ni-coins couleur_icone fs-big-icon"></em>
                                </div>
                            </div><!-- .nk-cmwg -->
                                        </div><!-- .card -->
                    </div><!-- .col -->
                    <div class="col-lg-3 col-sm-6">
                        <div class="card custom-card-height2 position-relative h-100 bg-danger">
                            <div class="nk-cmwg nk-cmwg1">
                                <div class="card-inner pt-3">
                                    <div class="d-flex justify-content-between">
                                        <div class="flex-item">
                                            <div class="text-white d-flex flex-wrap">
                                                <span class="fs-2 me-1">175.2K</span>
                                                <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-up"></em>12.4%</span>
                                            </div>
                                            <h6 class="text-white">Alerte Stock</h6>
                                        </div>
                                        <div class="card-tools me-n1">
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-sm btn-trigger on-dark" data-bs-toggle="dropdown"><em class="icon ni ni-more-v"></em></a>
                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                        <li><a href="#"><span>30 Days</span></a></li>
                                                        <li><a href="#"><span>3 Months</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- .card-inner -->
                                <div class="position-absolute bottom-0 end-0 p-2">
                                    <em class="icon ni ni-alert-circle-fill text-pink fs-big-icon"></em>
                                </div>
                            </div><!-- .nk-cmwg -->
                        </div><!-- .card -->
                    </div><!-- .col -->
                    <!-- j'ai ajouté cette partie -->
                    <div class="col-lg-9">
                        <div class="d-flex flex-column h-100">
                            <!-- Ajoute une row responsive -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="card position-relative custom-card-height h-100">
                                        <div class="card-inner h-100 w-100 d-flex flex-column">
                                            <h5 class="text-dark text-start">Bénéfices du mois</h5>
                                            <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                <p class="text-dark responsive-text text-center m-0">10000000 FCFA.</p>
                                            </div>
                                        </div>
                                        <div class="position-absolute bottom-0 end-0 p-2">
                                            <em class="icon ni ni-coins card-couleur fs-big-icon"></em>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card position-relative custom-card-height h-100">
                                        <div class="card-inner h-100 w-100 d-flex flex-column">
                                            <h5 class="text-dark text-start">Dettes</h5>
                                            <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                                <p class="text-dark responsive-text text-center m-0">10000000 FCFA.</p>
                                            </div>
                                        </div>
                                        <div class="position-absolute bottom-0 end-0 p-2">
                                            <em class="icon ni ni-file-docs card-couleur fs-big-icon"></em>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Troisième carte inchangée -->
                            <div>
                                <div class="card position-relative custom-card-height h-100">
                                    <div class="card-inner h-100 w-100 d-flex flex-column">
                                        <h5 class="text-dark text-start">Chiffre d'affaire du mois</h5>
                                        <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                            <p class="text-dark responsive-text text-center m-0">10000000 FCFA.</p>
                                        </div>
                                    </div>
                                    <div class="position-absolute bottom-0 end-0 p-2">
                                        <em class="icon ni ni-growth card-couleur fs-big-icon"></em>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="card position-relative custom-card-full-height h-100">
                            <div class="card-inner d-flex flex-column w-100 h-100">
                                <h5 class="text-dark text-start">Commandes en cours</h5>
                                <div class="flex-grow-1 d-flex justify-content-center align-items-center">
                                    <p class="text-dark responsive-text3 text-center m-0">20</p>
                                </div>
                            </div>
                            <div class="position-absolute bottom-0 end-0 p-2">
                                <em class="icon ni ni-reload card-couleur fs-big-icon"></em>
                            </div>
                        </div>
                    </div>
                    <!-- j'ai ajouté cette partie -->
                </div><!-- .row -->
            </div><!-- .nk-block -->

@endsection