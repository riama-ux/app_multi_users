@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark mb-0">Créer un nouvel Ajustement de Stock</h1>
        <a href="{{ route('ajustements.index') }}" class="btn btn-outline-primary shadow-sm d-flex align-items-center">
            <i class="fas fa-list me-2"></i> Liste des Ajustements
        </a>
    </div>

    <div class="card shadow-lg border-0 mt-4">
        <div class="card-body">
            <form action="{{ route('ajustements.store') }}" method="POST" id="ajustementForm">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="date_ajustement" class="form-label">Date et heure de l'ajustement <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_ajustement" id="date_ajustement" class="form-control" value="{{ old('date_ajustement', now()->format('Y-m-d\TH:i')) }}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="type" class="form-label">Type d'ajustement <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="">-- Sélectionner le type --</option>
                                <option value="entree" {{ old('type') == 'entree' ? 'selected' : '' }}>Entrée (Ajout de stock)</option>
                                <option value="sortie" {{ old('type') == 'sortie' ? 'selected' : '' }}>Sortie (Retrait de stock)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="motif_global" class="form-label">Motif global de l'ajustement</label>
                            <textarea name="motif_global" id="motif_global" class="form-control" rows="3">{{ old('motif_global') }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="mt-4 mb-4">

                <h4 class="mb-4">Produits à ajuster</h4>

                {{-- Composant Livewire pour la recherche de produits --}}
                <div class="mb-4">
                    @livewire('ajustement-product-search')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="produitsAjustementTable">
                        <thead>
                            <tr class="table-light">
                                <th>Produit</th>
                                <th>Stock Actuel</th>
                                <th>Quantité Ajustée <span class="text-danger">*</span></th>
                                <th>Prix Unitaire Ajusté (pour entrée)</th>
                                <th>Motif Spécifique</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Ligne de modèle cachée pour le clonage par JavaScript --}}
                            <tr style="display: none;" class="product-ajustement-row-template">
                                <td>
                                    <input type="hidden" class="product-id-input" data-name="lignes[idx][produit_id]" disabled>
                                    <span class="product-name-display fw-bold"></span>
                                </td>
                                <td>
                                    <span class="product-current-stock-display text-info fw-bold"></span>
                                </td>
                                <td>
                                    <input type="number" class="form-control quantite-ajustee-input" min="1" value="1" required data-name="lignes[idx][quantite_ajustee]" disabled>
                                </td>
                                <td>
                                    <input type="number" step="1" class="form-control prix-unitaire-ajuste-input" min="1" data-name="lignes[idx][prix_unitaire_ajuste]" disabled>
                                </td>
                                <td>
                                    <input type="text" class="form-control motif-ligne-input" placeholder="Motif spécifique (optionnel)" data-name="lignes[idx][motif_ligne]" disabled>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm removeRow">Supprimer</button>
                                </td>
                            </tr>
                            
                            {{-- Lignes pré-remplies en cas d'erreur de validation (old('lignes')) --}}
                            @if(old('lignes'))
                                @foreach(old('lignes') as $index => $oldLigne)
                                    @php
                                        // Récupération du produit pour l'affichage (nécessite d'avoir la relation configurée ou de le retrouver)
                                        $product = \App\Models\Produit::find($oldLigne['produit_id']);
                                    @endphp
                                    @if($product)
                                        <tr class="product-ajustement-row">
                                            <td>
                                                <input type="hidden" name="lignes[{{ $index }}][produit_id]" value="{{ $product->id }}">
                                                <span class="product-name-display fw-bold">{{ $product->nom }}</span>
                                            </td>
                                            <td>
                                                <span class="product-current-stock-display text-info fw-bold">{{ $product->quantite }}</span>
                                            </td>
                                            <td>
                                                <input type="number" name="lignes[{{ $index }}][quantite_ajustee]" class="form-control quantite-ajustee-input" min="1" value="{{ $oldLigne['quantite_ajustee'] }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="1" name="lignes[{{ $index }}][prix_unitaire_ajuste]" class="form-control prix-unitaire-ajuste-input" min="1" value="{{ $oldLigne['prix_unitaire_ajuste'] ?? '' }}">
                                            </td>
                                            <td>
                                                <input type="text" name="lignes[{{ $index }}][motif_ligne]" class="form-control motif-ligne-input" placeholder="Motif spécifique (optionnel)" value="{{ $oldLigne['motif_ligne'] ?? '' }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm removeRow">Supprimer</button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    <button type="submit" class="btn btn-primary me-2">Enregistrer l'Ajustement</button>
                    <a href="{{ route('ajustements.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsAjustementTableBody = document.querySelector('#produitsAjustementTable tbody');
        const productAjustementRowTemplate = document.querySelector('.product-ajustement-row-template');
        const ajustementForm = document.getElementById('ajustementForm');

        // Fonction pour ajouter une ligne de produit au tableau d'ajustement
        function addProductAjustementRow(product, quantiteAjustee = 1, prixUnitaireAjuste = '') {
            // Vérifier si le produit existe déjà par son ID pour éviter les doublons
            let existingRowInput = Array.from(produitsAjustementTableBody.querySelectorAll('.product-id-input'))
                                         .find(input => input.value == product.id);

            if (existingRowInput) {
                alert('Ce produit est déjà dans la liste d\'ajustement.');
                return;
            }

            const newRow = productAjustementRowTemplate.cloneNode(true);
            newRow.style.display = '';
            newRow.classList.remove('product-ajustement-row-template');
            newRow.classList.add('product-ajustement-row');

            // Réactiver les inputs et définir les noms
            newRow.querySelectorAll('input, select, textarea').forEach(input => {
                input.removeAttribute('disabled');
            });

            // Assigner les valeurs des inputs
            newRow.querySelector('.product-id-input').value = product.id;
            newRow.querySelector('.product-name-display').textContent = product.nom;
            // Afficher le stock actuel du produit
            newRow.querySelector('.product-current-stock-display').textContent = product.quantite; 
            newRow.querySelector('.quantite-ajustee-input').value = quantiteAjustee;
            newRow.querySelector('.prix-unitaire-ajuste-input').value = prixUnitaireAjuste;

            // Ajouter l'écouteur de suppression pour la nouvelle ligne
            newRow.querySelector('.removeRow').addEventListener('click', function() {
                newRow.remove();
                updateRowIndexes();
            });

            produitsAjustementTableBody.appendChild(newRow);
            updateRowIndexes();
        }

        // Fonction pour réindexer les noms des inputs
        function updateRowIndexes() {
            let index = 0;
            produitsAjustementTableBody.querySelectorAll('tr.product-ajustement-row').forEach((row) => {
                // Assigner les attributs 'name' en utilisant l'index courant
                row.querySelector('.product-id-input').setAttribute('name', `lignes[${index}][produit_id]`);
                row.querySelector('.quantite-ajustee-input').setAttribute('name', `lignes[${index}][quantite_ajustee]`);
                row.querySelector('.prix-unitaire-ajuste-input').setAttribute('name', `lignes[${index}][prix_unitaire_ajuste]`);
                row.querySelector('.motif-ligne-input').setAttribute('name', `lignes[${index}][motif_ligne]`);
                index++;
            });
        }

        // Écouteur Livewire pour la sélection d'un nouveau produit
        window.addEventListener('productSelectedForAjustement', event => {
            const product = event.detail.product;
            addProductAjustementRow(product, 1, product.cout_achat);
        });

        // Gestion de la soumission du formulaire
        ajustementForm.addEventListener('submit', function(e) {
            e.preventDefault(); 
            updateRowIndexes();
            
            // Validation côté client pour s'assurer qu'au moins une ligne est présente
            const actualProductRows = produitsAjustementTableBody.querySelectorAll('tr.product-ajustement-row');
            if (actualProductRows.length === 0) {
                alert('Veuillez ajouter au moins un produit à l\'ajustement.');
                return;
            }

            // Soumission standard du formulaire (le code JavaScript de soumission via Fetch a été retiré pour laisser le formulaire se soumettre normalement, permettant à Laravel de gérer les validations et la redirection)
            this.submit();
        });

        // Mise à jour initiale des indices pour les lignes pré-remplies (si applicable)
        updateRowIndexes();
    });
</script>
@endsection