@extends('pages.admin.shared.layout')

@section('content')
    <h3>Liste des magasins</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.magasins.create') }}" class="btn btn-primary mb-3">Ajouter un magasin</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($magasins as $magasin)
                <tr>
                    <td>{{ $magasin->nom }}</td>
                    <td>{{ $magasin->adresse ?? '—' }}</td>
                    <td>
                        <a href="{{ route('admin.magasins.edit', $magasin->id) }}" class="btn btn-sm btn-warning">Modifier</a>

                        <form action="{{ route('admin.magasins.destroy', $magasin->id) }}" method="POST" style="display:inline-block"
                            onsubmit="return confirm('Confirmer la suppression de ce magasin ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center">Aucun magasin trouvé.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
