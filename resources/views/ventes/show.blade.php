@extends('pages.admin.shared.layout')

@section('content')
<h1>Détails de la vente #{{ $vente->id }}</h1>

<p><strong>Client:</strong> {{ $vente->client->nom ?? 'N/A' }}</p>
<p><strong>Date:</strong> {{ $vente->date_vente->format('d/m/Y H:i') }}</p>
<p><strong>Mode de paiement:</strong> 
    @switch($vente->mode_paiement)
        @case('especes') Espèces @break
        @case('mobile_money') Mobile Money @break
        @case('virement') Virement @break
        @case('cheque') Chèque @break
        @default {{ ucfirst($vente->mode_paiement) }}
    @endswitch
</p>
<p><strong>Statut:</strong> {{ ucfirst($vente->statut) }}</p>

<h3>Lignes de vente</h3>
<table class="table">
  <thead>
    <tr>
      <th>Produit</th>
      <th>Quantité</th>
      <th>Prix unitaire</th>
      <th>Total</th>
      <th>Lot ID</th>
    </tr>
  </thead>
  <tbody>
    @if($vente->ligneVentes->count())
        @foreach($vente->ligneVentes as $ligne)
        <tr>
        <td>{{ $ligne->produit->nom ?? 'N/A' }}</td>
        <td>{{ $ligne->quantite }}</td>
        <td>{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} FCFA</td>
        <td>{{ number_format($ligne->prix_total, 2, ',', ' ') }} FCFA</td>
        <td>{{ $ligne->lot_id }}</td>
        </tr>
        @endforeach
    @else
    <tr>
        <td colspan="5">Aucune ligne de vente enregistrée.</td>
    </tr>
    @endif
  </tbody>
</table>

<h3>Paiements</h3>
<table class="table">
  <thead>
    <tr>
      <th>Montant</th>
      <th>Mode</th>
      <th>Date</th>
      <th>Encaisseur</th>
    </tr>
  </thead>
  <tbody>
    @if($vente->paiements->count())
        @foreach($vente->paiements as $paiement)
        <tr>
        <td>{{ number_format($paiement->montant, 2, ',', ' ') }} FCFA</td>
        <td>{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</td>
        <td>{{ $paiement->date_paiement->format('d/m/Y H:i') }}</td>
        <td>{{ $paiement->user->name ?? 'N/A' }}</td>
        </tr>
        @endforeach
    @else
    <tr>
        <td colspan="4">Aucun paiement enregistré.</td>
    </tr>
    @endif
  </tbody>
</table>

<a href="{{ route('ventes.index') }}" class="btn btn-secondary">Retour à la liste</a>
<a href="{{ route('ventes.edit', $vente) }}" class="btn btn-warning">Modifier</a>
@endsection
