@extends('pages.admin.shared.layout')

@section('content')
<h1>Liste des ventes</h1>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

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
@endsection
