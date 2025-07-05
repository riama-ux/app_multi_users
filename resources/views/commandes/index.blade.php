@extends('pages.admin.shared.layout')

@section('content')
    <h1>Liste des commandes</h1>

    <a href="{{ route('commandes.create') }}" class="btn btn-primary mb-3">Nouvelle commande</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fournisseur</th>
                <th>Date commande</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($commandes as $commande)
                <tr>
                    <td>{{ $commande->id }}</td>
                    <td>{{ $commande->fournisseur->nom ?? 'N/A' }}</td>
                    <td>{{ $commande->date_commande->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($commande->statut) }}</td>
                    <td>
                        <a href="{{ route('commandes.show', $commande) }}" class="btn btn-info btn-sm">Voir</a>
                        @if ($commande->statut !== 'livree')
                            <a href="{{ route('commandes.edit', $commande) }}" class="btn btn-warning btn-sm">Modifier</a>
                            <form action="{{ route('commandes.destroy', $commande) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Confirmer la suppression ?')" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune commande trouvée.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $commandes->links() }}
@endsection
