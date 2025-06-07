@extends('pages.admin.shared.layout')
@section('content')

<div class="d-flex justify-content-between mb-3 h3">
    <div class="col-8 d-flex justify-content-between">
        <strong>Utilisateur</strong>
    </div>
    <div class="text-danger">

    </div>
</div>

<div class="row">
    <div class="col-8">

        <div class="card">
            @include('flash-message')
            <div class="card-body">
                <form action="{{ request()->is('admin/compte/create') ? route('admin.compte.store') : route('admin.compte.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method(request()->is('admin/compte*/edit') ? 'PUT' : 'POST')

                    <div class="mb-3 row">
                        <div class="col col-3">
                            Prénom(s) & Nom
                        </div>
                        <div class="col">
                            <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ request()->is('admin/compte/create') ? '' : $user->name }}">
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col col-3">
                            Role 
                        </div>
                        <div class="col">
                            <select name="role" class="form-control @error('role') is-invalid @enderror">
                                <option value="{{ isset($user) ? $user->role : '' }}">{{ isset($user) ? $user->role : '' }}</option>
                                @for($i = 0; $i < count($roles); $i++) <option value="{{ $roles[$i] }}">{{ $roles[$i] }}</option>
                                    @endfor
                            </select>
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
                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ request()->is('admin/compte/create') ? '' : $user->email }}">
                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col col-3">
                            Mot de passe
                        </div>
                        <div class="col">
                            <input name="password" type="password" class="form-control @error('password') is-invalid @enderror" value="{{ request()->is('admin/compte/create') ? '' : $user->password }}">
                            @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        @if(request()->is('admin/compte/create'))
                        <div class="col text-end">
                            <button type="reset" class="btn btn-outline-primary"><i class="align-middle" data-feather="refresh-ccw"></i> Annuler</button>
                            <button type="submit" class="btn btn-primary text-white"><i class="align-middle" data-feather="save"></i> Enregistrer</button>
                        </div>
                        @endif
                        @if(request()->is('admin/compte*/edit'))
                        <div class="col text-end">
                            <button type="reset" class="btn btn-outline-primary"><i class="align-middle" data-feather="refresh-ccw"></i> Annuler</button>
                            <button type="submit" class="btn btn-primary text-white" onclick="return confirm ('Voulez-vous effectuer la modification ?')"><i class="align-middle" data-feather="edit-3"></i> Modifier</button>
                        </div>
                        @endif
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

@endsection