@extends('pages.admin.shared.layout')


@section('content')
<div class="container">
    <h2>Liste des transferts</h2>

    <a href="{{ route('transferts.create') }}" class="btn btn-primary mb-3">Nouveau Transfert</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Destination</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transferts as $transfert)
                <tr>
                    <td>{{ $transfert->date_transfert }}</td>
                    <td>{{ $transfert->magasinSource->nom }}</td>
                    <td>{{ $transfert->magasinDestination->nom }}</td>
                    <td>{{ ucfirst($transfert->statut) }}</td>
                    <td>
                        <a href="{{ route('transferts.show', $transfert) }}" class="btn btn-info btn-sm">Voir</a>
                        @if($transfert->statut === 'attente' && session('magasin_actif_id') == $transfert->magasin_source_id)
                            <a href="{{ route('transferts.edit', $transfert) }}" class="btn btn-warning btn-sm">Modifier</a>
                            <form action="{{ route('transferts.destroy', $transfert) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</button>
                            </form>
                        @endif
                        @if($transfert->statut === 'attente' && session('magasin_actif_id') == $transfert->magasin_destination_id)
                            <form action="{{ route('transferts.valider', $transfert) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-success btn-sm" onclick="return confirm('Valider le transfert ?')">Valider</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $transferts->links() }}
</div>
@endsection
