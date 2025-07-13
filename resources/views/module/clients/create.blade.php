@extends('pages.admin.shared.layout')

@section('content')

<div class="nk-block nk-block-lg">
    
    <div class="nk-block-head-content d-flex justify-content-between align-items-center mb-4">
        <h1 class="nk-block-title">Ajouter un client</h1>
        <a href="{{ route('module.clients.index') }}" class="btn btn-outline-primary d-none d-sm-inline-flex go-back-btn">
            <em class="icon ni ni-arrow-left"></em><span>Retour à la liste des clients</span>
        </a>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Erreurs de validation :</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('module.clients.store') }}" method="POST" class="form-validate">
                @csrf

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label" for="nom">Nom <span class="text-danger">*</span></label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="nom" name="nom" required value="{{ old('nom') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label" for="telephone">Téléphone</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="telephone" name="telephone" value="{{ old('telephone') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <div class="form-control-wrap">
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label" for="adresse">Adresse</label>
                            <div class="form-control-wrap">
                                <textarea class="form-control" id="adresse" name="adresse">{{ old('adresse') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-primary">Enregistrer le client</button>
                            <a href="{{ route('module.clients.index') }}" class="btn btn-lg btn-outline-secondary">Annuler</a>
                        </div>
                    </div>
                </div>
            </form>
        </div></div></div>@endsection