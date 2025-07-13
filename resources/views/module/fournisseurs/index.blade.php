@extends('pages.admin.shared.layout')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="nk-block-title">Liste des fournisseurs</h3>
        <a href="{{ route('module.fournisseurs.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center">
            <em class="icon ni ni-plus me-2"></em> Nouveau fournisseur
        </a>
</div>
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
                    <a href="{{ route('module.fournisseurs.edit', ['fournisseur' => $fournisseur->id]) }}" class="btn btn-sm btn-warning">Modifier</a>
                    <a href="{{ route('module.fournisseurs.show', ['fournisseur' => $fournisseur->id]) }}" class="btn btn-sm btn-info">Voir</a>

                    <form action="{{ route('module.fournisseurs.destroy', ['fournisseur' => $fournisseur->id]) }}" method="POST" style="display:inline-block"
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
