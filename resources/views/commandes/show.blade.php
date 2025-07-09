@extends('pages.admin.shared.layout')

@section('content')
    <h1>Détails de la commande #{{ $commande->id }}</h1>

    <p><strong>Fournisseur :</strong> {{ $commande->fournisseur->nom }}</p>
    <p><strong>Date commande :</strong> {{ $commande->date_commande->format('d/m/Y') }}</p>
    <p><strong>Date prévue de livraison :</strong> {{ $commande->date_prevue_livraison->format('d/m/Y') }}</p>

    {{-- LOGIQUE POUR LE STATUT DE LIVRAISON BASÉE SUR 'is_late' ET 'days_late' --}}
    @if ($commande->statut === 'livree')
        <p><strong>Date de réception :</strong> {{ $commande->date_reception->format('d/m/Y H:i') }}</p>
        @if ($commande->is_late)
            <p class="text-danger"><strong>Statut de livraison :</strong> Livrée en retard de {{ $commande->days_late }} jour(s) !</p> {{-- AJOUTEZ CECI --}}
        @else
            <p class="text-success"><strong>Statut de livraison :</strong> Livrée à temps.</p>
        @endif
    @else
        <p><strong>Date de réception :</strong> Non réceptionnée</p>
        @if ($commande->date_prevue_livraison->isPast() && $commande->statut === 'en_attente')
            <p class="text-warning"><strong>Statut de livraison :</strong> En retard (non réceptionnée) !</p>
            {{-- Vous pourriez aussi calculer les jours de retard ici pour l'affichage, même si non enregistré --}}
            @php
                $currentDate = \Carbon\Carbon::now()->startOfDay();
                $prevueDate = $commande->date_prevue_livraison->startOfDay();
                if ($currentDate->greaterThan($prevueDate)) {
                    $daysOverdue = $currentDate->diffInDays($prevueDate);
                    echo '<p class="text-warning">Retard actuel : ' . $daysOverdue . ' jour(s).</p>';
                }
            @endphp
        @else
            <p><strong>Statut de livraison :</strong> En attente.</p>
        @endif
    @endif

    <p><strong>Statut de la commande :</strong> {{ ucfirst($commande->statut) }}</p>
    <p><strong>Coût transport :</strong> {{ number_format($commande->cout_transport ?? 0, 0, ',', ' ') }} FCFA</p>
    <p><strong>Frais supplémentaires :</strong> {{ number_format($commande->frais_suppl ?? 0, 0, ',', ' ') }} FCFA</p>
    <p><strong>Coût total :</strong> {{ number_format($commande->cout_total ?? 0, 0, ',', ' ') }} FCFA</p>

    <h4>Produits commandés</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Code</th>
                <th>Référence</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($commande->lignesCommande as $ligne)
                <tr>
                    <td>{{ $ligne->produit->nom }}</td>
                    <td>{{ $ligne->produit->code ?? 'N/A' }}</td>
                    <td>{{ $ligne->produit->reference ?? 'N/A' }}</td>
                    <td>{{ $ligne->quantite }}</td>
                    <td>{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($commande->statut !== 'livree')
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#receptionModal">
            Réceptionner
        </button>
    @endif

    <a href="{{ route('commandes.index') }}" class="btn btn-secondary">Retour</a>

    {{-- Votre modal de réception --}}
    <div class="modal fade" id="receptionModal" tabindex="-1" aria-labelledby="receptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('commandes.reception', $commande) }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="receptionModalLabel">Réception commande #{{ $commande->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cout_transport" class="form-label">Coût transport *</label>
                        <input type="number" name="cout_transport" id="cout_transport" class="form-control" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="frais_suppl" class="form-label">Frais supplémentaires *</label>
                        <input type="number" name="frais_suppl" id="frais_suppl" class="form-control" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Valider la réception</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
@endsection