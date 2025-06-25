@extends('pages.admin.shared.layout')

@section('content')
<h3>Détails du fournisseur</h3>

<a href="{{ route('module.fournisseurs.index') }}" class="btn btn-secondary mb-3">← Retour à la liste</a>

<div class="card mb-4">
    <div class="card-body">
        <p><strong>Nom :</strong> {{ $fournisseur->nom }}</p>
        <p><strong>Téléphone :</strong> {{ $fournisseur->telephone ?? '—' }}</p>
        <p><strong>Email :</strong> {{ $fournisseur->email ?? '—' }}</p>
        <p><strong>Adresse :</strong> {{ $fournisseur->adresse ?? '—' }}</p>
    </div>
</div>

<h4>Commandes passées</h4>

@if($fournisseur->commandes->isEmpty())
    <p>Aucune commande enregistrée pour ce fournisseur.</p>
@else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Statut</th>
                <th>Utilisateur</th>
                <th>Détails</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fournisseur->commandes as $commande)
                <tr>
                    <td>{{ $commande->date_commande }}</td>
                    <td>
                        <span class="badge bg-{{ $commande->statut === 'livrée' ? 'success' : 'warning' }}">
                            {{ ucfirst($commande->statut) }}
                        </span>
                    </td>
                    <td>{{ $commande->user->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('module.commandes.show', $commande->id) }}" class="btn btn-sm btn-info">
                            Voir
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection
