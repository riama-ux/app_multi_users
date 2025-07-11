@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid">
    <h1>Liste des Ajustements de Stock</h1>

    {{-- Section de filtrage --}}
    <div class="card mb-4">
        <div class="card-header">Filtres</div>
        <div class="card-body">
            <form method="GET" action="{{ route('ajustements.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Type d'ajustement</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">Tous</option>
                            <option value="entree" {{ request('type') == 'entree' ? 'selected' : '' }}>Entrée</option>
                            <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>Sortie</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Appliquer les filtres</button>
                        <a href="{{ route('ajustements.index') }}" class="btn btn-secondary">Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>
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

    <a href="{{ route('ajustements.create') }}" class="btn btn-primary mb-3">Nouvel Ajustement</a>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date d'Ajustement</th>
                    <th>Type</th>
                    <th>Motif Global</th>
                    <th>Utilisateur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($ajustements as $ajustement)
                    <tr>
                        <td>{{ $ajustement->id }}</td>
                        <td>{{ $ajustement->date_ajustement->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($ajustement->type == 'entree')
                                <span class="badge bg-success">Entrée</span>
                            @else
                                <span class="badge bg-danger">Sortie</span>
                            @endif
                        </td>
                        <td>{{ $ajustement->motif_global ?? 'N/A' }}</td>
                        <td>{{ $ajustement->user->name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('ajustements.show', $ajustement->id) }}" class="btn btn-info btn-sm" title="Voir les détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('ajustements.edit', $ajustement->id) }}" class="btn btn-warning btn-sm" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('ajustements.destroy', $ajustement->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet ajustement ? Cette action est irréversible et annulera les mouvements de stock associés.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucun ajustement de stock trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $ajustements->links() }}
    </div>
</div>
@endsection
