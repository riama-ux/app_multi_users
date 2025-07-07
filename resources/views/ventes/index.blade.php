@extends('pages.admin.shared.layout')

@section('content')
<h1>Liste des ventes</h1>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="GET" action="{{ route('ventes.index') }}" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="statut" class="form-select" onchange="this.form.submit()">
            <option value="">-- Tous les statuts --</option>
            <option value="payee" {{ request('statut') == 'payee' ? 'selected' : '' }}>Payée</option>
            <option value="partielle" {{ request('statut') == 'partielle' ? 'selected' : '' }}>Partielle</option>
            <option value="credit" {{ request('statut') == 'credit' ? 'selected' : '' }}>Crédit</option>
            <option value="retournee" {{ request('statut') == 'retournee' ? 'selected' : '' }}>Retournée</option>
        </select>
    </div>
</form>


<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Client</th>
      <th>Date</th>
      <th>Total TTC</th>
      <th>Montant payé</th>
      <th>Reste à payer</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    @foreach($ventes as $vente)
    <tr>
      <td>{{ $vente->id }}</td>
      <td>{{ $vente->client->nom ?? 'N/A' }}</td>
      <td>{{ $vente->date_vente->format('d/m/Y H:i') }}</td>
      <td>{{ number_format($vente->total_ttc, 2, ',', ' ') }} FCFA</td>
      <td>{{ number_format($vente->montant_paye, 2, ',', ' ') }} FCFA</td>
      <td>{{ number_format($vente->reste_a_payer, 2, ',', ' ') }} FCFA</td>
      <td>
        @if($vente->statut === 'payee')
          <span class="badge bg-success">Payée</span>
        @elseif($vente->statut === 'partielle')
          <span class="badge bg-warning">Partielle</span>
        @elseif($vente->statut === 'credit')
          <span class="badge bg-danger">Crédit</span>
        @elseif($vente->statut === 'retournee')
          <span class="badge bg-info">Retournée</span>
        @else
          <span class="badge bg-secondary">{{ ucfirst($vente->statut) }}</span>
        @endif
      </td>
      <td>
        <a href="{{ route('ventes.show', $vente) }}" class="btn btn-sm btn-primary">Voir</a>
        <a href="{{ route('ventes.edit', $vente) }}" class="btn btn-sm btn-warning">Modifier</a>
        {{-- Suppression interdite, message ou lien vers retour client --}}
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
{{ $ventes->appends(request()->query())->links() }}

@endsection
