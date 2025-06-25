@extends('pages.admin.shared.layout')

@section('content')
    <h3>Clients du magasin actif</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('module.clients.create') }}" class="btn btn-primary mb-3">Nouveau client</a>

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
            @forelse ($clients as $client)
                <tr>
                    <td>{{ $client->nom }}</td>
                    <td>{{ $client->telephone }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->adresse }}</td>
                    <td>
                        <a href="{{ route('module.clients.edit', $client->id) }}" class="btn btn-sm btn-info">Modifier</a>
                        <form action="{{ route('module.clients.destroy', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce client ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun client enregistré.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $clients->links() }}
@endsection

