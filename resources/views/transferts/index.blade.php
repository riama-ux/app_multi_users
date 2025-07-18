@extends('pages.admin.shared.layout')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">

                <!-- En-tête de la page -->
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Liste des Transferts</h3>
                            <div class="nk-block-desc">
                                <p>Gérez les transferts de produits entre vos magasins.</p>
                            </div>
                        </div><!-- .nk-block-head-content -->
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-card-toggle">
                                <a href="{{ route('transferts.create') }}" class="btn btn-primary">
                                    <em class="icon ni ni-plus"></em><span>Nouveau Transfert</span>
                                </a>
                            </div>
                        </div><!-- .nk-block-head-content -->
                    </div><!-- .nk-block-between -->
                </div><!-- .nk-block-head -->

                <!-- Tableau des transferts -->
                <div class="nk-block nk-block-lg">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <table class="nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                                <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col">Date</th>
                                        <th class="nk-tb-col tb-col-md">Magasin Source</th>
                                        <th class="nk-tb-col tb-col-md">Magasin Destination</th>
                                        <th class="nk-tb-col tb-col-sm">Statut</th>
                                        <th class="nk-tb-col nk-tb-action-col text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transferts as $transfert)
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col">{{ $transfert->date_transfert->format('d/m/Y H:i') }}</td>
                                            <td class="nk-tb-col tb-col-md">{{ $transfert->magasinSource->nom }}</td>
                                            <td class="nk-tb-col tb-col-md">{{ $transfert->magasinDestination->nom }}</td>
                                            <td class="nk-tb-col tb-col-sm">
                                                @php
                                                    $badgeClass = '';
                                                    switch ($transfert->statut) {
                                                        case 'attente':
                                                            $badgeClass = 'warning';
                                                            break;
                                                        case 'valide':
                                                            $badgeClass = 'success';
                                                            break;
                                                        case 'annule':
                                                            $badgeClass = 'danger';
                                                            break;
                                                        default:
                                                            $badgeClass = 'secondary';
                                                    }
                                                @endphp
                                                <span class="badge badge-dim bg-{{ $badgeClass }}">{{ ucfirst($transfert->statut) }}</span>
                                            </td>
                                            <td class="nk-tb-col nk-tb-action-col text-end">
                                                <div class="drodown">
                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-sm">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li><a href="{{ route('transferts.show', $transfert) }}"><em class="icon ni ni-eye"></em><span>Détails</span></a></li>
                                                            
                                                            @if($transfert->statut === 'attente' && session('magasin_actif_id') == $transfert->magasin_source_id)
                                                                <li><a href="{{ route('transferts.edit', $transfert) }}"><em class="icon ni ni-edit"></em><span>Modifier</span></a></li>
                                                                <li class="divider"></li>
                                                                <li>
                                                                    <form action="{{ route('transferts.destroy', $transfert) }}" method="POST" class="d-inline-block delete-form">
                                                                        @csrf @method('DELETE')
                                                                        <button class="btn btn-sm text-dark px-3" onclick="return confirm('Supprimer ?')"><em class="icon ni ni-trash"></em><span>Supprimer</button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            
                                                            @if($transfert->statut === 'attente' && session('magasin_actif_id') == $transfert->magasin_destination_id)
                                                                <li class="divider"></li>
                                                                <li>
                                                                    <form action="{{ route('transferts.valider', $transfert) }}" method="POST" class="d-inline-block validate-form">
                                                                        @csrf
                                                                        <button type="button" class="btn btn-sm text-success px-3 w-100 text-start validate-transfert" data-transfert-id="{{ $transfert->id }}">
                                                                            <em class="icon ni ni-check-circle"></em><span>Valider</span>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="nk-tb-item">
                                            <td class="nk-tb-col text-center" colspan="5">
                                                <div class="py-5">
                                                    <p>Aucun transfert trouvé.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div><!-- .card-inner -->
                    </div><!-- .card-preview -->
                </div><!-- .nk-block -->

                <!-- Pagination -->
                <div class="card-inner-sm">
                    <div class="d-flex justify-content-center">
                        {{ $transferts->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
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
                <p>Êtes-vous sûr de vouloir supprimer ce transfert ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de validation -->
<div class="modal fade" tabindex="-1" id="validateConfirmationModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de validation</h5>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir valider ce transfert ? Cette action mettra à jour les stocks.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" id="confirmValidateButton">Valider</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let formToSubmit = null;

        // Gestion de la suppression des transferts
        document.querySelectorAll('.delete-transfert').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); 
                formToSubmit = this.closest('form');
                
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                deleteModal.show();
            });
        });

        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit(); 
            }
        });

        // Gestion de la validation des transferts
        document.querySelectorAll('.validate-transfert').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                formToSubmit = this.closest('form');
                
                var validateModal = new bootstrap.Modal(document.getElementById('validateConfirmationModal'));
                validateModal.show();
            });
        });

        document.getElementById('confirmValidateButton').addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit();
            }
        });
    });
</script>
@endsection