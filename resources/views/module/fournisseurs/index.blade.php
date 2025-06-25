@extends('pages.admin.shared.layout')

@section('content')
<h3>Liste des fournisseurs</h3>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('module.fournisseurs.create') }}" class="btn btn-primary mb-3">Ajouter un fournisseur</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Adresse</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($fournisseurs as $fournisseur)
            <tr>
                <td>{{ $fournisseur->nom }}</td>
                <td>{{ $fournisseur->telephone }}</td>
                <td>{{ $fournisseur->email }}</td>
                <td>{{ $fournisseur->adresse }}</td>
                <td>
                    <a href="{{ route('module.fournisseurs.edit', $fournisseur->id) }}" class="btn btn-sm btn-warning">Modifier</a>
                    <a href="{{ route('module.fournisseurs.show', $fournisseur->id) }}" class="btn btn-sm btn-info">Voir</a>

                    <form action="{{ route('module.fournisseurs.destroy', $fournisseur->id) }}" method="POST" style="display:inline-block"
                        onsubmit="return confirm('Supprimer ce fournisseur ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center">Aucun fournisseur trouvé.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
