@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Ajouter un client</h4>

        @include('flash-message')

        <form action="{{ route('module.clients.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                {{-- Nom --}}
                <div class="col-md-6">
                    <label class="form-label">Nom du client</label>
                    <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                    @error('nom') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Téléphone --}}
                <div class="col-md-6">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control @error('telephone') is-invalid @enderror" value="{{ old('telephone') }}">
                    @error('telephone') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Adresse --}}
                <div class="col-md-6">
                    <label class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-control @error('adresse') is-invalid @enderror" value="{{ old('adresse') }}">
                    @error('adresse') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('module.clients.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
