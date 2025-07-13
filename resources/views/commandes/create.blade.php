@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid py-4">

    <div class="nk-block-head-content d-flex justify-content-between align-items-center mb-4">
            <h3 class="nk-block-title">Nouvelle Commande</h3>
            <a href="{{ route('commandes.index') }}" class="btn btn-outline-primary">
                <em class="icon ni ni-list"></em><span>Liste des commandes</span>
            </a>
    </div>

    <div class="card shadow-lg border-0 mb-5">
        
        <div class="card-body p-5">

            @if ($errors->any())
                <div class="alert alert-danger fade show mb-4" role="alert">
                    <strong class="d-block mb-2">Erreurs de validation :</strong>
                    <ul class="mb-0 list-unstyled">
                        @foreach ($errors->all() as $error)
                            <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('commandes.store') }}" method="POST">
                @csrf

                <div class="row g-5">
                    
                    <div class="col-lg-5">
                        <div class="p-4 border rounded shadow-sm bg-light h-100">
                            <h5 class="text-primary mb-4 pb-2 border-bottom">Informations générales</h5>
                            
                            <div class="mb-4">
                                <label for="fournisseur_id" class="form-label fw-bold">Fournisseur <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="fournisseur_id" id="selectFournisseur" class="form-select" required>
                                        <option value="">-- Choisir un fournisseur --</option>
                                        @foreach ($fournisseurs as $fournisseur)
                                            <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                                {{ $fournisseur->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalFournisseur" title="Ajouter un fournisseur">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="date_commande" class="form-label fw-bold">Date et heure de commande <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="date_commande" id="date_commande" class="form-control" value="{{ old('date_commande', now()->format('Y-m-d\TH:i')) }}" required>
                            </div>

                            <div class="mb-0">
                                <label for="date_prevue_livraison" class="form-label fw-bold">Date et heure prévue de livraison <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="date_prevue_livraison" id="date_prevue_livraison" class="form-control" value="{{ old('date_prevue_livraison', now()->addDays(7)->format('Y-m-d\TH:i')) }}" required>
                                @error('date_prevue_livraison')
                                    <div class="text-danger mt-1"><small>{{ $message }}</small></div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="h-100">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="text-dark">Produits de la commande</h5>
                                <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalProduit">
                                    <i class="bi bi-box-seam me-2"></i>Nouveau produit
                                </button>
                            </div>

                            <div class="mb-4">
                                {{-- Intégration du composant Livewire pour la recherche de produits --}}
                                @livewire('commande-product-search')
                            </div>

                            <div class="table-responsive border rounded bg-white shadow-sm p-3">
                                <table class="table table-hover table-borderless mb-0" id="lignes-commande-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="py-3">Produit <span class="text-danger">*</span></th>
                                            <th class="py-3" style="width: 150px;">Quantité <span class="text-danger">*</span></th>
                                            <th class="py-3" style="width: 150px;">Prix unitaire <span class="text-danger">*</span></th>
                                            <th class="py-3" style="width: 80px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Ligne de modèle cachée pour le clonage par JavaScript --}}
                                        <tr style="display: none;" class="ligne-commande-template">
                                            <td class="align-middle">
                                                <input type="hidden" class="produit-id-input">
                                                <span class="product-name-display fw-bold text-muted"></span>
                                            </td>
                                            <td class="align-middle">
                                                <input type="number" class="form-control form-control-sm quantite-input" min="1" value="1" placeholder="Quantité">
                                            </td>
                                            <td class="align-middle">
                                                <input type="number" step="0.01" class="form-control form-control-sm prix-unitaire-input" min="0" placeholder="Prix">
                                            </td>
                                            <td class="align-middle">
                                                <button type="button" class="btn btn-danger btn-sm remove-ligne" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 my-5">
                    <button type="submit" class="btn btn-primary px-4 py-4">
                        <i class="bi bi-check-circle me-2"></i> Créer la commande
                    </button>
                    <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary px-4 py-4">
                        <i class="bi bi-x-circle me-2"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ---------------------------------------------------------------------- --}}
{{-- Modals --}}
{{-- ---------------------------------------------------------------------- --}}

{{-- Modal Nouveau Produit --}}
<div class="modal fade" id="modalProduit" tabindex="-1" aria-labelledby="modalProduitLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable bg-white">
        <form method="POST" id="form-produit-modal" action="{{ route('produits.store') }}">
            @csrf
            <div class="modal-content bg-white border-0 shadow-lg">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="modalProduitLabel">Créer un nouveau produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-danger d-none" id="product-modal-error-alert" role="alert"></div>
                    
                    <div class="row g-3 mb-4">
                        
                        <div class="col-md-3">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="marque" class="form-label">Marque <span class="text-danger">*</span></label>
                            <input type="text" name="marque" id="marque" class="form-control" value="{{ old('marque') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" name="reference" id="reference" class="form-control" value="{{ old('reference') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="unite" class="form-label">Unité <span class="text-danger">*</span></label>
                            <select name="unite" id="unite" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach(['pièce', 'kg', 'litre', 'mètre', 'paquet'] as $unit)
                                    <option value="{{ $unit }}" {{ old('unite') == $unit ? 'selected' : '' }}>
                                        {{ ucfirst($unit) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="selectCategorie" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select name="categorie_id" id="selectCategorie" class="form-select" required>
                                    <option value="" >-- Choisir --</option>
                                    @foreach($categories as $categorie)
                                        <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCategorie" title="Ajouter une catégorie">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-122">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        
                        <div class="col-md-4">
                            <label for="cout_achat" class="form-label">Coût d'achat par défaut <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="cout_achat" id="cout_achat" class="form-control" value="{{ old('cout_achat') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="prix_vente" class="form-label">Prix de vente par défaut <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="prix_vente" id="prix_vente" class="form-control" value="{{ old('prix_vente') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="marge" class="form-label">Marge (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="marge" id="marge" class="form-control bg-light" value="{{ old('marge') }}" required readonly>
                        </div>
                        <div class="col-md-12">
                            <label for="seuil_alerte" class="form-label">Seuil d'alerte (quantité) <span class="text-danger">*</span></label>
                            <input type="number" name="seuil_alerte" id="seuil_alerte" class="form-control" value="{{ old('seuil_alerte') }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center align-item-center border-top pt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Créer</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Nouvelle Catégorie --}}
<div class="modal fade" id="modalCategorie" tabindex="-1" aria-labelledby="modalCategorieLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="form-categorie-modal" action="{{ route('module.categories.store') }}">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="modalCategorieLabel">Nouvelle catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="categorie_nom" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="categorie_nom" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center align-item-center border-top pt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Créer</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Nouveau Fournisseur --}}
<div class="modal fade" id="modalFournisseur" tabindex="-1" aria-labelledby="modalFournisseurLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <form id="form-fournisseur-modal" method="POST" action="{{ route('module.fournisseurs.store') }}">
            @csrf
            <div class="modal-content bg-white border-0 shadow-lg">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="modalFournisseurLabel">Ajouter un fournisseur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body row g-3 p-4">
                    <div id="fournisseur-error" class="alert alert-danger d-none"></div>
                    <div id="fournisseur-success" class="alert alert-success d-none"></div>

                    <div class="col-md-6 mb-3">
                        <label for="fournisseur_nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="fournisseur_nom" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" name="adresse" id="adresse" class="form-control">
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center align-item-center border-top pt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Enregistrer</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ---------------------------------------------------------------------- --}}
{{-- Scripts JavaScript --}}
{{-- ---------------------------------------------------------------------- --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Calcul de la marge dans le modal produit ---
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
                margeInput.value = '';
            }
        }

        if (coutAchatInput && prixVenteInput && margeInput) {
            coutAchatInput.addEventListener('input', updateMarge);
            prixVenteInput.addEventListener('input', updateMarge);
        }

        // --- Gestion de la table des lignes de commande ---
        const formProduit = document.getElementById('form-produit-modal');
        const modalProduit = new bootstrap.Modal(document.getElementById('modalProduit'));
        const lignesCommandeTableBody = document.querySelector('#lignes-commande-table tbody');
        const ligneCommandeTemplate = document.querySelector('.ligne-commande-template');

        let currentLigneIndex = 0;

        // Ajoute une ligne de produit à la table
        function addProductRow(product, quantity = 1, prixUnitaire = '') {
            // Vérifie si le produit est déjà dans la liste
            let existingRowInput = Array.from(lignesCommandeTableBody.querySelectorAll('.produit-id-input'))
                .find(input => input.value == product.id);

            if (existingRowInput) {
                // Si le produit existe, incrémente la quantité
                const qtyInput = existingRowInput.closest('tr').querySelector('.quantite-input');
                qtyInput.value = parseInt(qtyInput.value) + 1;
                return;
            }

            // Crée une nouvelle ligne à partir du template
            const newRow = ligneCommandeTemplate.cloneNode(true);
            newRow.style.display = ''; // Affiche la ligne
            newRow.classList.remove('ligne-commande-template');

            // Assigne les valeurs du produit
            const productIdInput = newRow.querySelector('.produit-id-input');
            productIdInput.value = product.id;
            newRow.querySelector('.product-name-display').textContent = product.nom;

            const quantiteInput = newRow.querySelector('.quantite-input');
            quantiteInput.value = quantity;
            quantiteInput.setAttribute('required', 'required');

            const prixUnitaireInput = newRow.querySelector('.prix-unitaire-input');
            // Utilise le prix unitaire par défaut si fourni, sinon le prix de vente par défaut du produit
            prixUnitaireInput.value = prixUnitaire !== '' ? prixUnitaire : (product.prix_vente || '');
            prixUnitaireInput.setAttribute('required', 'required');

            // Ajoute l'écouteur d'événement pour la suppression de la ligne
            newRow.querySelector('.remove-ligne').addEventListener('click', function() {
                newRow.remove();
                updateRowIndexes();
            });

            lignesCommandeTableBody.appendChild(newRow);
            updateRowIndexes();
        }

        // Met à jour les index des inputs pour le formulaire
        function updateRowIndexes() {
            currentLigneIndex = 0;
            lignesCommandeTableBody.querySelectorAll('tr:not(.ligne-commande-template)').forEach((row) => {
                row.querySelector('.produit-id-input').setAttribute('name', `lignes[${currentLigneIndex}][produit_id]`);
                row.querySelector('.quantite-input').setAttribute('name', `lignes[${currentLigneIndex}][quantite]`);
                row.querySelector('.prix-unitaire-input').setAttribute('name', `lignes[${currentLigneIndex}][prix_unitaire]`);

                currentLigneIndex++;
            });
        }

        // Écoute l'événement Livewire lorsque qu'un produit est sélectionné
        window.addEventListener('productSelectedForCommande', event => {
            const product = event.detail.product;
            addProductRow(product, 1, product.prix_vente);
        });

        // --- Gestion de la soumission du modal Produit via AJAX ---
        document.getElementById('modalProduit').addEventListener('show.bs.modal', function (event) {
            formProduit.reset();
            // Nettoyer les messages d'erreur et les classes 'is-invalid'
            const errorAlert = document.getElementById('product-modal-error-alert');
            errorAlert.classList.add('d-none');
            errorAlert.innerHTML = '';
            formProduit.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            formProduit.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        });

        formProduit.addEventListener('submit', function(e) {
            e.preventDefault();

            const errorAlert = document.getElementById('product-modal-error-alert');
            errorAlert.classList.add('d-none');
            errorAlert.innerHTML = '';
            formProduit.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            formProduit.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

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
                    modalProduit.hide();
                    // Dispatch l'événement pour ajouter le produit à la commande via Livewire
                    Livewire.dispatch('productSelectedForCommande', { product: data.produit });
                    formProduit.reset();
                } else {
                    errorAlert.innerHTML = data.message || 'Une erreur est survenue.';
                    errorAlert.classList.remove('d-none');
                }
            })
            .catch(err => {
                let errorMessage = 'Une erreur est survenue lors de la création du produit.';
                if (err.errors) {
                    // Si des erreurs de validation sont présentes, les afficher pour chaque champ
                    for (const field in err.errors) {
                        const input = formProduit.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.classList.add('invalid-feedback');
                            errorDiv.innerHTML = err.errors[field].join('<br>');
                            input.parentNode.appendChild(errorDiv);
                        }
                    }
                    errorMessage = 'Veuillez vérifier les champs du formulaire.';
                } else if (err.message) {
                    errorMessage = err.message;
                }
                errorAlert.innerHTML = errorMessage;
                errorAlert.classList.remove('d-none');
            });
        });

        // --- Gestion de la soumission du modal Fournisseur via AJAX ---
        const formFournisseur = document.getElementById('form-fournisseur-modal');
        const modalFournisseur = new bootstrap.Modal(document.getElementById('modalFournisseur'));
        const selectFournisseur = document.getElementById('selectFournisseur');
        const fournisseurErrorDiv = document.getElementById('fournisseur-error');
        const fournisseurSuccessDiv = document.getElementById('fournisseur-success');

        formFournisseur.addEventListener('submit', function(e) {
            e.preventDefault();

            fournisseurErrorDiv.classList.add('d-none');
            fournisseurSuccessDiv.classList.add('d-none');
            fournisseurErrorDiv.innerHTML = '';
            fournisseurSuccessDiv.innerHTML = '';

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
                if (data.success && data.fournisseur) {
                    modalFournisseur.hide();
                    // Ajoute le nouveau fournisseur au select
                    const option = new Option(data.fournisseur.nom, data.fournisseur.id, true, true);
                    selectFournisseur.appendChild(option);
                    
                    // Affiche un message de succès
                    fournisseurSuccessDiv.innerHTML = 'Fournisseur ajouté avec succès!';
                    fournisseurSuccessDiv.classList.remove('d-none');
                    formFournisseur.reset();
                } else {
                    fournisseurErrorDiv.innerHTML = data.message || 'Impossible d\'ajouter le fournisseur.';
                    fournisseurErrorDiv.classList.remove('d-none');
                }
            })
            .catch(err => {
                let errorMessage = 'Une erreur est survenue lors de l\'ajout du fournisseur.';
                if (err.errors) {
                    errorMessage = Object.values(err.errors).map(arr => arr.join('<br>')).join('<br>');
                } else if (err.message) {
                    errorMessage = err.message;
                }
                fournisseurErrorDiv.innerHTML = errorMessage;
                fournisseurErrorDiv.classList.remove('d-none');
            });
        });

    });
</script>
@endsection