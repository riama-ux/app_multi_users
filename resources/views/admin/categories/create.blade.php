@extends('pages.admin.shared.layout')

@section('content')
<div class="container">
    <h2>Créer une catégorie</h2>

    <form method="POST" action="{{ route('admin.categories.store') }}">
        @csrf

        <div class="mb-3">
            <label for="nom" class="form-label">Nom de la catégorie</label>
            <input type="text" name="nom" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="magasin_id" class="form-label">Magasin</label>
            <select name="magasin_id" class="form-select" required>
                @foreach($magasins as $magasin)
                    <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary" type="submit">Enregistrer</button>
    </form>
</div>
@endsection
