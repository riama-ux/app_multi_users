@extends('pages.admin.shared.layout')

@section('content')
<div class="container">
    <h2>Modifier le Transfert #{{ $transfert->id }}</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('transferts.update', $transfert->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="magasin_destination_id">Magasin de destination</label>
            <select name="magasin_destination_id" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                @foreach($magasins as $magasin)
                    <option value="{{ $magasin->id }}" {{ old('magasin_destination_id', $transfert->magasin_destination_id) == $magasin->id ? 'selected' : '' }}>{{ $magasin->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="date_transfert">Date du transfert</label>
            <input type="date" name="date_transfert" class="form-control" value="{{ old('date_transfert', $transfert->date_transfert->format('Y-m-d')) }}" required>
        </div>

        <hr>
        <h5>Produits à transférer</h5>

        {{-- Intégration du composant Livewire pour la recherche de produits --}}
        <div class="mb-4">
            @livewire('product-search')
        </div>

        <table class="table table-bordered" id="produitsTable">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Description</th> {{-- AJOUT DE LA COLONNE DESCRIPTION --}}
                    <th>Quantité</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- Ligne de modèle cachée pour le clonage par JavaScript --}}
                <tr style="display: none;" class="product-row-template">
                    <td>
                        <input type="hidden" class="product-id-input">
                        <span class="product-name-display"></span>
                        <br><small class="product-ref-code"></small>
                    </td>
                    <td><small class="product-description-display text-muted"></small></td> {{-- AJOUT DE L'AFFICHAGE DESCRIPTION --}}
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

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Mettre à jour le transfert</button>
            <a href="{{ route('transferts.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

{{-- Scripts JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsTableBody = document.querySelector('#produitsTable tbody');
        const productRowTemplate = document.querySelector('.product-row-template');

        // Fonction pour mettre à jour les attributs 'name' des inputs dans les lignes du tableau
        // Ceci assure une indexation séquentielle pour la soumission du formulaire
        function updateRowIndexes() {
            produitsTableBody.querySelectorAll('tr:not(.product-row-template)').forEach((row, index) => {
                row.querySelector('.product-id-input').setAttribute('name', `produits[${index}]`);
                row.querySelector('.product-qty-input').setAttribute('name', `quantites[${index}]`);
            });
        }

        // Fonction pour ajouter une ligne de produit au tableau
        function addProductRow(product, quantity = 1, availableStock = 0) {
            // Vérifier si le produit existe déjà dans le tableau pour éviter les doublons
            let existingRowInput = Array.from(produitsTableBody.querySelectorAll('.product-id-input'))
                                        .find(input => input.value == product.id);

            if (existingRowInput) {
                // Si le produit existe déjà, incrémenter simplement la quantité
                const qtyInput = existingRowInput.closest('tr').querySelector('.product-qty-input');
                qtyInput.value = parseInt(qtyInput.value) + 1;
                return; // Quitter la fonction après la mise à jour de la ligne existante
            }

            const newRow = productRowTemplate.cloneNode(true);
            newRow.style.display = ''; // Rendre la ligne visible
            newRow.classList.remove('product-row-template'); // Supprimer la classe template

            // Remplir la ligne clonée avec les données du produit
            newRow.querySelector('.product-id-input').value = product.id;
            newRow.querySelector('.product-name-display').textContent = product.nom;
            // Assurez-vous que 'code' et 'reference' sont disponibles sur l'objet 'product'
            newRow.querySelector('.product-ref-code').textContent = `Référence: ${product.reference || ''} / Code: ${product.code || ''}`;
            newRow.querySelector('.product-description-display').textContent = product.description || ''; // AJOUT DE LA DESCRIPTION
            newRow.querySelector('.product-available-stock').textContent = availableStock;

            const qtyInput = newRow.querySelector('.product-qty-input');
            qtyInput.max = availableStock; // Définir la quantité max en fonction du stock disponible
            qtyInput.value = quantity;
            qtyInput.setAttribute('required', 'required'); // S'assurer que la quantité est requise

            // Ajouter l'écouteur d'événement pour le bouton de suppression sur la nouvelle ligne
            newRow.querySelector('.removeRow').addEventListener('click', function() {
                newRow.remove(); // Supprimer la ligne
                updateRowIndexes(); // Ré-indexer après la suppression
            });

            produitsTableBody.appendChild(newRow); // Ajouter la nouvelle ligne au corps du tableau
            updateRowIndexes(); // Mettre à jour les indices après l'ajout d'une nouvelle ligne
        }

        // --- Population initiale des lignes de transfert existantes ---
        // Assurez-vous que $transfert->ligneTransferts est chargé avec la relation 'produit'
        // dans le contrôleur (TransfertController@edit)
        const existingLignesTransfert = @json($transfert->ligneTransferts ?? []);
        console.log('Existing Lignes Transfert (from Blade):', existingLignesTransfert); // Log de débogage

        existingLignesTransfert.forEach(ligne => {
            console.log('Processing existing ligne:', ligne); // Log de débogage pour chaque ligne
            if (ligne.produit) { // S'assurer que la relation 'produit' existe
                const productData = {
                    id: ligne.produit.id,
                    nom: ligne.produit.nom,
                    code: ligne.produit.code || '',
                    reference: ligne.produit.reference || '',
                    description: ligne.produit.description || '' // Inclure la description
                };
                // Utiliser ligne.produit.quantite pour availableStock car c'est le stock agrégé
                addProductRow(productData, ligne.quantite, ligne.produit.quantite);
            } else {
                console.warn('Produit est null ou indéfini pour la ligne :', ligne); // Avertissement si le produit est manquant
            }
        });

        // --- Écouteur d'événement Livewire pour la sélection d'un nouveau produit ---
        window.addEventListener('productSelected', event => {
            const product = event.detail.product; // Détails du produit du composant Livewire
            const availableStock = event.detail.availableStock; // Stock disponible du composant Livewire
            console.log('Produit sélectionné depuis Livewire :', product, 'Stock disponible :', availableStock); // Log de débogage
            addProductRow(product, 1, availableStock); // Ajouter un nouveau produit avec une quantité par défaut de 1
        });

        // --- Écouteur d'événement pour les boutons de suppression (Délégation pour les futures lignes) ---
        produitsTableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow')) {
                const row = e.target.closest('tr');
                if (row && !row.classList.contains('product-row-template')) { // S'assurer de ne pas supprimer la ligne template
                    row.remove();
                    updateRowIndexes();
                }
            }
        });
    });
</script>
@endsection
