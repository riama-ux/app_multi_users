@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid">
    <h1>Modifier l'Ajustement de Stock #{{ $ajustement->id }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('ajustements.update', $ajustement->id) }}" method="POST" id="ajustementEditForm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="date_ajustement" class="form-label">Date et heure de l'ajustement *</label>
            <input type="datetime-local" name="date_ajustement" id="date_ajustement" class="form-control" value="{{ old('date_ajustement', $ajustement->date_ajustement->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type d'ajustement *</label>
            <select name="type" id="type" class="form-control" required>
                <option value="">-- Sélectionner le type --</option>
                <option value="entree" {{ old('type', $ajustement->type) == 'entree' ? 'selected' : '' }}>Entrée (Ajout de stock)</option>
                <option value="sortie" {{ old('type', $ajustement->type) == 'sortie' ? 'selected' : '' }}>Sortie (Retrait de stock)</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="motif_global" class="form-label">Motif global de l'ajustement</label>
            <textarea name="motif_global" id="motif_global" class="form-control" rows="3">{{ old('motif_global', $ajustement->motif_global) }}</textarea>
        </div>

        <h4>Produits à ajuster</h4>

        {{-- Composant Livewire pour la recherche de produits --}}
        <div class="mb-4">
            @livewire('ajustement-product-search')
        </div>

        <table class="table table-bordered" id="produitsAjustementTable">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Stock Actuel</th>
                    <th>Quantité Ajustée *</th>
                    <th>Prix Unitaire Ajusté (pour entrée)</th>
                    <th>Motif Spécifique</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- Ligne de modèle cachée pour le clonage par JavaScript --}}
                <tr style="display: none;" class="product-ajustement-row-template">
                    <td>
                        <input type="hidden" class="product-id-input" data-name="lignes[idx][produit_id]" dislable>
                        <input type="hidden" class="ligne-ajustement-id-input" data-name="lignes[idx][id]" > {{-- Pour les lignes existantes --}}
                        <span class="product-name-display"></span>
                    </td>
                    <td>
                        <span class="product-current-stock-display text-info fw-bold"></span>
                    </td>
                    <td>
                        <input type="number" class="form-control quantite-ajustee-input" min="0.01" value="1" required data-name="lignes[idx][quantite_ajustee]" dislable>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control prix-unitaire-ajuste-input" min="0" data-name="lignes[idx][prix_unitaire_ajuste]" dislable>
                    </td>
                    <td>
                        <input type="text" class="form-control motif-ligne-input" placeholder="Motif spécifique (optionnel)" data-name="lignes[idx][motif_ligne]" dislable>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">Supprimer</button>
                    </td>
                </tr>
                {{-- Les lignes de produits existantes seront pré-remplies ici par JavaScript --}}
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary mt-4">Mettre à jour l'Ajustement</button>
        <a href="{{ route('ajustements.index') }}" class="btn btn-secondary mt-4">Annuler</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsAjustementTableBody = document.querySelector('#produitsAjustementTable tbody');
        const productAjustementRowTemplate = document.querySelector('.product-ajustement-row-template');
        const ajustementEditForm = document.getElementById('ajustementEditForm');

        // Fonction pour ajouter une ligne de produit au tableau d'ajustement
        function addProductAjustementRow(product, quantiteAjustee = 1, prixUnitaireAjuste = '', motifLigne = '', ligneId = null) {
            // Vérifier si le produit existe déjà par son ID pour éviter les doublons lors de l'ajout via Livewire
            if (ligneId === null) { // Seulement si ce n'est pas une ligne existante qu'on pré-remplit
                let existingRowInput = Array.from(produitsAjustementTableBody.querySelectorAll('.product-id-input'))
                                            .find(input => input.value == product.id);

                if (existingRowInput) {
                    alert('Ce produit est déjà dans la liste d\'ajustement.');
                    return;
                }
            }

            const newRow = productAjustementRowTemplate.cloneNode(true);
            newRow.style.display = '';
            newRow.classList.remove('product-ajustement-row-template');
            newRow.classList.add('product-ajustement-row'); // Ajouter une classe pour les lignes réelles

            // Assigner les valeurs des inputs
            newRow.querySelector('.product-id-input').value = product.id;
            newRow.querySelector('.product-name-display').textContent = product.nom;
            newRow.querySelector('.product-current-stock-display').textContent = product.quantite; // Afficher le stock actuel

            if (ligneId) {
                const ligneAjustementIdInput = newRow.querySelector('.ligne-ajustement-id-input');
                ligneAjustementIdInput.value = ligneId;
                // Laisser l'attribut name vide ici, il sera mis à jour par updateRowIndexes
            }

            newRow.querySelector('.quantite-ajustee-input').value = quantiteAjustee;
            newRow.querySelector('.prix-unitaire-ajuste-input').value = prixUnitaireAjuste;
            newRow.querySelector('.motif-ligne-input').value = motifLigne;


            newRow.querySelector('.removeRow').addEventListener('click', function() {
                newRow.remove();
                updateRowIndexes(); // Réindexer après suppression
            });

            produitsAjustementTableBody.appendChild(newRow);
            updateRowIndexes(); // Appeler pour s'assurer que tous les index sont corrects après l'ajout
        }

        // Pré-remplir les lignes d'ajustement existantes
        const existingLignesAjustement = @json($ajustement->lignesAjustement ?? []);
        existingLignesAjustement.forEach(ligne => {
            if (ligne.produit) {
                const productData = {
                    id: ligne.produit.id,
                    nom: ligne.produit.nom,
                    quantite: ligne.produit.quantite // Stock actuel du produit
                };
                addProductAjustementRow(productData, ligne.quantite_ajustee, ligne.prix_unitaire_ajuste, ligne.motif_ligne, ligne.id);
            } else {
                console.warn('Produit est null ou indéfini pour la ligne d\'ajustement :', ligne);
            }
        });

        // Fonction pour réindexer les noms des inputs après suppression ou ajout
        function updateRowIndexes() {
            let index = 0; // Réinitialiser l'index
            produitsAjustementTableBody.querySelectorAll('tr.product-ajustement-row').forEach((row) => {
                // Assigner les attributs 'name' en utilisant l'index courant
                row.querySelector('.product-id-input').setAttribute('name', `lignes[${index}][produit_id]`);
                const ligneAjustementIdInput = row.querySelector('.ligne-ajustement-id-input');
                if (ligneAjustementIdInput) {
                    ligneAjustementIdInput.setAttribute('name', `lignes[${index}][id]`);
                }
                row.querySelector('.quantite-ajustee-input').setAttribute('name', `lignes[${index}][quantite_ajustee]`);
                row.querySelector('.prix-unitaire-ajuste-input').setAttribute('name', `lignes[${index}][prix_unitaire_ajuste]`);
                row.querySelector('.motif-ligne-input').setAttribute('name', `lignes[${index}][motif_ligne]`);
                index++;
            });
        }

        // Écouteur d'événement Livewire pour ajouter un produit à la table
        window.addEventListener('productSelectedForAjustement', event => {
            const product = event.detail.product;
            addProductAjustementRow(product, 1, product.cout_achat); // Par défaut 1 quantité, coût d'achat comme prix ajusté
        });

        // =====================================================================
        // Gestion de la soumission du formulaire
        // =====================================================================
        ajustementEditForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Empêcher la soumission par défaut du navigateur

            // S'assurer que tous les inputs ont les bons noms avant la soumission
            updateRowIndexes();

            // Créer un nouvel objet FormData à partir du formulaire après la mise à jour des noms
            const formData = new FormData(ajustementEditForm);

            // Soumettre le formulaire manuellement via fetch
            fetch(ajustementEditForm.action, {
                method: 'POST', // La méthode sera PUT grâce à @method('PUT')
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Important pour les requêtes AJAX de Laravel
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Assurez-vous d'avoir cette balise meta dans votre layout
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirection ou affichage d'un message de succès
                    window.location.href = data.redirect || '{{ route('ajustements.index') }}';
                } else {
                    // Afficher les erreurs de validation ou autres messages d'erreur
                    let errorMessage = 'Erreur lors de la soumission du formulaire.';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).map(arr => arr.join('<br>')).join('<br>');
                    } else if (data.message) {
                        errorMessage = data.message;
                    }
                    alert(errorMessage); // Utiliser une alerte simple pour l'instant
                }
            })
            .catch(error => {
                console.error('Erreur lors de la soumission:', error);
                alert('Une erreur inattendue est survenue lors de la soumission.');
            });
        });

        // Appel initial pour les lignes pré-remplies par old('lignes') ou $ajustement->lignesAjustement
        updateRowIndexes();
    });
</script>
@endsection
