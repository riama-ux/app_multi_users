@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid">
    <h1>Détails de l'Ajustement de Stock #{{ $ajustement->id }}</h1>

    <div class="card mb-4">
        <div class="card-header">Informations Générales</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Date d'Ajustement:</strong> {{ $ajustement->date_ajustement->format('d/m/Y H:i') }}</p>
                    <p><strong>Type:</strong>
                        @if($ajustement->type == 'entree')
                            <span class="badge bg-success">Entrée</span>
                        @else
                            <span class="badge bg-danger">Sortie</span>
                        @endif
                    </p>
                    <p><strong>Utilisateur:</strong> {{ $ajustement->user->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Motif Global:</strong> {{ $ajustement->motif_global ?? 'Non spécifié' }}</p>
                    <p><strong>Magasin:</strong> {{ $ajustement->magasin->nom ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Produits Ajustés</div>
        <div class="card-body">
            @if($ajustement->lignesAjustement->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité Ajustée</th>
                                <th>Prix Unitaire Ajusté</th>
                                <th>Motif Spécifique</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ajustement->lignesAjustement as $ligne)
                                <tr>
                                    <td>{{ $ligne->produit->nom ?? 'Produit inconnu' }}</td>
                                    <td>{{ $ligne->quantite_ajustee }}</td>
                                    <td>{{ number_format($ligne->prix_unitaire_ajuste, 2, ',', ' ') }} FCFA</td>
                                    <td>{{ $ligne->motif_ligne ?? 'Non spécifié' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>Aucun produit n'a été ajusté dans cet ajustement.</p>
            @endif
        </div>
    </div>

    <a href="{{ route('ajustements.index') }}" class="btn btn-secondary">Retour à la liste</a>
    <a href="{{ route('ajustements.edit', $ajustement->id) }}" class="btn btn-warning">Modifier l'Ajustement</a>
</div>
@endsection
