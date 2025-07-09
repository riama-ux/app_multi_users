@extends('pages.admin.shared.layout')

@section('content')
    <h1>Nouvelle commande</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalProduit">
        + Nouveau produit
    </button>

    <form action="{{ route('commandes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="fournisseur_id" class="form-label">Fournisseur *</label>
            <select name="fournisseur_id" id="selectFournisseur" class="form-select" required>
                <option value="">-- Choisir un fournisseur --</option>
                @foreach ($fournisseurs as $fournisseur)
                    <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                        {{ $fournisseur->nom }}
                    </option>
                @endforeach
            </select>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFournisseur">+</button>
        </div>

        <div class="mb-3">
            <label for="date_commande" class="form-label">Date de commande *</label>
            <input type="date" name="date_commande" id="date_commande" class="form-control" value="{{ old('date_commande', date('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label for="date_prevue_livraison" class="form-label">Date prévue de livraison</label>
            <input type="date" name="date_prevue_livraison" id="date_prevue_livraison" class="form-control" value="{{ old('date_prevue_livraison') }}" required>
            @error('date_prevue_livraison')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <h4>Lignes de commande</h4>

        <table class="table" id="lignes-commande-table">
            <thead>
                <tr>
                    <th>Produit *</th>
                    <th>Code</th>
                    <th>Référence</th>
                    <th>Quantité *</th>
                    <th>Prix unitaire *</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr class="ligne-commande">
                    <td>
                        <select name="lignes[0][produit_id]" class="form-select produit-select" required>
                            <option value="">-- Choisir un produit --</option>
                            @foreach ($produits as $produit)
                                <option value="{{ $produit->id }}" data-code="{{ $produit->code }}" data-reference="{{ $produit->reference }}">{{ $produit->nom }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" class="form-control code-input" name="lignes[0][code]"></td>
                    <td><input type="text" class="form-control reference-input" name="lignes[0][reference]"></td>
                    <td><input type="number" name="lignes[0][quantite]" class="form-control" min="1" required></td>
                    <td><input type="number" step="0.01" name="lignes[0][prix_unitaire]" class="form-control" min="0" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-ligne">-</button></td>
                </tr>
            </tbody>
        </table>

        <button type="button" id="add-ligne" class="btn btn-secondary mb-3">Ajouter une ligne</button>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('commandes.index') }}" class="btn btn-secondary">Annuler</a>
    </form>


    <div class="modal fade" id="modalProduit" tabindex="-1" aria-labelledby="modalProduitLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" id="form-produit-modal" action="{{ route('produits.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalProduitLabel">Créer un nouveau produit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body row g-3">

                        <div class="col-md-2">
                            <label for="nom" class="form-label">Nom *</label>
                            <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
                        </div>

                        <div class="col-md-2">
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" name="reference" id="reference" class="form-control" value="{{ old('reference') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="code">Code </label>
                            <input type="text" name="code" class="form-control" value="{{ old('code') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="marque">Marque *</label>
                            <input type="text" name="marque" class="form-control" value="{{ old('marque', $produit->marque ?? '') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="unite">Unité*</label>
                            <select name="unite" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach(['pièce', 'kg', 'litre', 'mètre', 'paquet'] as $unit)
                                    <option value="{{ $unit }}" {{ old('unite', $produit->unite ?? '') == $unit ? 'selected' : '' }}>
                                        {{ ucfirst($unit) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <label class="w-100">Catégorie *</label>
                            <div class="input-group">
                                <select name="categorie_id" id="selectCategorie" class="form-select" required>
                                    <option value="" >-- Choisir --</option>
                                    @foreach($categories as $categorie)
                                        <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCategorie">
                                    +
                                </button>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="description">Description *</label>
                            <textarea name="description" class="form-control" required>{{ old('description', $produit->description ?? '') }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label for="cout_achat" class="form-label">Coût d'achat par défaut *</label>
                            <input type="number" step="0.01" name="cout_achat" id="cout_achat" class="form-control" value="{{ old('cout_achat') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="prix_vente" class="form-label">Prix de vente par défaut *</label>
                            <input type="number" step="0.01" name="prix_vente" id="prix_vente" class="form-control" value="{{ old('prix_vente') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="marge" class="form-label">Marge (%) *</label>
                            <input type="number" step="0.01" name="marge" id="marge" class="form-control" value="{{ old('marge') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="seuil_alerte" class="form-label">Seuil d'alerte (quantité) *</label>
                            <input type="number" name="seuil_alerte" id="seuil_alerte" class="form-control" value="{{ old('seuil_alerte') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Créer</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalCategorie" tabindex="-1" aria-labelledby="modalCategorieLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('module.categories.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCategorieLabel">Nouvelle catégorie</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <label>Nom de la catégorie *</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Créer</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalFournisseur" tabindex="-1" aria-labelledby="modalFournisseurLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-fournisseur-modal" method="POST" action="{{ route('module.fournisseurs.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="modalFournisseurLabel">Ajouter un fournisseur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="telephone" class="form-label">Téléphone</label>
                    <input type="text" name="telephone" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" name="adresse" class="form-control">
                </div>
                <div id="fournisseur-error" class="alert alert-danger d-none"></div>
                <div id="fournisseur-success" class="alert alert-success d-none"></div>
                </div>
                <div class="modal-footer">
                <button type="submit" class="btn btn-success">Enregistrer</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    {{-- Intégration des données des produits en JSON pour JavaScript --}}
    <script>
        // Use a mutable array for allProducts
        let allProducts = @json($produits);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const coutAchatInput = document.getElementById('cout_achat');
            const prixVenteInput = document.getElementById('prix_vente');
            const margeInput = document.getElementById('marge');

            function updateMarge() {
                const cout = parseFloat(coutAchatInput.value);
                const prix = parseFloat(prixVenteInput.value);

                if (!isNaN(cout) && cout > 0 && !isNaN(prix)) {
                    const marge = ((prix - cout) / cout) * 100;
                    margeInput.value = marge.toFixed(2);
                } else {
                    margeInput.value = ''; // Effacer la marge si les entrées sont invalides
                }
            }

            coutAchatInput.addEventListener('input', updateMarge);
            prixVenteInput.addEventListener('input', updateMarge);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formProduit = document.getElementById('form-produit-modal');
            const modalProduit = new bootstrap.Modal(document.getElementById('modalProduit'));
            const lignesCommandeTableBody = document.querySelector('#lignes-commande-table tbody');

            let targetSelect = null; // Pour suivre quel sélecteur de produit a déclenché le modal

            // Écouteur d'événement pour l'affichage du modal produit, afin de déterminer quel sélecteur l'a déclenché
            document.getElementById('modalProduit').addEventListener('show.bs.modal', function (event) {
                // Tenter de trouver le dernier sélecteur de produit vide ou le dernier tout court
                const allProduitSelects = lignesCommandeTableBody.querySelectorAll('.produit-select');
                targetSelect = null;
                for (let i = allProduitSelects.length - 1; i >= 0; i--) {
                    if (!allProduitSelects[i].value) {
                        targetSelect = allProduitSelects[i];
                        break;
                    }
                }
                if (!targetSelect && allProduitSelects.length > 0) {
                    targetSelect = allProduitSelects[allProduitSelects.length - 1];
                }
            });


            formProduit.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(formProduit);

                fetch(formProduit.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': formProduit.querySelector('input[name="_token"]').value
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => Promise.reject(data));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.produit) {
                        // Fermer le modal
                        modalProduit.hide();

                        // Add the new product to the local allProducts array
                        allProducts.push(data.produit);

                        // Add the new product to all existing product dropdowns
                        document.querySelectorAll('.produit-select').forEach(select => {
                            const option = new Option(data.produit.nom, data.produit.id);
                            // Only append if the option doesn't already exist to prevent duplicates
                            if (!Array.from(select.options).some(opt => opt.value == data.produit.id)) {
                                select.appendChild(option);
                            }
                        });

                        if (targetSelect) {
                            // Set the value of the target select to the newly created product's ID
                            targetSelect.value = data.produit.id;
                            // Manually trigger the 'change' event on the target select
                            // This will make sure the code and reference inputs get updated.
                            targetSelect.dispatchEvent(new Event('change'));
                        }

                        // Réinitialiser le formulaire modal pour une nouvelle utilisation propre
                        formProduit.reset();
                    } else {
                        alert('Erreur: ' + (data.message || 'Impossible de créer le produit'));
                    }
                })
                .catch(err => {
                    let errorMessage = 'Une erreur est survenue.';
                    if (err.errors) {
                        errorMessage = Object.values(err.errors).map(arr => arr.join('<br>')).join('<br>');
                    } else if (err.message) {
                        errorMessage = err.message;
                    }
                    alert(errorMessage);
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formFournisseur = document.getElementById('form-fournisseur-modal');
            const modalFournisseur = new bootstrap.Modal(document.getElementById('modalFournisseur'));
            const selectFournisseur = document.getElementById('selectFournisseur');
            const errorDiv = document.getElementById('fournisseur-error');
            const successDiv = document.getElementById('fournisseur-success');

            formFournisseur.addEventListener('submit', function(e) {
                e.preventDefault();

                errorDiv.classList.add('d-none');
                successDiv.classList.add('d-none');
                errorDiv.innerHTML = '';
                successDiv.innerHTML = '';

                const formData = new FormData(formFournisseur);

                fetch(formFournisseur.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': formFournisseur.querySelector('input[name="_token"]').value
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) return response.json().then(data => Promise.reject(data));
                    return response.json();
                })
                .then(data => {
                    if(data.success && data.fournisseur) {
                        // Ajouter à la liste et sélectionner
                        const option = new Option(data.fournisseur.nom, data.fournisseur.id, true, true);
                        selectFournisseur.appendChild(option);
                        selectFournisseur.value = data.fournisseur.id;

                        // Afficher un message de succès temporaire
                        successDiv.textContent = "Fournisseur ajouté avec succès.";
                        successDiv.classList.remove('d-none');

                        // Réinitialiser le formulaire modal
                        formFournisseur.reset();

                        // Fermer le modal après 1.5s
                        setTimeout(() => modalFournisseur.hide(), 1500);
                    } else {
                        errorDiv.textContent = data.message || 'Erreur lors de la création.';
                        errorDiv.classList.remove('d-none');
                    }
                })
                .catch(errors => {
                    if(errors.errors) {
                        // Afficher les erreurs de validation Laravel
                        errorDiv.innerHTML = Object.values(errors.errors).map(arr => arr.join('<br>')).join('<br>');
                        errorDiv.classList.remove('d-none');
                    } else {
                        errorDiv.textContent = errors.message || 'Erreur serveur.';
                        errorDiv.classList.remove('d-none');
                    }
                });
            });
        });
    </script>

    <script>
        let index = 1; // Index de départ pour les nouvelles lignes

        // Fonction pour appliquer les écouteurs d'événements à une ligne donnée
        function applyProductListeners(row) {
            const produitSelect = row.querySelector('.produit-select');
            const codeInput = row.querySelector('.code-input');
            const referenceInput = row.querySelector('.reference-input');
            const removeButton = row.querySelector('.remove-ligne');

            // --- Écouteur d'événement pour le sélecteur de produit ---
            produitSelect.addEventListener('change', function () {
                const selectedProductId = this.value;
                // Trouver le produit correspondant dans le tableau allProducts
                const selectedProduct = allProducts.find(p => p.id == selectedProductId);

                if (selectedProduct) {
                    codeInput.value = selectedProduct.code || '';
                    referenceInput.value = selectedProduct.reference || '';
                } else {
                    // Si rien n'est sélectionné, vider les champs code et référence
                    codeInput.value = '';
                    referenceInput.value = '';
                }
            });

            // --- Écouteur d'événement pour le champ Code ---
            codeInput.addEventListener('input', function () {
                const enteredCode = this.value.trim();
                // Trouver le produit correspondant par le code (insensible à la casse)
                const matchedProduct = allProducts.find(p => p.code && p.code.toLowerCase() === enteredCode.toLowerCase());

                if (enteredCode === '') {
                    // Si le code est vidé, vider le sélecteur de produit et la référence
                    produitSelect.value = '';
                    referenceInput.value = '';
                    return;
                }

                if (matchedProduct) {
                    // Sélectionner le produit dans le dropdown
                    produitSelect.value = matchedProduct.id;
                    // Remplir le champ référence
                    referenceInput.value = matchedProduct.reference || '';
                } else {
                    // Si aucune correspondance, vider le sélecteur de produit et la référence
                    produitSelect.value = '';
                    referenceInput.value = '';
                }
            });

            // --- Écouteur d'événement pour le champ Référence ---
            referenceInput.addEventListener('input', function () {
                const enteredReference = this.value.trim();
                // Trouver le produit correspondant par la référence (insensible à la casse)
                const matchedProduct = allProducts.find(p => p.reference && p.reference.toLowerCase() === enteredReference.toLowerCase());

                if (enteredReference === '') {
                    // Si la référence est vidée, vider le sélecteur de produit et le code
                    produitSelect.value = '';
                    codeInput.value = '';
                    return;
                }

                if (matchedProduct) {
                    // Sélectionner le produit dans le dropdown
                    produitSelect.value = matchedProduct.id;
                    // Remplir le champ code
                    codeInput.value = matchedProduct.code || '';
                } else {
                    // Si aucune correspondance, vider le sélecteur de produit et le code
                    produitSelect.value = '';
                    codeInput.value = '';
                }
            });


            // --- Écouteur d'événement pour le bouton Supprimer ---
            removeButton.addEventListener('click', function () {
                // S'assurer qu'au moins une ligne reste
                if (document.querySelectorAll('.ligne-commande').length > 1) {
                    this.closest('tr').remove();
                } else {
                    alert("Vous devez avoir au moins une ligne de commande.");
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const tbody = document.querySelector('#lignes-commande-table tbody');
            const addLigneButton = document.getElementById('add-ligne');

            // Appliquer les écouteurs à la ligne initiale
            document.querySelectorAll('.ligne-commande').forEach(row => {
                applyProductListeners(row);
            });

            addLigneButton.addEventListener('click', () => {
                const originalRow = document.querySelector('.ligne-commande');
                const newRow = originalRow.cloneNode(true); // Cloner en profondeur

                // Réinitialiser les valeurs pour la nouvelle ligne et mettre à jour les noms des champs
                newRow.querySelectorAll('select, input').forEach(el => {
                    if (el.name) {
                        // Remplacer l'index dans le nom du champ (ex: lignes[0] devient lignes[1])
                        el.name = el.name.replace(/lignes\[\d+\]/, `lignes[${index}]`);
                    }
                    if (el.tagName === 'SELECT') {
                        el.value = ''; // Réinitialiser le sélecteur à l'option par défaut
                    } else if (el.type === 'number' || el.type === 'text' || el.type === 'email') {
                        el.value = ''; // Vider les champs texte et numériques
                    }
                });

                tbody.appendChild(newRow);
                index++; // Incrémenter l'index pour la prochaine ligne

                // Appliquer les écouteurs à la nouvelle ligne créée
                applyProductListeners(newRow);
            });
        });
    </script>
@endsection