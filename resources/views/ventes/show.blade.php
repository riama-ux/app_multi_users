@extends('pages.admin.shared.layout')

@section('content')
<h1>Détails de la vente #{{ $vente->id }}</h1>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Détails de la Vente #{{ $vente->id }}</h2>
    <div class="btn-group">
        <a href="{{ route('retours_clients.create', ['vente_id' => $vente->id]) }}" class="btn btn-primary">
            <i class="fas fa-undo"></i> Enregistrer un retour
        </a>
        </div>
</div>



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
      <th></th>
    </tr>
  </thead>
  <tbody>
    @if($vente->paiements->count())
        @foreach($vente->paiements as $paiement)
        <tr @if($paiement->annule) class="table-secondary" @endif>
        <td>{{ number_format($paiement->montant, 2, ',', ' ') }} FCFA</td>
        <td>{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</td>
        <td>{{ $paiement->date_paiement->format('d/m/Y H:i') }}</td>
        <td>{{ $paiement->user->name ?? 'N/A' }}</td>
        <td>
            @if(!$paiement->annule)
            <form action="{{ route('paiements.annuler', $paiement) }}" method="POST" onsubmit="return confirm('Confirmer l\'annulation de ce paiement ?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
            </form>
            @else
            <span class="text-muted">Annulé</span>
            @endif
        </td>
        </tr>
        @endforeach
    @else
    <tr>
        <td colspan="4">Aucun paiement enregistré.</td>
    </tr>
    @endif
  </tbody>
</table>

@if($vente->reste_a_payer > 0)
<hr>
<h4>Ajouter un paiement</h4>
<form action="{{ route('paiements.store', $vente) }}" method="POST" class="row g-2 align-items-end">
    @csrf
    <div class="col-md-3">
        <label for="montant" class="form-label">Montant</label>
        <input type="number" name="montant" id="montant" class="form-control" max="{{ $vente->reste_a_payer }}" min="1" required>
    </div>
    <div class="col-md-3">
        <label for="mode_paiement" class="form-label">Mode de paiement</label>
        <select name="mode_paiement" class="form-select" required>
            <option value="especes">Espèces</option>
            <option value="mobile_money">Mobile Money</option>
            <option value="virement">Virement</option>
            <option value="cheque">Chèque</option>
            <option value="autre">Autre</option>
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-success">Valider le paiement</button>
    </div>
</form>
@endif


<a href="{{ route('ventes.index') }}" class="btn btn-secondary">Retour à la liste</a>
<a href="{{ route('ventes.edit', $vente) }}" class="btn btn-warning">Modifier</a>
<a href="{{ route('ventes.retour.create', $vente) }}" class="btn btn-info">Retourner des produits</a>
@endsection
