@extends('pages.admin.shared.layout')

@section('content')
    <h3>Liste des crédits (magasin actif)</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Montant</th>
                <th>Statut</th>
                <th>Échéance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($credits as $credit)
                <tr>
                    <td>{{ $credit->created_at->format('d/m/Y') }}</td>
                    <td>{{ $credit->client->nom ?? '-' }}</td>
                    <td>{{ number_format($credit->montant) }} FCFA</td>
                    <td>
                        <span class="badge bg-{{ $credit->statut === 'payé' ? 'success' : 'warning' }}">
                            {{ ucfirst($credit->statut) }}
                        </span>
                    </td>
                    <td>{{ $credit->echeance ? \Carbon\Carbon::parse($credit->echeance)->format('d/m/Y') : '-' }}</td>
                    <td>
                        <a href="{{ route('credits.edit', $credit->id) }}" class="btn btn-sm btn-primary">Modifier</a>
                        <form action="{{ route('credits.destroy', $credit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce crédit ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Aucun crédit trouvé.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $credits->links() }}
@endsection

