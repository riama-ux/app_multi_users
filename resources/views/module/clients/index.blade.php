@extends('pages.admin.shared.layout')

@section('content')

<div class="nk-block nk-block-lg">
    
    <div class="nk-block-head-content d-flex justify-content-between align-items-center mb-4">
        <h1 class="nk-block-title">Clients du magasin actif</h1>
        <a href="{{ route('module.clients.create') }}" class="btn btn-primary">
            <em class="icon ni ni-plus"></em><span>Nouveau client</span>
        </a>
    </div>

    

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <table class="nk-tb-list nk-tb-ulgy" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col"><span>Nom</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Téléphone</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Email</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Adresse</span></th>
                        <th class="nk-tb-col nk-tb-col-tools text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr class="nk-tb-item">
                            <td class="nk-tb-col">{{ $client->nom }}</td>
                            <td class="nk-tb-col tb-col-md">{{ $client->telephone }}</td>
                            <td class="nk-tb-col tb-col-md">{{ $client->email }}</td>
                            <td class="nk-tb-col tb-col-md">{{ $client->adresse }}</td>
                            <td class="nk-tb-col nk-tb-col-tools">
                                <ul class="nk-tb-actions gx-1">
                                    <li>
                                        <a href="{{ route('module.clients.edit', $client->id) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                            <em class="icon ni ni-edit-alt"></em>
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('module.clients.destroy', $client->id) }}" method="POST" class="d-inline-block">
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
                            <td class="nk-tb-col text-center" colspan="5">Aucun client enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div></div><div class="card-inner-sm">
        <div class="d-flex justify-content-center">
            {{ $clients->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div><div class="modal fade" tabindex="-1" id="deleteConfirmationModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de suppression</h5>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le client <strong id="clientToDeleteName"></strong> ? Cette action est irréversible.</p>
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
        let clientName = '';

        // Gestion de la suppression (remplace la confirmation native)
        document.querySelectorAll('.delete-client').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Empêche la soumission immédiate du formulaire
                formToSubmit = this.closest('form');
                clientName = this.dataset.clientName;
                document.getElementById('clientToDeleteName').textContent = clientName;
                
                // Affiche le modal de confirmation
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                deleteModal.show();
            });
        });

        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit(); // Soumet le formulaire après confirmation
            }
        });
    });
</script>
@endsection