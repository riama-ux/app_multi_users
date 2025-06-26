@extends('pages.admin.shared.layout')

@section('content')
<h3>Liste des transferts</h3>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<a href="{{ route('module.transferts.create') }}" class="btn btn-primary mb-3">Nouveau transfert</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Source</th>
            <th>Destination</th>
            <th>Date</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transferts as $transfert)
            <tr>
                <td>{{ $transfert->id }}</td>
                <td>{{ $transfert->magasinSource->nom ?? '-' }}</td>
                <td>{{ $transfert->magasinDestination->nom ?? '-' }}</td>
                <td>{{ $transfert->date_transfert }}</td>
                <td>{{ ucfirst($transfert->statut) }}</td>
                <td>
                    <a href="{{ route('module.transferts.show', ['transfert' => $transfert->id]) }}" class="btn btn-info btn-sm">Voir</a>
                    @if($transfert->magasin_source_id == session('magasin_actif_id') && $transfert->statut == 'en attente')
                        <a href="{{ route('module.transferts.edit', ['transfert' => $transfert->id]) }}" class="btn btn-warning btn-sm">Modifier</a>

                        <form action="{{ route('module.transferts.destroy', ['transfert' => $transfert->id]) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Confirmer suppression ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Aucun transfert trouvé.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $transferts->links() }}
@endsection
