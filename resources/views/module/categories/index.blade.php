@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Liste des catégories</h4>
            <a href="{{ route('module.categories.create') }}" class="btn btn-primary">
                <em class="icon ni ni-plus"></em> Nouvelle catégorie
            </a>
        </div>

        @include('flash-message')

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Nom</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $categorie)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $categorie->nom }}</td>
                            <td>
                                <a href="{{ route('module.categories.edit', $categorie->id) }}" class="btn btn-sm btn-outline-info">
                                    <em class="icon ni ni-edit"></em>
                                </a>
                                <form action="{{ route('module.categories.destroy', $categorie->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?')">
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
                            <td colspan="3" class="text-center text-danger">Aucune catégorie trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
