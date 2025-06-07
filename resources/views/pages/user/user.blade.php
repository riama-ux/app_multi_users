@extends('pages.user.shared.layout')
@section('content')

<div class="d-flex justify-content-between mb-3 h3">
    <div class="col-8 d-flex justify-content-between">
        <strong>Compte utilisateur</strong>
    </div>
    <div class="text-danger">

    </div>
</div>

<div class="row">
    <div class="col-8">

        <div class="card">
            @include('flash-message')
            <div class="card-body">
                <form action="{{ route('user.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3 row">
                        <div class="col col-3">
                            Prénom(s) & Nom
                        </div>
                        <div class="col">
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ $user->name }}">
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col col-3">
                            Rôle
                        </div>
                        <div class="col">
                            <input name="role" type="text" class="form-control @error('role') is-invalid @enderror" value="{{ $user->role }}" disabled>
                            @error('role')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col col-3">
                            Email
                        </div>
                        <div class="col">
                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ $user->email }}" disabled>
                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col col-3">
                            Nouveau Mot de passe
                        </div>
                        <div class="col">
                            <input name="new_password" type="password" class="form-control @error('new_password') is-invalid @enderror">
                            @error('new_password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col col-3">
                            Confirmez le Mot de passe
                        </div>
                        <div class="col">
                            <input name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror">
                            @error('password_confirmation')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col text-end">
                            <button type="reset" class="btn btn-outline-primary"><i class="align-middle" data-feather="refresh-ccw"></i> Annuler</button>
                            <button type="submit" class="btn btn-primary text-white" onclick="return confirm ('Voulez-vous effectuer la modification ?')"><i class="align-middle" data-feather="edit-3"></i> Modifier</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

@endsection