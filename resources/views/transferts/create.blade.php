@extends('pages.admin.shared.layout')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-dark mb-0"> Nouveau Transfert</h2>
        <a href="{{ route('transferts.index') }}" class="btn btn-primary shadow-sm d-flex align-items-center">
            <i class="fas fa-list me-2"></i> Liste des Transferts
        </a>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <form action="{{ route('transferts.store') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="magasin_destination_id" class="form-label">Magasin de destination</label>
                            <select name="magasin_destination_id" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($magasins as $magasin)
                                    <option value="{{ $magasin->id }}" {{ old('magasin_destination_id') == $magasin->id ? 'selected' : '' }}>{{ $magasin->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="date_transfert" class="form-label">Date du transfert</label>
                            <input type="date" name="date_transfert" class="form-control" value="{{ old('date_transfert', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>

                <hr class="mb-4">
                
                <h5>Produits à transférer</h5>

                {{-- Intégration du composant Livewire pour la recherche de produits --}}
                <div class="mb-4">
                    @livewire('product-search')
                </div>

                {{-- Tableau des produits avec bordures (classes Bootstrap standard) --}}
                <table class="table table-bordered table-striped" id="produitsTable">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Ligne de modèle cachée pour le clonage par JavaScript --}}
                        <tr style="display: none;" class="product-row-template">
                            <td>
                                <input type="hidden" name="produits[]" class="product-id-input">
                                <span class="product-name-display fw-bold"></span>
                                <br><small class="product-ref-code text-muted"></small>
                            </td>
                            <td>
                                <input type="number" class="form-control product-qty-input" min="1" step="1">
                                <small class="text-muted">Stock dispo: <span class="product-available-stock">0</span></small>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm removeRow">Supprimer</button>
                            </td>
                        </tr>
                        {{-- Les produits ajoutés dynamiquement seront insérés ici --}}
                    </tbody>
                </table>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Enregistrer le transfert</button>
                    <a href="{{ route('transferts.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsTableBody = document.querySelector('#produitsTable tbody');
        const productRowTemplate = document.querySelector('.product-row-template');

        // Écouteur pour le bouton "Supprimer" sur les lignes de produits
        produitsTableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow')) {
                e.preventDefault();
                const row = e.target.closest('tr');
                if (row) {
                    row.remove();
                }
            }
        });

        // Livewire event listener pour ajouter un produit à la table
        window.addEventListener('productSelected', event => {
            const product = event.detail.product;
            const availableStock = event.detail.availableStock;

            let existingRow = Array.from(produitsTableBody.querySelectorAll('.product-id-input'))
                                    .find(input => input.value == product.id);

            if (existingRow) {
                const qtyInput = existingRow.closest('tr').querySelector('.product-qty-input');
                const newQty = parseInt(qtyInput.value) + 1;
                
                if (newQty <= availableStock) {
                    qtyInput.value = newQty;
                } else {
                    alert(`La quantité maximale disponible pour ce produit est de ${availableStock}.`);
                }
            } else {
                // Cloner la ligne template et la remplir
                const newRow = productRowTemplate.cloneNode(true);
                newRow.style.display = '';
                newRow.classList.remove('product-row-template');

                newRow.querySelector('.product-id-input').value = product.id;
                newRow.querySelector('.product-name-display').textContent = product.nom;
                newRow.querySelector('.product-ref-code').textContent = `Référence: ${product.reference} / Code: ${product.code}`;
                newRow.querySelector('.product-available-stock').textContent = availableStock;

                const qtyInput = newRow.querySelector('.product-qty-input');
                qtyInput.max = availableStock;
                qtyInput.value = 1;
                
                qtyInput.setAttribute('name', 'quantites[]');
                qtyInput.setAttribute('required', 'required');

                produitsTableBody.appendChild(newRow);
            }
        });
    });
</script>
@endsection