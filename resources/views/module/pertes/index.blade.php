@extends('pages.admin.shared.layout')

@section('content')
<div class="container">
    <h1>Liste des pertes</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('module.pertes.create') }}" class="btn btn-primary mb-3">Ajouter une perte</a>

    @if($pertes->count())
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Motif</th>
                <th>Date</th>
                <th>Utilisateur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pertes as $perte)
            <tr>
                <td>{{ $perte->produit->nom ?? 'N/A' }}</td>
                <td>{{ $perte->quantite }}</td>
                <td>{{ $perte->motif }}</td>
                <td>{{ $perte->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $perte->user->name ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('module.pertes.edit', $perte->id) }}" class="btn btn-sm btn-warning">Modifier</a>

                    <form action="{{ route('module.pertes.destroy', $perte->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Confirmer la suppression ?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $pertes->links() }}

    @else
        <p>Aucune perte enregistrée.</p>
    @endif
</div>
@endsection
