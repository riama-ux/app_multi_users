@extends('pages.admin.shared.layout')

@section('content')
    <h3>Modifier un crédit</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('module.credits.update', $credit->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Client</label>
            <input type="text" class="form-control" value="{{ $credit->client->nom ?? 'N/A' }}" disabled>
        </div>

        <div class="mb-3">
            <label>Montant dû</label>
            <input type="text" class="form-control" value="{{ number_format($credit->montant) }} FCFA" disabled>
        </div>

        <div class="mb-3">
            <label for="statut">Statut</label>
            <select name="statut" class="form-select" required>
                <option value="non payé" {{ $credit->statut === 'non payé' ? 'selected' : '' }}>Non payé</option>
                <option value="payé" {{ $credit->statut === 'payé' ? 'selected' : '' }}>Payé</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="echeance">Date d’échéance (facultative)</label>
            <input type="date" name="echeance" class="form-control" value="{{ old('echeance', $credit->echeance) }}">
        </div>

        <button class="btn btn-success">Mettre à jour</button>
        <a href="{{ route('module.credits.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
@endsection

