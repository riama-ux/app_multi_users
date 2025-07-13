@extends('pages.admin.shared.layout')

@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Liste des Retours Clients</h3>
                            <div class="nk-block-desc">
                                <p>Gérez les retours clients et consultez l'historique des retours.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            
                        </div>
                    </div>
                    </div>
                        <div class="nk-block">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            
                            <form action="{{ route('retours_clients.index') }}" method="GET">
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="client_id" class="form-label">Client</label>
                                            <select name="client_id" id="client_id" class="form-select">
                                                <option value="">-- Tous les clients --</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="statut" class="form-label">Statut</label>
                                            <select name="statut" id="statut" class="form-select">
                                                <option value="">-- Tous les statuts --</option>
                                                <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                                <option value="traite" {{ request('statut') == 'traite' ? 'selected' : '' }}>Traité</option>
                                                <option value="rembourse" {{ request('statut') == 'rembourse' ? 'selected' : '' }}>Remboursé</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                                        
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><div class="nk-block nk-block-lg">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table class="nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                                <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col">ID</th>
                                        <th class="nk-tb-col tb-col-md">Date Retour</th>
                                        <th class="nk-tb-col tb-col-lg">Client</th>
                                        <th class="nk-tb-col tb-col-lg">Vente Associée</th>
                                        <th class="nk-tb-col tb-col-md">Montant Remboursé</th>
                                        <th class="nk-tb-col tb-col-sm">Motif Global</th>
                                        <th class="nk-tb-col tb-col-md">Statut</th>
                                        <th class="nk-tb-col tb-col-md">Effectué par</th>
                                        <th class="nk-tb-col nk-tb-col-tools text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($retours as $retour)
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col">{{ $retour->id }}</td>
                                            <td class="nk-tb-col tb-col-md">{{ $retour->date_retour->format('d/m/Y H:i') }}</td>
                                            <td class="nk-tb-col tb-col-lg">{{ $retour->client->nom ?? 'Client inconnu' }}</td>
                                            <td class="nk-tb-col tb-col-lg">
                                                @if ($retour->vente)
                                                    <a href="{{ route('ventes.show', $retour->vente->id) }}">Vente #{{ $retour->vente->id }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="nk-tb-col tb-col-md">{{ number_format($retour->montant_rembourse, 2, ',', ' ') }} FCFA</td>
                                            <td class="nk-tb-col tb-col-sm">{{ $retour->motif_global ?? 'N/A' }}</td>
                                            <td class="nk-tb-col tb-col-md">
                                                @php
                                                    $statusClass = '';
                                                    switch ($retour->statut) {
                                                        case 'remboursé':
                                                            $statusClass = 'success';
                                                            break;
                                                        case 'traité':
                                                            $statusClass = 'info';
                                                            break;
                                                        case 'en_attente':
                                                        default:
                                                            $statusClass = 'warning';
                                                            break;
                                                    }
                                                @endphp
                                                <span class="badge badge-dim bg-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $retour->statut)) }}</span>
                                            </td>
                                            <td class="nk-tb-col tb-col-md">{{ $retour->user->name ?? 'Utilisateur inconnu' }}</td>
                                            <td class="nk-tb-col nk-tb-col-tools">
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-sm">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li><a href="{{ route('retours_clients.show', $retour->id) }}"><em class="icon ni ni-eye"></em><span>Détails</span></a></li>
                                                            
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col text-center" colspan="9">
                                                <div class="py-5">
                                                    <p>Aucun retour client trouvé.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div></div></div><div class="card-inner-sm">
                    <div class="d-flex justify-content-center">
                        {{ $retours->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="deleteConfirmationModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de suppression</h5>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce retour client ? Cette action est irréversible et affectera le stock.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Supprimer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let formToSubmit = null;

        // Écouteurs pour les boutons de suppression dans le dropdown
        document.querySelectorAll('.delete-retour').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Empêche la soumission immédiate du formulaire
                
                // Récupère le formulaire parent le plus proche avec la classe .delete-form
                formToSubmit = this.closest('.delete-form');
                
                // Ouvre le modal de confirmation
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                deleteModal.show();
            });
        });

        // Confirmation de la suppression dans le modal
        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (formToSubmit) {
                // Soumet le formulaire après confirmation
                formToSubmit.submit(); 
            }
        });
    });
</script>
@endsection