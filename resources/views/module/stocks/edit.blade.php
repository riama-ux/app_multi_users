@extends('pages.admin.shared.layout')

@section('content')
    <h3>Modifier le stock</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('module.stocks.update', $stock->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Produit</label>
            <input type="text" value="{{ $stock->produit->nom }}" class="form-control" disabled>
        </div>

        <div class="mb-3">
            <label for="quantite">Quantité</label>
            <input type="number" name="quantite" value="{{ old('quantite', $stock->quantite) }}" class="form-control" required>
        </div>

        <button class="btn btn-primary">Modifier</button>
        <a href="{{ route('module.stocks.index') }}" class="btn btn-secondary">Retour</a>
    </form>
@endsection

