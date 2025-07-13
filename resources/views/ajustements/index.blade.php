@extends('pages.admin.shared.layout')

@section('content')

<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Liste des Ajustements de Stock</h3>
                            <div class="nk-block-desc">
                                <p>Gérez et consultez les ajustements de stock.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('ajustements.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center">
                                <em class="icon ni ni-plus-lg"></em> Nouvel Ajustement
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Section de filtrage --}}
                <div class="nk-block">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <form method="GET" action="{{ route('ajustements.index') }}">
                                <div class="row g-4 justify-content-center">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="type" class="form-label">Type d'ajustement</label>
                                            <select name="type" id="type" class="form-select">
                                                <option value="">Tous</option>
                                                <option value="entree" {{ request('type') == 'entree' ? 'selected' : '' }}>Entrée</option>
                                                <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>Sortie</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date" class="form-label">Date</label>
                                            <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                                        <a href="{{ route('ajustements.index') }}" class="btn btn-secondary">Réinitialiser</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="nk-block nk-block-lg">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table class="nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                                <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col">ID</th>
                                        <th class="nk-tb-col tb-col-md">Date d'Ajustement</th>
                                        <th class="nk-tb-col tb-col-lg">Type</th>
                                        <th class="nk-tb-col tb-col-sm">Motif Global</th>
                                        <th class="nk-tb-col tb-col-md">Utilisateur</th>
                                        <th class="nk-tb-col nk-tb-col-tools text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ajustements as $ajustement)
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col">{{ $ajustement->id }}</td>
                                            <td class="nk-tb-col tb-col-md">{{ $ajustement->date_ajustement->format('d/m/Y H:i') }}</td>
                                            <td class="nk-tb-col tb-col-lg">
                                                @if($ajustement->type == 'entree')
                                                    <span class="badge badge-dim bg-success">Entrée</span>
                                                @else
                                                    <span class="badge badge-dim bg-danger">Sortie</span>
                                                @endif
                                            </td>
                                            <td class="nk-tb-col tb-col-sm">{{ $ajustement->motif_global ?? 'N/A' }}</td>
                                            <td class="nk-tb-col tb-col-md">{{ $ajustement->user->name ?? 'N/A' }}</td>
                                            <td class="nk-tb-col nk-tb-col-tools">
                                                <ul class="nk-tb-actions gx-1">
                                                    <li>
                                                        <a href="{{ route('ajustements.show', $ajustement->id) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Détails">
                                                            <em class="icon ni ni-eye"></em>
                                                        </a>
                                                    </li>
                                                    
                                                    
                                                    <li>
                                                        
                                                        <form id="delete-form-{{ $ajustement->id }}" action="{{ route('ajustements.destroy', $ajustement->id) }}" method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-trigger btn-icon delete-client" onclick="return confirm('Supprimer ?')" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer"><em class="icon ni ni-trash"></em></button>
                                            
                                                        </form>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col text-center" colspan="6">
                                                <div class="py-5">
                                                    <p>Aucun ajustement de stock trouvé.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card-inner-sm">
                    <div class="d-flex justify-content-center">
                        {{ $ajustements->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmation de suppression --}}
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
                <p>Êtes-vous sûr de vouloir supprimer cet ajustement de stock ? Cette action est irréversible et annulera les mouvements de stock associés.</p>
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

        // Écouteurs pour les boutons de suppression
        document.querySelectorAll('.delete-ajustement').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); 
                
                const ajustementId = this.getAttribute('data-ajustement-id');
                formToSubmit = document.getElementById('delete-form-' + ajustementId);
                
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