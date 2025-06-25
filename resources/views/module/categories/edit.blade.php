@extends('pages.admin.shared.layout')


@section('content')
    <h3>Modifier la catégorie</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('module.categories.update', $categorie->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nom" class="form-label">Nom de la catégorie</label>
            <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom', $categorie->nom) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="{{ route('module.categories.index') }}" class="btn btn-secondary">Retour</a>
    </form>
@endsection

