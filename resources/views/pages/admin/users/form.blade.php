
@extends('pages.admin.shared.layout')

@section('header')
    
<div class="toggle-expand-content" data-content="pageMenu">
    <ul class="nk-block-tools g-3">
                                                    
        <li class="nk-block-tools-opt"><a href="{{ route('admin.compte.index') }}" class="btn btn-icon btn-primary"><em class="icon ni ni-eye"></em></a></li>
    </ul>
</div>

@endsection

@section('content')
 
                            <div class="card card-preview">
                                <div class="card-inner">
                                    @include('flash-message')
                                    <div class="preview-block">
                                        <form action="{{ request()->is('admin/compte/create') ? route('admin.compte.store') : route('admin.compte.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method(request()->is('admin/compte*/edit') ? 'PUT' : 'POST')

                                            <div class="row gy-4">
                                                {{-- Nom --}}
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Prénom(s) & Nom</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name ?? '') }}">
                                                            @error('name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Rôle --}}
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Rôle</label>
                                                        <div class="form-control-wrap">
                                                            <select name="role" class="form-control @error('role') is-invalid @enderror">
                                                                <option value="{{ old('role', $user->role ?? '') }}">{{ old('role', $user->role ?? 'Sélectionnez un rôle...') }}</option>
                                                                @foreach($roles as $role)
                                                                    <option value="{{ $role }}">{{ $role }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('role')
                                                            <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Email --}}
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Email</label>
                                                        <div class="form-control-wrap">
                                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}">
                                                            @error('email')
                                                            <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Mot de passe --}}
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Mot de passe</label>
                                                        <div class="form-control-wrap">
                                                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" value="{{ request()->is('admin/compte/create') ? '' : $user->password }}">
                                                            @error('password')
                                                            <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="preview-hr">

                                            {{-- Boutons --}}
                                            <div class="col-md-12 d-flex justify-content-center">
                                                <div class="form-group mx-3">
                                                    <button type="reset" class="btn btn-lg btn-outline-primary">
                                                        <i class="align-middle" data-feather="refresh-ccw"></i> Annuler
                                                    </button>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-lg btn-primary text-white"
                                                        @if(request()->is('admin/compte*/edit'))
                                                            onclick="return confirm('Voulez-vous effectuer la modification ?')"
                                                        @endif
                                                    >
                                                        <i class="align-middle" data-feather="{{ request()->is('admin/compte*/edit') ? 'edit-3' : 'save' }}"></i>
                                                        {{ request()->is('admin/compte*/edit') ? 'Modifier' : 'Enregistrer' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
@endsection
                