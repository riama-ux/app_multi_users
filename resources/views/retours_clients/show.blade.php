@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid">
    <h1>Détails du retour client #{{ $retour->id }}</h1>

    <div class="card mb-4">
        <div class="card-header">Informations générales</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Date du retour :</strong> {{ $retour->date_retour->format('d/m/Y H:i') }}</p>
                    <p><strong>Client :</strong> {{ $retour->client->nom ?? 'Client inconnu' }}</p>
                    <p><strong>Vente associée :</strong>
                        @if ($retour->vente)
                            <a href="{{ route('ventes.show', $retour->vente->id) }}">#{{ $retour->vente->id }}</a> ({{ $retour->vente->date_vente->format('d/m/Y') }})
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Effectué par :</strong> {{ $retour->user->name ?? 'Utilisateur inconnu' }}</p>
                    <p><strong>Magasin :</strong> {{ $retour->magasin->nom ?? 'Magasin inconnu' }}</p>
                    <p><strong>Statut :</strong> <span class="badge bg-{{ $retour->statut == 'rembourse' ? 'success' : ($retour->statut == 'traite' ? 'info' : 'warning') }}">{{ ucfirst(str_replace('_', ' ', $retour->statut)) }}</span></p>
                    <p><strong>Montant remboursé :</strong> {{ number_format($retour->montant_rembourse, 2, ',', ' ') }} FCFA</p>
                </div>
            </div>
            <p><strong>Motif global :</strong> {{ $retour->motif_global ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Produits retournés</div>
        <div class="card-body">
            @if ($retour->lignesRetour->isEmpty())
                <p>Aucun produit dans ce retour.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité retournée</th>
                                <th>Prix unitaire retour</th>
                                <th>Motif spécifique</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($retour->lignesRetour as $ligne)
                                <tr>
                                    <td>{{ $ligne->produit->nom ?? 'Produit inconnu' }}</td>
                                    <td>{{ $ligne->quantite_retournee }}</td>
                                    <td>{{ number_format($ligne->prix_unitaire_retour, 2, ',', ' ') }} FCFA</td>
                                    <td>{{ $ligne->motif_ligne ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('retours_clients.index') }}" class="btn btn-secondary">Retour à la liste</a>
        <a href="{{ route('retours_clients.edit', $retour->id) }}" class="btn btn-warning">Modifier le retour</a>
    </div>
</div>
@endsection
