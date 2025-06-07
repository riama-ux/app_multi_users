@extends('pages.admin.shared.layout')

@section('header')
    
<div class="toggle-expand-content" data-content="pageMenu">
    <ul class="nk-block-tools g-3">
        <li>
            <a href="#" class="dropdown-toggle btn btn-white btn-dim btn-outline-light" data-bs-toggle="dropdown"><em class="icon ni ni-plus"></em><span><span class="d-md-none">Autres</span><span class="d-none d-md-block">Autres Magasins</span></span></a>
        </li>
        <li class="nk-block-tools-opt"><a href="{{ route('admin.compte.create') }}" class="btn btn-icon btn-primary"><em class="icon ni ni-plus"></em></a></li>
    </ul>
</div>

@endsection

@section('content')

                            <div class="col-xxl-8">
                                <div class="card card-full">
                                    <div class="card-inner">
                                        @include('flash-message')
                                        <div class="card-header bg-white">

                                            <form class="navbar-search" method="get" action="{{ route('admin.compte.search') }}">
                                            @include('pages.admin.shared.compteSearchBar')
                                            </form>
                                        </div>
                                        
                                    </div>
                                    <div class="card-inner p-0 border-top">
                                        <div class="nk-tb-list nk-tb-orders">
                                            <div class="nk-tb-item nk-tb-head">
                                                <div class="nk-tb-col"><span>Nom utilisateur</span></div>
                                                <div class="nk-tb-col tb-col"><span>Rôle</span></div>
                                                <div class="nk-tb-col tb-col"><span>Email</span></div>
                                                <div class="nk-tb-col"><span>&nbsp;</span></div>
                                            </div>
                                            @forelse($users as $row)
                                                <div class="nk-tb-item">
                                                    <div class="nk-tb-col">
                                                        <span class="tb-lead">
                                                            <a href="#"><strong>{{ $row->name }}<span class="text-danger">{{ $row->numero }}</span></strong></a>
                                                        </span>
                                                    </div>

                                                    <div class="nk-tb-col tb-col">
                                                        <div class="user-card">
                                                            <div class="user-name">
                                                                <span class="tb-lead"><strong>{{ $row->role }}</strong></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="nk-tb-col tb-col">
                                                        <span class="tb-sub text-primary">{{ $row->email }}</span>
                                                    </div>

                                                    <div class="nk-tb-col nk-tb-col-action">
                                                        <div class="dropdown">
                                                            <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                                <em class="icon ni ni-more-h"></em>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                                <ul class="link-list-plain">
                                                                    <li>
                                                                        <a href="{{ route('admin.compte.edit', encrypt($row->id)) }}">
                                                                            Modifier
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <form action="{{ route('admin.compte.destroy', $row->id) }}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" onclick="return confirm('Voulez-vous supprimer le compte ?')" class="btn text-danger w-100 text-start">
                                                                                <i class="align-middle" data-feather="x"></i> Supprimer
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="nk-tb-item">
                                                    <div class="nk-tb-col text-center w-100">
                                                        <span class="text-danger h6">Aucun enregistrement trouvé !</span>
                                                    </div>
                                                </div>
                                            @endforelse
                                            <div class="d-flex justify-content-center mt-3">
                                                @if(request()->is('admin/compte'))
                                                    {!! $users->links() !!}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                            
                                </div><!-- .card -->
                            </div>
                        
@endsection
        