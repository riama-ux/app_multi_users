@extends('pages.admin.shared.layout')

@section('content')
<div class="col-xxl-8">
    <div class="card card-full">
        <div class="card-inner">
            <div class=" d-flex justify-content-end">
                <a href="{{ route('admin.compte.create') }}" class="btn btn-icon btn-primary mb-5">
                    <em class="icon ni ni-plus"></em>
                </a>
            </div>
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
                    <div class="nk-tb-col tb-col"><span>Magasins</span></div>
                    <div class="nk-tb-col"><span>&nbsp;</span></div>
                </div>

                @forelse($users as $row)
                    <div class="nk-tb-item">
                        {{-- Nom --}}
                        <div class="nk-tb-col">
                            <span class="tb-lead">
                                <strong>{{ $row->name }}</strong>
                            </span>
                        </div>

                        {{-- Rôle avec badge --}}
                        <div class="nk-tb-col tb-col">
                            <div class="user-card">
                                <div class="user-name">
                                    <span class="tb-lead">
                                        <strong>{{ $row->role }}</strong>
                                        @if($row->role == 'Non Actif')
                                            <span class="badge bg-warning text-dark ms-1">à activer</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="nk-tb-col tb-col">
                            <span class="tb-sub text-primary">{{ $row->email }}</span>
                        </div>

                        {{-- Magasins --}}
                        <div class="nk-tb-col tb-col">
                            @if($row->magasins->count())
                                <ul class="list-unstyled mb-0">
                                    @foreach($row->magasins as $magasin)
                                        <li>{{ $magasin->nom }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">Aucun</span>
                            @endif
                        </div>

                        {{-- Actions --}}
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
    </div>
</div>
@endsection
