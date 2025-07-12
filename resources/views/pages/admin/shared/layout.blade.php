<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ URL::to('./images/favicon.png') }}">
    <!-- Page Title  -->
    <title>Dashboard - Campaign | DashLite Admin Template</title>
    <!-- StyleSheets  -->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
     <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js" integrity="sha512-..." crossorigin="anonymous"></script> 
    <link rel="stylesheet" href="{{ URL::to('./assets/css/dashlite.css?ver=3.2.3') }}">
    <link id="skin-default" rel="stylesheet" href="{{ URL::to('./assets/css/theme.css?ver=3.2.3') }}">

    @livewireStyles
</head>

<body class="nk-body ui-rounder has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <div class="nk-sidebar is-light nk-sidebar-fixed is-light " data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-sidebar-brand">
                        <a href="{{ route('admin.home') }}" class="logo-link nk-sidebar-logo text-logo">
                            <span class="logo-text">
                                <span class="logo-ika">ika</span><span class="logo-stock">Stock</span>
                            </span>
                        </a>
                    </div>
                    <div class="nk-menu-trigger me-n2">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                    </div>
                </div><!-- .nk-sidebar-element -->


                <div class="nk-sidebar-element">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">Dashboard</h6>
                                </li><!-- .nk-menu-heading -->
                                <li class="nk-menu-item {{ request()->routeIs('admin.home') ? 'active' : '' }}">
                                    <a href="{{ route('admin.home') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-presentation"></em></span>
                                        <span class="nk-menu-text">Tableau de bord</span>
                                    </a>
                                </li><!-- .nk-menu-item -->

                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">Modules</h6>
                                </li><!-- .nk-menu-heading -->

                                <!-- Ventes et Retours Clients -->
                                <li class="nk-menu-item has-sub {{ request()->routeIs('ventes.*') || request()->routeIs('retours_clients.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-cart"></em></span>
                                        <span class="nk-menu-text">Ventes et Retours</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('ventes.create') ? 'active' : '' }}">
                                            <a href="{{ route('ventes.create') }}" class="nk-menu-link"><span class="nk-menu-text">Nouvelle Vente</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('ventes.index') ? 'active' : '' }}">
                                            <a href="{{ route('ventes.index') }}" class="nk-menu-link"><span class="nk-menu-text">Liste des Ventes</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('retours_clients.index') ? 'active' : '' }}">
                                            <a href="{{ route('retours_clients.index') }}" class="nk-menu-link"><span class="nk-menu-text">Retours Clients</span></a>
                                        </li>
                                    </ul>
                                </li><!-- .nk-menu-item -->

                                <!-- Stock (Inventaire, Mouvements, Transferts, Ajustements) -->
                                <li class="nk-menu-item has-sub {{ request()->routeIs('stocks.*') || request()->routeIs('mouvements_stock.*') || request()->routeIs('transferts.*') || request()->routeIs('ajustements.*') || request()->routeIs('stock_lots.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-layers"></em></span>
                                        <span class="nk-menu-text">Stock</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('stocks.index') ? 'active' : '' }}">
                                            <a href="{{ route('stocks.index') }}" class="nk-menu-link"><span class="nk-menu-text">Inventaire (Stocks Actuels)</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('mouvements_stock.index') ? 'active' : '' }}">
                                            <a href="{{ route('mouvements_stock.index') }}" class="nk-menu-link"><span class="nk-menu-text">Mouvements de Stock</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('transferts.index') ? 'active' : '' }}">
                                            <a href="{{ route('transferts.index') }}" class="nk-menu-link"><span class="nk-menu-text">Transferts de Stock</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('ajustements.index') ? 'active' : '' }}">
                                            <a href="{{ route('ajustements.index') }}" class="nk-menu-link"><span class="nk-menu-text">Ajustements de Stock</span></a>
                                        </li>
                                        {{-- Si vous voulez un lien direct pour ajouter un ajustement de type "sortie" (perte), vous pouvez ajouter : --}}
                                        {{-- <li class="nk-menu-item {{ request()->routeIs('ajustements.create') && request('type') == 'sortie' ? 'active' : '' }}">
                                            <a href="{{ route('ajustements.create', ['type' => 'sortie']) }}" class="nk-menu-link"><span class="nk-menu-text">Nouvelle Perte (Ajustement)</span></a>
                                        </li> --}}
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->

                                <!-- Produits -->
                                <li class="nk-menu-item has-sub {{ request()->routeIs('produits.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-tile-thumb"></em></span>
                                        <span class="nk-menu-text">Produits</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('produits.create') ? 'active' : '' }}">
                                            <a href="{{ route('produits.create') }}" class="nk-menu-link"><span class="nk-menu-text">Ajouter Produit</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('produits.index') ? 'active' : '' }}">
                                            <a href="{{ route('produits.index') }}" class="nk-menu-link"><span class="nk-menu-text">Liste des Produits</span></a>
                                        </li>
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->

                                <!-- Catégories -->
                                <li class="nk-menu-item has-sub {{ request()->routeIs('module.categories.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-menu-circled"></em></span>
                                        <span class="nk-menu-text">Catégories</span>
                                    </a>
                                    <ul class="nk-menu-sub">

                                        <li class="nk-menu-item {{ request()->routeIs('module.categories.index') ? 'active' : '' }}">
                                            <a href="{{ route('module.categories.index') }}" class="nk-menu-link"><span class="nk-menu-text">Liste des Catégories</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('module.categories.create') ? 'active' : '' }}">
                                            <a href="{{ route('module.categories.create') }}" class="nk-menu-link"><span class="nk-menu-text">Ajouter Catégorie</span></a>
                                        </li>
                                        
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->

                                <!-- Fournisseurs -->
                                <li class="nk-menu-item has-sub {{ request()->routeIs('module.fournisseurs.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                        <span class="nk-menu-text">Fournisseurs</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('module.fournisseurs.create') ? 'active' : '' }}">
                                            <a href="{{ route('module.fournisseurs.create') }}" class="nk-menu-link"><span class="nk-menu-text">Ajouter Fournisseur</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('module.fournisseurs.index') ? 'active' : '' }}">
                                            <a href="{{ route('module.fournisseurs.index') }}" class="nk-menu-link"><span class="nk-menu-text">Liste des Fournisseurs</span></a>
                                        </li>
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->

                                <!-- Commandes -->
                                <li class="nk-menu-item has-sub {{ request()->routeIs('commandes.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-grid-alt"></em></span>
                                        <span class="nk-menu-text">Commandes</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('commandes.create') ? 'active' : '' }}">
                                            <a href="{{ route('commandes.create') }}" class="nk-menu-link"><span class="nk-menu-text">Nouvelle Commande</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('commandes.index') ? 'active' : '' }}">
                                            <a href="{{ route('commandes.index') }}" class="nk-menu-link"><span class="nk-menu-text">Liste des Commandes</span></a>
                                        </li>
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->

                                <!-- Clients -->
                                <li class="nk-menu-item has-sub {{ request()->routeIs('module.clients.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                        <span class="nk-menu-text">Clients</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('module.clients.create') ? 'active' : '' }}">
                                            <a href="{{ route('module.clients.create') }}" class="nk-menu-link"><span class="nk-menu-text">Ajouter Client</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('module.clients.index') ? 'active' : '' }}">
                                            <a href="{{ route('module.clients.index') }}" class="nk-menu-link"><span class="nk-menu-text">Liste des Clients</span></a>
                                        </li>
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->

                                <!-- Utilisateurs -->
                                <li class="nk-menu-item has-sub {{ request()->routeIs('admin.compte.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-user-list"></em></span>
                                        <span class="nk-menu-text">Utilisateurs</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('admin.compte.create') ? 'active' : '' }}">
                                            <a href="{{ route('admin.compte.create') }}" class="nk-menu-link"><span class="nk-menu-text">Ajouter Utilisateur</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.compte.index') ? 'active' : '' }}">
                                            <a href="{{ route('admin.compte.index') }}" class="nk-menu-link"><span class="nk-menu-text">Liste des Utilisateurs</span></a>
                                        </li>
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->

                                <!-- Magasin -->
                                {{-- J'ai supposé que vous avez des routes de ressource pour les magasins sous le préfixe 'admin.' --}}
                                <li class="nk-menu-item has-sub {{ request()->routeIs('admin.magasins.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-block-over"></em></span>
                                        <span class="nk-menu-text">Magasin</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item {{ request()->routeIs('admin.magasins.create') ? 'active' : '' }}">
                                            <a href="{{ route('admin.magasins.create') }}" class="nk-menu-link"><span class="nk-menu-text">Ajouter Magasin</span></a>
                                        </li>
                                        <li class="nk-menu-item {{ request()->routeIs('admin.magasins.index') ? 'active' : '' }}">
                                            <a href="{{ route('admin.magasins.index') }}" class="nk-menu-link"><span class="nk-menu-text">Liste des Magasins</span></a>
                                        </li>
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->

                                <!-- Paiement des crédits -->
                                {{-- Note: Vos routes pour les paiements sont des POST pour store/annuler.
                                    Pour "Ajouter" et "Afficher", vous aurez besoin de routes GET dédiées.
                                    J'ai mis des placeholders ici, assurez-vous que ces routes existent ou adaptez-les.
                                    Par exemple, si "Afficher" liste les crédits, la route pourrait être 'credits.index'.
                                --}}
                                <li class="nk-menu-item has-sub {{ request()->routeIs('paiements.*') || request()->routeIs('credits.*') ? 'active' : '' }}">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                                        <span class="nk-menu-text">Paiements des crédits</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        {{-- Si vous avez une route pour créer un nouveau paiement de crédit (ex: via une vente ou un client) --}}
                                        <li class="nk-menu-item {{ request()->routeIs('paiements.create') ? 'active' : '' }}">
                                            <a href="#" class="nk-menu-link"><span class="nk-menu-text">Ajouter Paiement</span></a>
                                        </li>
                                        {{-- Si vous avez une route pour lister tous les paiements ou les crédits --}}
                                        <li class="nk-menu-item {{ request()->routeIs('paiements.index') || request()->routeIs('credits.index') ? 'active' : '' }}">
                                            <a href="#" class="nk-menu-link"><span class="nk-menu-text">Afficher Paiements/Crédits</span></a>
                                        </li>
                                    </ul><!-- .nk-menu-sub -->
                                </li><!-- .nk-menu-item -->

                            </ul><!-- .nk-menu -->
                        </div><!-- .nk-sidebar-menu -->
                    </div><!-- .nk-sidebar-content -->
                </div><!-- .nk-sidebar-element -->


                <!-- .nk-sidebar-element -->
            </div>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <div class="nk-header is-light nk-header-fixed is-light">
                    <div class="container-xl wide-xl">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ms-n1 me-3">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                                <a href="{{ route('admin.home') }}" class="logo-link nk-sidebar-logo text-logo">
                                    <span class="logo-text">
                                        <span class="logo-ika">ika</span><span class="logo-stock">Stock</span>
                                    </span>
                                </a>
                            </div><!-- .nk-header-brand -->
                            <div class="nk-header-menu is-light">
                                <div class="nk-header-menu-inner">
                                    <!-- Menu -->
                                    <ul class="nk-menu nk-menu-main">
                                        <li class="nk-menu-item has-sub">
                                            <a href="#" class="nk-menu-link">
                                                <span class="nk-menu-text couleur-active">Tableau de board</span>
                                            </a>
                                        </li><!-- .nk-menu-item -->
                                    </ul>
                                    <!-- Menu -->
                                </div>
                            </div><!-- .nk-header-menu -->
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm">
                                                    <em class="icon ni ni-user-alt"></em>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        <span><em class="icon ni ni-user-alt"></em></span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text">{{ Auth::user()->name }}</span>
                                                        <span class="sub-text">{{ Auth::user()->email }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="{{ route('admin.user.edit', encrypt(Auth::user()->id)) }}"><em class="icon ni ni-user-alt"></em><span>Mon Profile</span></a></li>
                                                    <li><a class="dark-switch" href="#"><em class="icon ni ni-moon"></em><span>Dark Mode</span></a></li>
                                                </ul>
                                            </div>
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li>
                                                        <a href="{{ route('auth.logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><em class="icon ni ni-signout"></em><span>Déconnexion</span></a>
                                                        <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="d-none">
                                                            @csrf
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div><!-- .nk-header-tools -->
                        </div><!-- .nk-header-wrap -->
                    </div><!-- .container-fliud -->
                </div>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-xl">
                        <div class="nk-content-body">
                            <div class="nk-block-head nk-block-head-sm">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h3 class="nk-block-title page-title">{{ $magasinActif?->nom ?? 'Aucun magasin sélectionné' }}</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Bienvenue {{ Auth::user()->name }}.</p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                    <div class="nk-block-head-content">
                                        @if(session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                        @endif
                                        @if(session('error'))
                                            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        @endif
                                        <div class="toggle-wrap nk-block-tools-toggle">
                                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                            @yield('header')
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            @yield('content')
                        </div>
                    </div>
                </div>
                <!-- content @e -->
                <!-- footer @s -->
                <div class="nk-footer">
                    <div class="container-xl wide-xl">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy; 2025 by <a href="#" target="_blank">miissdiial@gmail.com</a>
                            </div> 
                        </div>
                    </div>
                </div>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->

    <!-- JavaScript -->
    <script src="{{ URL::to('./assets/js/bundle.js?ver=3.2.3') }}"></script>
    <script src="{{ URL::to('./assets/js/scripts.js?ver=3.2.3') }}"></script>
    <script src="{{ URL::to('./assets/js/charts/gd-campaign.js?ver=3.2.3') }}"></script>

    @livewireScripts
</body>

</html>