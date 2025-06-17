@extends('pages.admin.shared.layout')

@section('content')
<div class="container">
    <h2>Catégories</h2>

    @include('flash-message')

    <form method="GET" class="mb-3">
        <label for="magasin_id">Filtrer par magasin :</label>
        <select name="magasin_id" onchange="this.form.submit()" class="form-select">
            @foreach($magasins as $magasin)
                <option value="{{ $magasin->id }}" {{ $magasin_id == $magasin->id ? 'selected' : '' }}>
                    {{ $magasin->nom }}
                </option>
            @endforeach
        </select>
    </form>

    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Nouvelle catégorie</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Magasin</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $categorie)
            <tr>
                <td>{{ $categorie->nom }}</td>
                <td>{{ $categorie->magasin->nom }}</td>
                <td>
                    <a href="{{ route('admin.categories.edit', $categorie) }}" class="btn btn-sm btn-warning">Modifier</a>
                    <form action="{{ route('admin.categories.destroy', $categorie) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Supprimer cette catégorie ?')" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $categories->appends(['magasin_id' => $magasin_id])->links() }}
</div>
@endsection