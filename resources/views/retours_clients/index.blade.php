@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Retours Clients</h1>
        <a href="{{ route('retours_clients.create') }}" class="btn btn-primary">Enregistrer un retour</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Filtres</div>
        <div class="card-body">
            <form action="{{ route('retours_clients.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="client_id" class="form-label">Client</label>
                        <select name="client_id" id="client_id" class="form-select">
                            <option value="">-- Tous les clients --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select name="statut" id="statut" class="form-select">
                            <option value="">-- Tous les statuts --</option>
                            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="traite" {{ request('statut') == 'traite' ? 'selected' : '' }}>Traité</option>
                            <option value="rembourse" {{ request('statut') == 'rembourse' ? 'selected' : '' }}>Remboursé</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                        <a href="{{ route('retours_clients.index') }}" class="btn btn-secondary">Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date Retour</th>
                    <th>Client</th>
                    <th>Vente Associée</th>
                    <th>Montant Remboursé</th>
                    <th>Motif Global</th>
                    <th>Statut</th>
                    <th>Effectué par</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($retours as $retour)
                    <tr>
                        <td>{{ $retour->id }}</td>
                        <td>{{ $retour->date_retour->format('d/m/Y H:i') }}</td>
                        <td>{{ $retour->client->nom ?? 'Client inconnu' }}</td>
                        <td>
                            @if ($retour->vente)
                                <a href="{{ route('ventes.show', $retour->vente->id) }}">Vente #{{ $retour->vente->id }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ number_format($retour->montant_rembourse, 2, ',', ' ') }} FCFA</td>
                        <td>{{ $retour->motif_global ?? 'N/A' }}</td>
                        <td><span class="badge bg-{{ $retour->statut == 'rembourse' ? 'success' : ($retour->statut == 'traite' ? 'info' : 'warning') }}">{{ ucfirst(str_replace('_', ' ', $retour->statut)) }}</span></td>
                        <td>{{ $retour->user->name ?? 'Utilisateur inconnu' }}</td>
                        <td>
                            <a href="{{ route('retours_clients.show', $retour->id) }}" class="btn btn-info btn-sm" title="Voir"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('retours_clients.edit', $retour->id) }}" class="btn btn-warning btn-sm" title="Modifier"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('retours_clients.destroy', $retour->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce retour ? Cette action est irréversible et affectera le stock.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Supprimer"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">Aucun retour client trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $retours->links() }}
    </div>
</div>
@endsection
