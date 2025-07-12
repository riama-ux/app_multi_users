@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 text-dark mb-0">Modifier le Transfert #{{ $transfert->id }}</h2>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg border-0 mt-4">
        <div class="card-body">
            <form action="{{ route('transferts.update', $transfert->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="magasin_destination_id" class="form-label">Magasin de destination</label>
                            <select name="magasin_destination_id" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($magasins as $magasin)
                                    <option value="{{ $magasin->id }}" {{ old('magasin_destination_id', $transfert->magasin_destination_id) == $magasin->id ? 'selected' : '' }}>{{ $magasin->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="date_transfert" class="form-label">Date du transfert</label>
                            <input type="date" name="date_transfert" class="form-control" value="{{ old('date_transfert', $transfert->date_transfert->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>

                <hr class="mb-4">

                <h5 class="mb-4">Produits à transférer</h5>

                {{-- Intégration du composant Livewire pour la recherche de produits --}}
                <div class="mb-4">
                    @livewire('product-search')
                </div>

                {{-- Tableau des produits avec bordures (classes Bootstrap standard) --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="produitsTable">
                        <thead>
                            <tr>
                                <th class="py-3">Produit</th>
                                <th class="py-3">Description</th>
                                <th class="py-3">Quantité</th>
                                <th class="py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Ligne de modèle cachée pour le clonage par JavaScript --}}
                            <tr style="display: none;" class="product-row-template">
                                <td>
                                    <input type="hidden" class="product-id-input">
                                    <span class="product-name-display fw-bold"></span>
                                    <br><small class="product-ref-code"></small>
                                </td>
                                <td><small class="product-description-display text-muted"></small></td>
                                <td>
                                    <input type="number" class="form-control product-qty-input" min="1" step="1">
                                    <small class="text-muted">Stock dispo: <span class="product-available-stock">0</span></small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm removeRow">Supprimer</button>
                                </td>
                            </tr>
                            {{-- Les lignes de transfert existantes seront pré-remplies ici par JavaScript --}}
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    <button type="submit" class="btn btn-primary">Mettre à jour le transfert</button>
                    <a href="{{ route('transferts.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Scripts JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsTableBody = document.querySelector('#produitsTable tbody');
        const productRowTemplate = document.querySelector('.product-row-template');

        // Fonction pour mettre à jour les attributs 'name' des inputs
        function updateRowIndexes() {
            produitsTableBody.querySelectorAll('tr:not(.product-row-template)').forEach((row, index) => {
                row.querySelector('.product-id-input').setAttribute('name', `produits[${index}]`);
                row.querySelector('.product-qty-input').setAttribute('name', `quantites[${index}]`);
            });
        }

        // Fonction pour ajouter une ligne de produit au tableau
        function addProductRow(product, quantity = 1, availableStock = 0) {
            // Vérifier si le produit existe déjà pour éviter les doublons
            let existingRowInput = Array.from(produitsTableBody.querySelectorAll('.product-id-input'))
                                            .find(input => input.value == product.id);

            if (existingRowInput) {
                // Si le produit existe, incrémenter la quantité
                const qtyInput = existingRowInput.closest('tr').querySelector('.product-qty-input');
                const newQty = parseInt(qtyInput.value) + 1;
                if (newQty <= availableStock) {
                    qtyInput.value = newQty;
                } else {
                    alert(`La quantité maximale disponible pour ce produit est de ${availableStock}.`);
                }
                return;
            }

            const newRow = productRowTemplate.cloneNode(true);
            newRow.style.display = '';
            newRow.classList.remove('product-row-template');

            // Remplir les données du produit
            newRow.querySelector('.product-id-input').value = product.id;
            newRow.querySelector('.product-name-display').textContent = product.nom;
            newRow.querySelector('.product-ref-code').textContent = `Référence: ${product.reference || ''} / Code: ${product.code || ''}`;
            newRow.querySelector('.product-description-display').textContent = product.description || '';
            newRow.querySelector('.product-available-stock').textContent = availableStock;

            const qtyInput = newRow.querySelector('.product-qty-input');
            qtyInput.max = availableStock;
            qtyInput.value = quantity;
            qtyInput.setAttribute('required', 'required');

            // Ajouter l'écouteur de suppression pour la nouvelle ligne
            newRow.querySelector('.removeRow').addEventListener('click', function() {
                newRow.remove();
                updateRowIndexes();
            });

            produitsTableBody.appendChild(newRow);
            updateRowIndexes(); // Mettre à jour les indices
        }

        // --- Population initiale des lignes de transfert existantes ---
        // S'assurer que $transfert->ligneTransferts est chargé dans le contrôleur
        const existingLignesTransfert = @json($transfert->ligneTransferts ?? []);

        existingLignesTransfert.forEach(ligne => {
            if (ligne.produit) {
                const productData = {
                    id: ligne.produit.id,
                    nom: ligne.produit.nom,
                    code: ligne.produit.code || '',
                    reference: ligne.produit.reference || '',
                    description: ligne.produit.description || ''
                };
                // Note: Nous utilisons le stock disponible du produit agrégé pour le 'availableStock'
                addProductRow(productData, ligne.quantite, ligne.produit.quantite);
            }
        });

        // --- Écouteur Livewire pour la sélection d'un nouveau produit ---
        window.addEventListener('productSelected', event => {
            const product = event.detail.product;
            const availableStock = event.detail.availableStock;
            addProductRow(product, 1, availableStock);
        });

        // --- Écouteur pour les boutons de suppression (Délégation) ---
        produitsTableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow')) {
                const row = e.target.closest('tr');
                if (row && !row.classList.contains('product-row-template')) {
                    row.remove();
                    updateRowIndexes();
                }
            }
        });
    });
</script>
@endsection