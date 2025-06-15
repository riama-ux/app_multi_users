@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Liste des fournisseurs</h4>
            <a href="{{ route('module.fournisseurs.create') }}" class="btn btn-primary">
                <em class="icon ni ni-plus"></em> Nouveau fournisseur
            </a>
        </div>

        @include('flash-message')

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fournisseurs as $fournisseur)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $fournisseur->nom }}</td>
                            <td>{{ $fournisseur->email ?? '—' }}</td>
                            <td>{{ $fournisseur->telephone ?? '—' }}</td>
                            <td>{{ $fournisseur->adresse ?? '—' }}</td>
                            <td>
                                <a href="{{ route('module.fournisseurs.edit', $fournisseur->id) }}" class="btn btn-sm btn-outline-info">
                                    <em class="icon ni ni-edit"></em>
                                </a>
                                <form action="{{ route('module.fournisseurs.destroy', $fournisseur->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce fournisseur ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <em class="icon ni ni-trash"></em>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-danger">Aucun fournisseur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $fournisseurs->links() }}
        </div>
    </div>
</div>
@endsection
