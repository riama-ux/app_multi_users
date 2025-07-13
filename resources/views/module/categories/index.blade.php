@extends('pages.admin.shared.layout')

@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Liste des catégories</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('module.categories.create') }}" class="btn btn-primary d-flex align-items-center">
                                <em class="icon ni ni-plus"></em><span>Ajouter une catégorie</span>
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
                                        <th class="nk-tb-col nk-tb-col-tools text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $categorie)
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col">{{ $categorie->nom }}</td>
                                            <td class="nk-tb-col nk-tb-col-tools">
                                                <ul class="nk-tb-actions gx-1">
                                                    <li>
                                                        <a href="{{ route('module.categories.edit', ['categorie' => $categorie->id]) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                                            <em class="icon ni ni-edit-alt"></em>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('module.categories.destroy', ['categorie' => $categorie->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette catégorie ?')">
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
                                            <td class="nk-tb-col text-center" colspan="2">
                                                <div class="py-5">
                                                    <p>Aucune catégorie pour ce magasin.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card-inner-sm">
                    <div class="d-flex justify-content-center">
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection