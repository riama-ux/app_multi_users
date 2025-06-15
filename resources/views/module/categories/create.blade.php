@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Ajouter une catégorie</h4>

        @include('flash-message')

        <form action="{{ route('module.categories.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Nom de la catégorie</label>
                <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                @error('nom')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-flex justify-content-center mt-4">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="{{ route('module.categories.index') }}" class="btn btn-outline-secondary ms-2">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
