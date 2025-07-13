@extends('pages.admin.shared.layout')

@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Liste des fournisseurs</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('module.fournisseurs.create') }}" class="btn btn-primary d-flex align-items-center">
                                <em class="icon ni ni-plus"></em><span>Nouveau fournisseur</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="nk-block nk-block-lg">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table class="nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                                <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col"><span>Nom</span></th>
                                        <th class="nk-tb-col tb-col-md"><span>Téléphone</span></th>
                                        <th class="nk-tb-col tb-col-md"><span>Email</span></th>
                                        <th class="nk-tb-col tb-col-lg"><span>Adresse</span></th>
                                        <th class="nk-tb-col nk-tb-col-tools text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($fournisseurs as $fournisseur)
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col">{{ $fournisseur->nom }}</td>
                                            <td class="nk-tb-col tb-col-md">{{ $fournisseur->telephone }}</td>
                                            <td class="nk-tb-col tb-col-md">{{ $fournisseur->email }}</td>
                                            <td class="nk-tb-col tb-col-lg">{{ $fournisseur->adresse }}</td>
                                            <td class="nk-tb-col nk-tb-col-tools">
                                                <ul class="nk-tb-actions gx-1">
                                                    <li>
                                                        <a href="{{ route('module.fournisseurs.edit', ['fournisseur' => $fournisseur->id]) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                                            <em class="icon ni ni-edit-alt"></em>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('module.fournisseurs.show', ['fournisseur' => $fournisseur->id]) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir">
                                                            <em class="icon ni ni-eye"></em>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('module.fournisseurs.destroy', ['fournisseur' => $fournisseur->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce fournisseur ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer">
                                                                <em class="icon ni ni-trash"></em>
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col text-center" colspan="5">
                                                <div class="py-5">
                                                    <p>Aucun fournisseur trouvé.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- <div class="card-inner-sm">
                    <div class="d-flex justify-content-center">
                        {{ $fournisseurs->links('pagination::bootstrap-5') }}
                    </div>
                </div> --}}

            </div>
        </div>
    </div>
</div>

@endsection