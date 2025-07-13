@extends('pages.admin.shared.layout')

@section('content')

    <div class="nk-block-head-content d-flex justify-content-between align-items-center mb-4">
        <h3 class="nk-block-title">Liste des catégories</h3>
        <a href="{{ route('module.categories.create') }}" class="btn btn-primary">
            <em class="icon ni ni-plus"></em><span>Ajouter une catégorie</span>
        </a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $categorie)
                <tr>
                    <td>{{ $categorie->nom }}</td>
                    <td>
                        <a href="{{ route('module.categories.edit', ['categorie' => $categorie->id]) }}" class="btn btn-sm btn-info">Modifier</a>


                        <form action="{{ route('module.categories.destroy', ['categorie' => $categorie->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette catégorie ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Aucune catégorie pour ce magasin.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $categories->links() }}
@endsection
