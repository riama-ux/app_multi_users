
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
                                    <div class="preview-block">
                                        @include('flash-message')
                                        <form action="{{ route('admin.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row gy-4">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Prénom(s) & Nom</label>
                                                        <div class="form-control-wrap">
                                                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $user->name }}">
                                                            @error('name')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Rôle</label>
                                                        <div class="form-control-wrap">
                                                            <input name="role" type="text" class="form-control @error('role') is-invalid @enderror" value="{{ $user->role }}" disabled>
                                                            @error('role')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Email</label>
                                                        <div class="form-control-wrap">
                                                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ $user->email }}" disabled>
                                                            @error('email')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Nouveau mot de passe</label>
                                                        <div class="form-control-wrap">
                                                            <input name="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror">
                                                            @error('new_password')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label class="form-label">Confirmez le mot de passe</label>
                                                        <div class="form-control-wrap">
                                                            <input name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror">
                                                            @error('password_confirmation')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="preview-hr">

                                            <div class="col-md-12 d-flex justify-content-center">
                                                <div class="form-group mx-3">
                                                    <button type="reset" class="btn btn-outline-primary">
                                                        <i class="align-middle" data-feather="refresh-ccw"></i> Annuler
                                                    </button>
                                                </div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary text-white" onclick="return confirm('Voulez-vous effectuer la modification ?')">
                                                        <i class="align-middle" data-feather="edit-3"></i> Modifier
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

@endsection
                