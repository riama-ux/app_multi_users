@extends('pages.admin.shared.layout')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Modifier la vente #{{ $vente->id }}</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <a href="{{ route('ventes.index') }}" class="btn btn-secondary">
                                <em class="icon ni ni-arrow-left"></em>
                                <span>Retour à la liste des ventes</span>
                            </a>
                        </div>
                    </div>
                </div>@if ($errors->any())
                    <div class="alert alert-danger alert-icon">
                        <em class="icon ni ni-alert-circle"></em>
                        <strong>Erreur de validation :</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-icon">
                        <em class="icon ni ni-cross-circle"></em> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('ventes.update', $vente->id) }}" method="POST" id="venteEditForm">
                    @csrf
                    @method('PUT')

                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="row g-4">
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="client_id">Client</label>
                                            <div class="form-control-wrap">
                                                <div class="input-group">
                                                    <select name="client_id" id="client_id" class="form-control form-select" required>
                                                        <option value="">-- Choisir un client --</option>
                                                        @foreach($clients as $client)
                                                            <option value="{{ $client->id }}" {{ (old('client_id', $vente->client_id) == $client->id) ? 'selected' : '' }}>
                                                                {{ $client->nom }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalClient">
                                                            <em class="icon ni ni-plus"></em>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="mode_paiement">Mode de paiement</label>
                                            <div class="form-control-wrap">
                                                <select name="mode_paiement" id="mode_paiement" class="form-control form-select" required>
                                                    <option value="especes" {{ (old('mode_paiement', $vente->mode_paiement) == 'especes') ? 'selected' : '' }}>Espèces</option>
                                                    <option value="mobile_money" {{ (old('mode_paiement', $vente->mode_paiement) == 'mobile_money') ? 'selected' : '' }}>Mobile Money</option>
                                                    <option value="virement" {{ (old('mode_paiement', $vente->mode_paiement) == 'virement') ? 'selected' : '' }}>Virement</option>
                                                    <option value="cheque" {{ (old('mode_paiement', $vente->mode_paiement) == 'cheque') ? 'selected' : '' }}>Chèque</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><div class="nk-block">
                        <div class="nk-block-head">
                            <h5 class="nk-block-title">Produits</h5>
                        </div>
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <div class="mb-4">
                                    {{-- Intégration du composant Livewire pour la recherche de produits --}}
                                    @livewire('vente-product-search')
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle" id="produitsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Produit</th>
                                                <th>Quantité</th>
                                                <th>Prix Unitaire (FCFA)</th>
                                                <th>Sous-total (FCFA)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Ligne de modèle cachée pour le clonage par JavaScript --}}
                                            <tr style="display: none;" class="product-row-template">
                                                <td>
                                                    <input type="hidden" class="product-id-input">
                                                    <input type="hidden" class="ligne-id-input"> {{-- Pour les lignes de vente existantes --}}
                                                    <span class="product-name-display"></span>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm quantite-input" min="1" value="1">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control form-control-sm prix-unitaire-input" min="0">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm sous-total-display" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger removeRow">Supprimer</button>
                                                </td>
                                            </tr>
                                            {{-- Les lignes de produits existantes seront pré-remplies ici par JavaScript --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><div class="nk-block">
                        <div class="nk-block-head">
                            <h5 class="nk-block-title">Résumé de la vente</h5>
                        </div>
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="remise">Remise (FCFA)</label>
                                            <input type="number" step="0.01" min="0" name="remise" id="remise" class="form-control" value="{{ old('remise', $vente->remise) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="total_ttc">Total TTC (FCFA)</label>
                                            <input type="text" name="total_ttc" id="total_ttc" class="form-control" readonly value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="montant_paye">Montant payé (FCFA)</label>
                                            <input type="number" step="0.01" min="0" name="montant_paye" id="montant_paye" class="form-control" value="{{ old('montant_paye', $vente->montant_paye) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="reste_a_payer">Reste à payer (FCFA)</label>
                                            <input type="text" id="reste_a_payer" class="form-control" readonly value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><div class="nk-block">
                        <button type="submit" class="btn btn-primary btn-lg mt-4">
                            <em class="icon ni ni-save"></em>
                            <span>Modifier la vente</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

{{-- ------------------------------------------------------------- --}}
{{-- MODALS : Client, Produit, Catégorie --}}
{{-- ------------------------------------------------------------- --}}

<div class="modal fade" id="modalClient" tabindex="-1" aria-labelledby="modalClientLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-client-modal" method="POST" action="{{ route('module.clients.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClientLabel">Ajouter un client</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Fermer">
                        <em class="icon ni ni-cross"></em>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="client-error"></div>
                    <div class="alert alert-success d-none" id="client-success"></div>
                    
                    <div class="form-group">
                        <label for="client-nom" class="form-label">Nom *</label>
                        <input type="text" name="nom" id="client-nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="client-telephone" class="form-label">Téléphone</label>
                        <input type="text" name="telephone" id="client-telephone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="client-email" class="form-label">Email</label>
                        <input type="email" name="email" id="client-email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="client-adresse" class="form-label">Adresse</label>
                        <input type="text" name="adresse" id="client-adresse" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalProduit" tabindex="-1" aria-labelledby="modalProduitLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="form-produit-modal" action="{{ route('produits.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProduitLabel">Créer un nouveau produit</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Fermer">
                        <em class="icon ni ni-cross"></em>
                    </button>
                </div>
                <div class="modal-body row g-3">
                    {{-- Champs du produit (inchangés) --}}
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" name="reference" id="reference" class="form-control" value="{{ old('reference') }}">
                    </div>
                    <div class="col-md-6">
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
                    <div class="col-md-6 d-flex flex-column">
                        <label>Catégorie *</label>
                        <div class="input-group">
                            <select name="categorie_id" id="selectCategorie" class="form-select" required>
                                <option value="" >-- Choisir --</option>
                                @foreach($categories as $categorie)
                                    <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCategorie">
                                <em class="icon ni ni-plus"></em>
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
                    <div class="col-md-12">
                        <label for="seuil_alerte" class="form-label">Seuil d'alerte (quantité) *</label>
                        <input type="number" name="seuil_alerte" id="seuil_alerte" class="form-control" value="{{ old('seuil_alerte') }}">
                    </div>
                </div>
                <div class="modal-footer bg-light">
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
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Fermer">
                        <em class="icon ni ni-cross"></em>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nom de la catégorie *</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary">Créer</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ------------------------------------------------------------- --}}
{{-- SCRIPTS JAVASCRIPT --}}
{{-- ------------------------------------------------------------- --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsTableBody = document.querySelector('#produitsTable tbody');
        const productRowTemplate = document.querySelector('.product-row-template');

        let currentLigneIndex = 0; // Index pour les noms d'input

        /**
         * Ajoute une ligne de produit au tableau de vente.
         * @param {Object} product - Données du produit.
         * @param {number} [quantity=1] - Quantité initiale.
         * @param {number|string} [prixUnitaire=''] - Prix unitaire spécifique (pour les lignes existantes).
         * @param {number|null} [ligneId=null] - ID de la ligne de vente existante.
         */
        function addProductRow(product, quantity = 1, prixUnitaire = '', ligneId = null) {
            // Empêcher les doublons lors de l'ajout via Livewire si la ligne n'est pas une ligne existante (ligneId === null)
            if (ligneId === null) {
                let existingRowInput = Array.from(produitsTableBody.querySelectorAll('.product-id-input'))
                                            .find(input => input.value == product.id);

                if (existingRowInput) {
                    const qtyInput = existingRowInput.closest('tr').querySelector('.quantite-input');
                    qtyInput.value = parseInt(qtyInput.value) + 1;
                    calculerTotaux();
                    return;
                }
            }

            const newRow = productRowTemplate.cloneNode(true);
            newRow.style.display = '';
            newRow.classList.remove('product-row-template');

            newRow.querySelector('.product-id-input').value = product.id;
            newRow.querySelector('.product-name-display').textContent = product.nom;

            if (ligneId) {
                newRow.querySelector('.ligne-id-input').value = ligneId;
            }

            const quantiteInput = newRow.querySelector('.quantite-input');
            quantiteInput.value = quantity;
            quantiteInput.setAttribute('required', 'required');
            quantiteInput.addEventListener('input', calculerTotaux); 

            const prixUnitaireInput = newRow.querySelector('.prix-unitaire-input');
            // Utiliser le prix unitaire passé ou le prix de vente par défaut du produit
            prixUnitaireInput.value = prixUnitaire !== '' ? prixUnitaire : (product.prix_vente || '');
            prixUnitaireInput.setAttribute('required', 'required');
            prixUnitaireInput.addEventListener('input', calculerTotaux);

            newRow.querySelector('.removeRow').addEventListener('click', function() {
                newRow.remove();
                updateRowIndexes();
                calculerTotaux();
            });

            produitsTableBody.appendChild(newRow);
            updateRowIndexes();
            calculerTotaux();
        }

        // Pré-remplir les lignes de vente existantes
        const existingLigneVentes = @json($vente->ligneVentes ?? []);
        existingLigneVentes.forEach(ligne => {
            if (ligne.produit) {
                const productData = {
                    id: ligne.produit.id,
                    nom: ligne.produit.nom,
                    prix_vente: ligne.produit.prix_vente 
                };
                // Utilisation des données existantes (quantité, prix unitaire, ID de la ligne)
                addProductRow(productData, ligne.quantite, ligne.prix_unitaire, ligne.id);
            } else {
                console.warn('Produit est null ou indéfini pour la ligne de vente :', ligne);
            }
        });


        // Fonction pour réindexer les noms des inputs
        function updateRowIndexes() {
            currentLigneIndex = 0;
            produitsTableBody.querySelectorAll('tr:not(.product-row-template)').forEach((row) => {
                row.querySelector('.product-id-input').setAttribute('name', `produits[${currentLigneIndex}][produit_id]`);
                const ligneIdInput = row.querySelector('.ligne-id-input');
                if (ligneIdInput) {
                    ligneIdInput.setAttribute('name', `produits[${currentLigneIndex}][id]`);
                }
                row.querySelector('.quantite-input').setAttribute('name', `produits[${currentLigneIndex}][quantite]`);
                row.querySelector('.prix-unitaire-input').setAttribute('name', `produits[${currentLigneIndex}][prix_unitaire]`);
                currentLigneIndex++;
            });
        }

        // Écouteur d'événement Livewire pour ajouter un produit
        window.addEventListener('productSelectedForVente', event => {
            const product = event.detail.product;
            addProductRow(product);
        });

        // Écouteurs de modification pour les calculs automatiques
        document.querySelector('#remise').addEventListener('input', calculerTotaux);
        document.querySelector('#montant_paye').addEventListener('input', calculerTotaux);

        // Fonction de calcul des totaux
        function calculerTotaux(){
            let totalLignes = 0;

            document.querySelectorAll('#produitsTable tbody tr:not(.product-row-template)').forEach(ligne => {
                const qte = parseFloat(ligne.querySelector('.quantite-input').value) || 0;
                const prix = parseFloat(ligne.querySelector('.prix-unitaire-input').value) || 0;
                const sousTotal = qte * prix;

                ligne.querySelector('.sous-total-display').value = sousTotal.toFixed(2);
                totalLignes += sousTotal;
            });

            const remise = parseFloat(document.querySelector('#remise').value) || 0;
            const montantPaye = parseFloat(document.querySelector('#montant_paye').value) || 0;

            const totalTTC = Math.max(totalLignes - remise, 0);
            const resteAPayer = Math.max(totalTTC - montantPaye, 0);

            document.querySelector('#total_ttc').value = totalTTC.toFixed(2);
            document.querySelector('#reste_a_payer').value = resteAPayer.toFixed(2);
        }

        // Calcul initial au chargement
        calculerTotaux();

        // ---------------------------------------------------------------------------------
        // Gestion des Modals (Client, Produit, Catégorie) via JavaScript/AJAX
        // ---------------------------------------------------------------------------------

        // 1. Modal Client
        const formClient = document.getElementById('form-client-modal');
        const modalClient = new bootstrap.Modal(document.getElementById('modalClient'));
        const selectClient = document.getElementById('client_id');
        const clientErrorDiv = document.getElementById('client-error');
        const clientSuccessDiv = document.getElementById('client-success');

        formClient.addEventListener('submit', function(e) {
            e.preventDefault();

            clientErrorDiv.classList.add('d-none');
            clientSuccessDiv.classList.add('d-none');

            fetch(formClient.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': formClient.querySelector('input[name="_token"]').value
                },
                body: new FormData(formClient)
            })
            .then(response => {
                if (!response.ok) return response.json().then(data => Promise.reject(data));
                return response.json();
            })
            .then(data => {
                if (data.success && data.client) {
                    modalClient.hide();
                    const option = new Option(data.client.nom, data.client.id, true, true);
                    selectClient.appendChild(option);
                    selectClient.value = data.client.id; // Sélectionne le nouveau client
                    clientSuccessDiv.innerHTML = 'Client ajouté avec succès!';
                    clientSuccessDiv.classList.remove('d-none');
                    formClient.reset();
                } else {
                    clientErrorDiv.innerHTML = data.message || 'Impossible d\'ajouter le client.';
                    clientErrorDiv.classList.remove('d-none');
                }
            })
            .catch(err => {
                let errorMessage = 'Une erreur est survenue.';
                if (err.errors) {
                    errorMessage = Object.values(err.errors).map(arr => arr.join('<br>')).join('<br>');
                } else if (err.message) {
                    errorMessage = err.message;
                }
                clientErrorDiv.innerHTML = errorMessage;
                clientErrorDiv.classList.remove('d-none');
            });
        });

        // 2. Modal Produit
        const formProduit = document.getElementById('form-produit-modal');
        const modalProduit = new bootstrap.Modal(document.getElementById('modalProduit'));

        // Réinitialiser le formulaire de produit et masquer les alertes/erreurs à l'ouverture du modal
        document.getElementById('modalProduit').addEventListener('show.bs.modal', function (event) {
            formProduit.reset();
            formProduit.querySelectorAll('.alert').forEach(alert => alert.classList.add('d-none'));
            formProduit.querySelectorAll('.text-danger').forEach(error => error.remove());
            formProduit.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
        });

        // Gestion de l'envoi du formulaire de produit via AJAX
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
                // Gérer les erreurs de validation HTTP 422 ou autres
                if (!response.ok) {
                    return response.json().then(data => Promise.reject(data));
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.produit) {
                    modalProduit.hide();
                    // Envoyer un événement Livewire pour ajouter le produit à la table de vente
                    Livewire.dispatch('productSelectedForVente', { product: data.produit });
                    formProduit.reset();
                } else {
                    alert(data.message || 'Une erreur est survenue lors de la création du produit.');
                }
            })
            .catch(err => {
                let errorMessage = 'Une erreur est survenue.';
                if (err.errors) {
                    // Afficher les erreurs de validation
                    errorMessage = Object.values(err.errors).map(arr => arr.join('<br>')).join('<br>');
                    
                    // Ajouter les classes et messages d'erreur aux inputs
                    for (const field in err.errors) {
                        const input = formProduit.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            let errorDiv = input.nextElementSibling;
                            if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
                                errorDiv = document.createElement('div');
                                errorDiv.classList.add('invalid-feedback');
                                input.parentNode.appendChild(errorDiv);
                            }
                            errorDiv.innerHTML = err.errors[field].join('<br>');
                        }
                    }
                } else if (err.message) {
                    errorMessage = err.message;
                }
                alert(errorMessage);
            });
        });

        // 3. Modal Catégorie
        const formCategorie = document.querySelector('#modalCategorie form');
        const modalCategorie = new bootstrap.Modal(document.getElementById('modalCategorie'));
        const selectCategorie = document.getElementById('selectCategorie');

        formCategorie.addEventListener('submit', function(e) {
            e.preventDefault();

            fetch(formCategorie.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': formCategorie.querySelector('input[name="_token"]').value
                },
                body: new FormData(formCategorie)
            })
            .then(response => {
                if (!response.ok) return response.json().then(data => Promise.reject(data));
                return response.json();
            })
            .then(data => {
                if (data.success && data.categorie) {
                    modalCategorie.hide();
                    const option = new Option(data.categorie.nom, data.categorie.id, true, true);
                    selectCategorie.appendChild(option);
                    selectCategorie.value = data.categorie.id;
                    formCategorie.reset();
                } else {
                    alert(data.message || 'Impossible d\'ajouter la catégorie.');
                }
            })
            .catch(err => {
                alert(err.message || 'Une erreur est survenue lors de l\'ajout de la catégorie.');
            });
        });
    });
</script>
@endsection