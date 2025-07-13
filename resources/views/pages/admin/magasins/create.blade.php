@extends('pages.admin.shared.layout')

@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Ajouter un magasin</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('admin.magasins.index') }}" class="btn btn-outline-primary d-flex align-items-center">
                                <em class="icon ni ni-list"></em><span>Liste des magasins</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.magasins.store') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="nom">Nom du magasin *</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="nom" id="nom" class="form-control" required value="{{ old('nom') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="adresse">Adresse</label>
                                            <div class="form-control-wrap">
                                                <input type="text" name="adresse" id="adresse" class="form-control" value="{{ old('adresse') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary me-2">Enregistrer</button>
                                        <a href="{{ route('admin.magasins.index') }}" class="btn btn-outline-secondary">Annuler</a>
                                    </div>
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection