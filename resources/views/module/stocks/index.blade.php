@extends('pages.admin.shared.layout')

@section('content')
<div class="card card-preview">
    <div class="card-inner">
        <h4 class="mb-4">Stocks par magasin</h4>

        @include('flash-message')

        @forelse ($magasins as $magasin)
            <div class="card mb-4 border border-primary">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-primary">{{ $magasin->nom }}</h6>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($magasin->stocks as $stock)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $stock->produit->nom }}</td>
                                        <td>{{ $stock->quantite }}</td>
                                        <td>
                                            <a href="{{ route('module.stocks.edit', $stock->id) }}" class="btn btn-sm btn-outline-primary">
                                                <em class="icon ni ni-edit-alt"></em> Modifier
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-danger">Aucun stock trouvé.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning">Aucun magasin disponible.</div>
        @endforelse
    </div>
</div>
@endsection
