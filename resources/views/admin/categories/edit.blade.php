@extends('pages.admin.shared.layout')

@section('content')
<div class="container">
    <h2>Modifier la catégorie</h2>

    <form method="POST" action="{{ route('admin.categories.update', $category) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nom" class="form-label">Nom de la catégorie</label>
            <input type="text" name="nom" class="form-control" value="{{ $category->nom }}" required>
        </div>

        <div class="mb-3">
            <label for="magasin_id" class="form-label">Magasin</label>
            <select name="magasin_id" class="form-select" required>
                @foreach($magasins as $magasin)
                    <option value="{{ $magasin->id }}" {{ $category->magasin_id == $magasin->id ? 'selected' : '' }}>{{ $magasin->nom }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-success" type="submit">Mettre à jour</button>
    </form>
</div>
@endsection
