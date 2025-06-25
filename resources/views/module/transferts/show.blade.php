@extends('pages.admin.shared.layout')

@section('content')
<h3>Détails du transfert</h3>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<p><strong>Date :</strong> {{ $transfert->date_transfert }}</p>
<p><strong>Magasin source :</strong> {{ $transfert->magasinSource->nom ?? '-' }}</p>
<p><strong>Magasin destination :</strong> {{ $transfert->magasinDestination->nom ?? '-' }}</p>
<p><strong>Statut :</strong> {{ ucfirst($transfert->statut) }}</p>
<p><strong>Créé par :</strong> {{ $transfert->user->name ?? '-' }}</p>

<hr>

@if($transfert->statut === 'en attente' && $transfert->magasin_destination_id === session('magasin_actif_id'))
    <form action="{{ route('module.transferts.valider', $transfert->id) }}" method="POST" style="margin-bottom: 1rem;">
        @csrf
        <button type="submit" class="btn btn-success" onclick="return confirm('Confirmer la réception de ce transfert ?')">
            Valider la réception
        </button>
    </form>
@endif


<h5>Produits transférés</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Produit</th>
            <th>Quantité</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transfert->lignes as $ligne)
        <tr>
            <td>{{ $ligne->produit->nom ?? '—' }}</td>
            <td>{{ $ligne->quantite }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<a href="{{ route('module.transferts.index') }}" class="btn btn-secondary">Retour à la liste</a>
@endsection
