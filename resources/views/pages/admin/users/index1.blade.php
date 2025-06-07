@extends('pages.admin.shared.layout')
@section('content')

<div class="d-flex justify-content-between mb-3 h3">
    <div class="col-8 d-flex justify-content-between">
        <strong>Utilisateurs</strong>
    </div>
    <div class="text-danger">
        {{ number_format($rows, 0, '.', ' ') }}
    </div>
</div>

<div class="row">
    <div class="col">

        <div class="card">
            @include('flash-message')
            <div class="card-header">

                <form class="navbar-search" method="get" action="{{ route('admin.compte.search') }}">
                @include('pages.admin.shared.compteSearchBar')
                </form>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="label label-default">Nom utilisateur</th>
                            <th class="label label-default">Rôle</th>
                            <th class="label label-default">Email</th>
                            <th class="text-end"><a href="{{ route('admin.compte.create') }}"><i class="align-middle" data-feather="plus"></i> Nouveau</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $row)
                        <tr>
                            <form action="{{ route('admin.compte.destroy', $row->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <td class="col">
                                    <strong>{{ $row->name }}<span class="text-danger">{{ $row->numero }}</span></strong>
                                </td>
                                <td class="col "><strong>{{ $row->role }}</strong></td>
                                <td class="col">{{ $row->email }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.compte.edit', encrypt($row->id)) }}" class=""><i class="align-middle" data-feather="edit-3"></i>Modifier</a>
                                    <button type="submit" onclick="return confirm('Voulez-vous supprimer le compte ?') " class="btn text-danger"><i class="align-middle" data-feather="x"></i>Supprimer</button>
                                </td>
                            </form>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center h6 text-danger">Aucun enregistrement trouvé !</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    @if(request()->is('admin/compte'))
                    {!! $users->links() !!}
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@endsection