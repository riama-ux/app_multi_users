@extends('pages.admin.shared.layout')

@section('content')

    

<!-- Conteneur principal de DashLite -->
<div class="nk-block nk-block-lg">
    
    <div class="nk-block-head-content d-flex justify-content-between align-items-center mb-4">
        <h1 class="nk-block-title">Liste des produits</h1>
        <a href="{{ route('produits.create') }}" class="btn btn-primary">
            <em class="icon ni ni-plus"></em><span>Ajouter un produit</span>
        </a>
    </div>

    <div class="card card-bordered card-preview">
        <div class="card-inner">
            <table class="nk-tb-list nk-tb-ulgy" data-auto-responsive="false">
                <thead>
                    <tr class="nk-tb-item nk-tb-head">
                        <th class="nk-tb-col"><span>Nom</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Référence</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Catégorie</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Coût achat</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Prix vente</span></th>
                        <th class="nk-tb-col tb-col-md"><span>Statut</span></th>
                        <th class="nk-tb-col nk-tb-col-tools text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produits as $produit)
                        <tr class="nk-tb-item @if($produit->trashed()) bg-lighter @endif">
                            <td class="nk-tb-col">{{ $produit->nom }}</td>
                            <td class="nk-tb-col tb-col-md">{{ $produit->reference }}</td>
                            <td class="nk-tb-col tb-col-md">{{ $produit->categorie->nom ?? 'N/A' }}</td>
                            <td class="nk-tb-col tb-col-md">{{ number_format($produit->cout_achat, 2) }}</td>
                            <td class="nk-tb-col tb-col-md">{{ number_format($produit->prix_vente, 2) }}</td>
                            <td class="nk-tb-col tb-col-md">
                                @if($produit->trashed())
                                    <span class="badge badge-dim bg-danger">Supprimé</span>
                                @else
                                    <span class="badge badge-dim bg-success">Actif</span>
                                @endif
                            </td>
                            <td class="nk-tb-col nk-tb-col-tools">
                                <ul class="nk-tb-actions gx-1">
                                    @if(!$produit->trashed())
                                        <li>
                                            <a href="{{ route('produits.show', $produit) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Voir">
                                                <em class="icon ni ni-eye"></em>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('produits.edit', $produit) }}" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                                <em class="icon ni ni-edit-alt"></em>
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('produits.destroy', $produit) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-trigger btn-icon delete-product" data-product-name="{{ $produit->nom }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer">
                                                    <em class="icon ni ni-trash"></em>
                                                </button>
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <form action="{{ route('produits.restore', $produit->id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                <button type="submit" class="btn btn-trigger btn-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Restaurer">
                                                    <em class="icon ni ni-undo"></em>
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('produits.forceDelete', $produit->id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-trigger btn-icon force-delete-product" data-product-name="{{ $produit->nom }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer définitivement">
                                                    <em class="icon ni ni-trash-fill"></em>
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr class="nk-tb-item">
                            <td class="nk-tb-col text-center" colspan="7">Aucun produit trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div><!-- .card-inner -->
    </div><!-- .card-preview -->

    <div class="card-inner-sm">
        {{ $produits->links('pagination::bootstrap-5') }}
    </div>

</div><!-- .nk-block -->

<!-- Modal de confirmation de suppression (pour remplacer confirm()) -->
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
                <p>Êtes-vous sûr de vouloir supprimer le produit <strong id="productToDeleteName"></strong> ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression définitive -->
<div class="modal fade" tabindex="-1" id="forceDeleteConfirmationModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                <em class="icon ni ni-cross"></em>
            </a>
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de suppression définitive</h5>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir **supprimer définitivement** le produit <strong id="productToForceDeleteName"></strong> ? Toutes les données associées seront perdues.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmForceDeleteButton">Supprimer définitivement</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let formToSubmit = null;
        let productName = '';

        // Gestion de la suppression douce (soft delete)
        document.querySelectorAll('.delete-product').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Empêche la soumission immédiate du formulaire
                formToSubmit = this.closest('form');
                productName = this.dataset.productName;
                document.getElementById('productToDeleteName').textContent = productName;
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

        // Gestion de la suppression définitive (force delete)
        document.querySelectorAll('.force-delete-product').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Empêche la soumission immédiate du formulaire
                formToSubmit = this.closest('form');
                productName = this.dataset.productName;
                document.getElementById('productToForceDeleteName').textContent = productName;
                // Affiche le modal de confirmation
                var forceDeleteModal = new bootstrap.Modal(document.getElementById('forceDeleteConfirmationModal'));
                forceDeleteModal.show();
            });
        });

        document.getElementById('confirmForceDeleteButton').addEventListener('click', function() {
            if (formToSubmit) {
                formToSubmit.submit(); // Soumet le formulaire après confirmation
            }
        });
    });
</script>
@endsection