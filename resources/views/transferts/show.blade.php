@extends('pages.admin.shared.layout')



@section('content')
<div class="container">
    <h2>Détails du Transfert #{{ $transfert->id }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Magasin Source :</strong> {{ $transfert->magasinSource->nom }}</p>
            <p><strong>Magasin Destination :</strong> {{ $transfert->magasinDestination->nom }}</p>
            <p><strong>Date :</strong> {{ $transfert->date_transfert->format('d/m/Y') }}</p>
            <p><strong>Utilisateur :</strong> {{ $transfert->user->name ?? 'N/A' }}</p>
            <p><strong>Statut :</strong>
                @if($transfert->statut === 'envoye')
                    <span class="badge bg-success">Validé</span>
                @elseif($transfert->statut === 'refuse')
                    <span class="badge bg-danger">Refusé</span>
                @else
                    <span class="badge bg-warning text-dark">En attente</span>
                @endif
            </p>
        </div>
    </div>

    <h5>Produits transférés</h5>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transfert->ligneTransferts as $ligne)
                <tr>
                    <td>{{ $ligne->produit->nom }}</td>
                    <td>{{ $ligne->quantite }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if($transfert->statut === 'attente' && session('magasin_actif_id') == $transfert->magasin_destination_id)
    <form action="{{ route('transferts.valider', $transfert->id) }}" method="POST" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-success" onclick="return confirm('Confirmer la réception du transfert ?')">
            Réceptionner
        </button>
    </form>
@endif


    <a href="{{ route('transferts.index') }}" class="btn btn-secondary mt-3">Retour</a>
</div>
@endsection

