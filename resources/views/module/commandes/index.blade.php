@extends('pages.admin.shared.layout')

@section('content')
    

@section('content')


    <h3>Détails de la commande</h3>

    <!-- Reste de ton contenu -->


    <h3>Commandes fournisseurs</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('module.commandes.create') }}" class="btn btn-primary mb-3">Nouvelle commande</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Fournisseur</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Par</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($commandes as $cmd)
                <tr>
                    <td>{{ $cmd->fournisseur->nom ?? '-' }}</td>
                    <td>{{ $cmd->date_commande }}</td>
                    <td>
                        <span class="badge bg-{{ $cmd->statut === 'livrée' ? 'success' : 'warning' }}">
                            {{ ucfirst($cmd->statut) }}
                        </span>
                    </td>
                    <td>{{ $cmd->user->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('module.commandes.show', $cmd->id) }}" class="btn btn-sm btn-info">Voir</a>
                        <form action="{{ route('module.commandes.destroy', $cmd->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune commande enregistrée.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $commandes->links() }}
@endsection

