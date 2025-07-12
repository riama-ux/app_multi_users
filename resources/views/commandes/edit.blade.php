@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-dark mb-0">Modifier la commande #{{ $commande->id }}</h1>
        <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary d-flex align-items-center shadow-sm">
            <i class="bi bi-arrow-left me-2"></i> Retour aux commandes
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Erreurs de validation:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('commandes.update', $commande->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label for="fournisseur_id" class="form-label">Fournisseur <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="fournisseur_id" id="selectFournisseur" class="form-select @error('fournisseur_id') is-invalid @enderror" required>
                                <option value="">-- Choisir un fournisseur --</option>
                                @foreach ($fournisseurs as $fournisseur)
                                    <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $commande->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>
                                        {{ $fournisseur->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFournisseur" title="Ajouter un fournisseur">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        @error('fournisseur_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="date_commande" class="form-label">Date de commande <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="date_commande" id="date_commande" class="form-control @error('date_commande') is-invalid @enderror" value="{{ old('date_commande', $commande->date_commande->format('Y-m-d\TH:i')) }}" required>
                        @error('date_commande')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="date_prevue_livraison" class="form-label">Date prévue de livraison <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="date_prevue_livraison" id="date_prevue_livraison" class="form-control @error('date_prevue_livraison') is-invalid @enderror" value="{{ old('date_prevue_livraison', $commande->date_prevue_livraison ? $commande->date_prevue_livraison->format('Y-m-d\TH:i') : '') }}" required>
                        @error('date_prevue_livraison')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h4 class="mb-3">Lignes de commande</h4>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0">Ajoutez les produits commandés par le fournisseur.</p>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalProduit">
                        <i class="bi bi-plus-circle me-2"></i> Ajouter un nouveau produit
                    </button>
                </div>

                <div class="mb-4">
                    @livewire('commande-product-search')
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="lignes-commande-table">
                        <thead class="table-light">
                            <tr>
                                <th>Produit <span class="text-danger">*</span></th>
                                <th>Quantité <span class="text-danger">*</span></th>
                                <th>Prix unitaire <span class="text-danger">*</span></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="display: none;" class="ligne-commande-template">
                                <td>
                                    <input type="hidden" class="produit-id-input">
                                    <input type="hidden" class="ligne-id-input">
                                    <span class="product-name-display fw-bold"></span>
                                </td>
                                <td>
                                    <input type="number" class="form-control quantite-input" min="1" value="1">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control prix-unitaire-input" min="0">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-ligne" title="Supprimer la ligne">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                    </table>
                </div>

                <div class="my-5 d-flex justify-content-end gap-3">
                    <button type="submit" class="btn btn-primary px-4 py-4 shadow-sm">
                        <i class="bi bi-save me-2"></i> Mettre à jour la commande
                    </button>
                    <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary px-4 py-4">
                        <i class="bi bi-x-circle me-2"></i> Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProduit" tabindex="-1" aria-labelledby="modalProduitLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="POST" id="form-produit-modal" action="{{ route('produits.store') }}">
            @csrf
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="modalProduitLabel">Créer un nouveau produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4 row g-3">
                    <div class="col-md-3">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="marque" class="form-label">Marque <span class="text-danger">*</span></label>
                        <input type="text" name="marque" id="marque" class="form-control" value="{{ old('marque', $produit->marque ?? '') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="unite" class="form-label">Unité<span class="text-danger">*</span></label>
                        <select name="unite" id="unite" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach(['pièce', 'kg', 'litre', 'mètre', 'paquet'] as $unit)
                                <option value="{{ $unit }}" {{ old('unite', $produit->unite ?? '') == $unit ? 'selected' : '' }}>
                                    {{ ucfirst($unit) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" name="reference" id="reference" class="form-control" value="{{ old('reference') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="categorie_id" id="selectCategorie" class="form-select" required>
                                <option value="" >-- Choisir --</option>
                                @foreach($categories as $categorie)
                                    <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCategorie">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description', $produit->description ?? '') }}</textarea>
                    </div>
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
                        <input type="number" step="0.01" name="marge" id="marge" class="form-control bg-light" value="{{ old('marge') }}" readonly required>
                    </div>
                    <div class="col-md-12">
                        <label for="seuil_alerte" class="form-label">Seuil d'alerte (quantité) <span class="text-danger">*</span></label>
                        <input type="number" name="seuil_alerte" id="seuil_alerte" class="form-control" value="{{ old('seuil_alerte') }}">
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

<div class="modal fade" id="modalCategorie" tabindex="-1" aria-labelledby="modalCategorieLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('module.categories.store') }}">
            @csrf
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="modalCategorieLabel">Nouvelle catégorie</h5>
                    <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="categorie_nom" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="categorie_nom" class="form-control" required>
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

<div class="modal fade" id="modalFournisseur" tabindex="-1" aria-labelledby="modalFournisseurLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="form-fournisseur-modal" method="POST" action="{{ route('module.fournisseurs.store') }}">
            @csrf
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="modalFournisseurLabel">Ajouter un fournisseur</h5>
                    <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body row g-3 p-4">
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
                    <div id="fournisseur-error" class="alert alert-danger d-none"></div>
                    <div id="fournisseur-success" class="alert alert-success d-none"></div>
                </div>
                <div class="modal-footer border-top pt-3 d-flex justify-content-center align-item-center">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Enregistrer</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Script pour le calcul de la marge (dans le modal Produit)
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
                margeInput.value = '';
            }
        }

        if (coutAchatInput && prixVenteInput && margeInput) {
            coutAchatInput.addEventListener('input', updateMarge);
            prixVenteInput.addEventListener('input', updateMarge);
        }
    });

    // Script pour la gestion des lignes de commande et des modals (Produit, Fournisseur)
    document.addEventListener('DOMContentLoaded', function () {
        const formProduit = document.getElementById('form-produit-modal');
        const modalProduit = new bootstrap.Modal(document.getElementById('modalProduit'));
        const lignesCommandeTableBody = document.querySelector('#lignes-commande-table tbody');
        const ligneCommandeTemplate = document.querySelector('.ligne-commande-template');

        let currentLigneIndex = 0;

        /**
         * Ajoute une nouvelle ligne de produit au tableau des commandes.
         * * @param {object} product - Données du produit.
         * @param {number} quantity - Quantité commandée.
         * @param {number} prixUnitaire - Prix unitaire d'achat.
         * @param {number|null} ligneId - ID de la ligne existante (pour la modification).
         */
        function addProductRow(product, quantity = 1, prixUnitaire = '', ligneId = null) {
            // Vérifie si le produit est déjà dans le tableau (pour éviter les doublons lors de l'ajout)
            let existingRowInput = Array.from(lignesCommandeTableBody.querySelectorAll('.produit-id-input'))
                                        .find(input => input.value == product.id);

            if (existingRowInput) {
                const qtyInput = existingRowInput.closest('tr').querySelector('.quantite-input');
                qtyInput.value = parseInt(qtyInput.value) + 1;
                return;
            }

            const newRow = ligneCommandeTemplate.cloneNode(true);
            newRow.style.display = '';
            newRow.classList.remove('ligne-commande-template');

            if (ligneId) {
                newRow.querySelector('.ligne-id-input').value = ligneId;
            }

            const productIdInput = newRow.querySelector('.produit-id-input');
            productIdInput.value = product.id;

            newRow.querySelector('.product-name-display').textContent = product.nom;

            const quantiteInput = newRow.querySelector('.quantite-input');
            quantiteInput.value = quantity;
            quantiteInput.setAttribute('required', 'required');

            const prixUnitaireInput = newRow.querySelector('.prix-unitaire-input');
            // Utilise le prix unitaire fourni ou le coût d'achat par défaut si le produit vient d'être sélectionné
            prixUnitaireInput.value = prixUnitaire !== '' ? prixUnitaire : (product.prix_unitaire_defaut || '');
            prixUnitaireInput.setAttribute('required', 'required');

            // Ajoute l'événement de suppression pour la nouvelle ligne
            newRow.querySelector('.remove-ligne').addEventListener('click', function() {
                newRow.remove();
                updateRowIndexes();
            });

            lignesCommandeTableBody.appendChild(newRow);
            updateRowIndexes();
        }

        /**
         * Met à jour les index des inputs pour les envoyer correctement au contrôleur (lignes[0], lignes[1], etc.)
         */
        function updateRowIndexes() {
            currentLigneIndex = 0;
            lignesCommandeTableBody.querySelectorAll('tr:not(.ligne-commande-template)').forEach((row) => {
                row.querySelector('.produit-id-input').setAttribute('name', `lignes[${currentLigneIndex}][produit_id]`);
                const ligneIdInput = row.querySelector('.ligne-id-input');
                if (ligneIdInput) {
                    ligneIdInput.setAttribute('name', `lignes[${currentLigneIndex}][id]`);
                }
                row.querySelector('.quantite-input').setAttribute('name', `lignes[${currentLigneIndex}][quantite]`);
                row.querySelector('.prix-unitaire-input').setAttribute('name', `lignes[${currentLigneIndex}][prix_unitaire]`);

                currentLigneIndex++;
            });
        }

        // Pré-remplir les lignes de commande existantes lors du chargement de la page
        const existingLignesCommande = @json($commande->lignesCommande ?? []);
        existingLignesCommande.forEach(ligne => {
            if (ligne.produit) {
                const productData = {
                    id: ligne.produit.id,
                    nom: ligne.produit.nom,
                    // Note: Utilisation de `cout_achat` car c'est le coût d'achat par défaut pour une commande fournisseur
                    prix_unitaire_defaut: ligne.produit.cout_achat 
                };
                addProductRow(productData, ligne.quantite, ligne.prix_unitaire, ligne.id);
            } else {
                console.warn('Produit introuvable pour la ligne de commande :', ligne);
            }
        });

        // Événement pour la suppression des lignes (géré par le bouton dans addProductRow)
        lignesCommandeTableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-ligne')) {
                const row = e.target.closest('tr');
                row.remove();
                updateRowIndexes();
            }
        });

        // Soumission du formulaire Produit via AJAX (dans la modale)
        formProduit.addEventListener('submit', function(e) {
            e.preventDefault();

            // Nettoyage des erreurs de validation précédentes
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
                    // Si la réponse n'est pas OK, rejeter avec les données JSON pour attraper les erreurs de validation
                    return response.json().then(data => Promise.reject(data));
                }
                return response.json();
            })
            .then(data => {
                // Succès: masquer la modale et ajouter le produit
                if (data.success && data.produit) {
                    modalProduit.hide();
                    
                    // Utilise Livewire.dispatch pour communiquer avec le composant de recherche si nécessaire
                    // et ajoute la ligne au tableau localement.
                    const productData = {
                        id: data.produit.id,
                        nom: data.produit.nom,
                        prix_unitaire_defaut: data.produit.cout_achat 
                    };
                    addProductRow(productData, 1, data.produit.cout_achat);

                    formProduit.reset();
                } else {
                    alert('Erreur: ' + (data.message || 'Impossible de créer le produit.'));
                }
            })
            .catch(err => {
                // Gestion des erreurs de validation (422 Unprocessable Entity)
                if (err.errors) {
                    for (const field in err.errors) {
                        const input = formProduit.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const errorDiv = document.createElement('div');
                            errorDiv.classList.add('invalid-feedback');
                            errorDiv.innerHTML = err.errors[field].join('<br>');
                            
                            // Cherche le parent correct pour l'erreur (si input-group, l'ajouter au groupe)
                            const inputGroup = input.closest('.input-group');
                            if (inputGroup) {
                                inputGroup.after(errorDiv);
                            } else {
                                input.parentNode.appendChild(errorDiv);
                            }
                        }
                    }
                } else {
                    alert('Une erreur est survenue lors de la création du produit.');
                }
            });
        });

        // Gestion du modal Fournisseur
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
                    // Ajouter le nouveau fournisseur au select et le sélectionner
                    const option = new Option(data.fournisseur.nom, data.fournisseur.id, true, true);
                    selectFournisseur.appendChild(option);
                    
                    fournisseurSuccessDiv.innerHTML = 'Fournisseur ajouté avec succès!';
                    fournisseurSuccessDiv.classList.remove('d-none');
                    formFournisseur.reset();
                } else {
                    fournisseurErrorDiv.innerHTML = data.message || 'Impossible d\'ajouter le fournisseur.';
                    fournisseurErrorDiv.classList.remove('d-none');
                }
            })
            .catch(err => {
                let errorMessage = 'Une erreur est survenue.';
                if (err.errors) {
                    errorMessage = Object.values(err.errors).map(arr => arr.join('<br>')).join('<br>');
                } else if (err.message) {
                    errorMessage = err.message;
                }
                fournisseurErrorDiv.innerHTML = errorMessage;
                fournisseurErrorDiv.classList.remove('d-none');
            });
        });
        
        // Écoute l'événement de Livewire pour ajouter un produit sélectionné à la liste
        window.addEventListener('productSelectedForCommande', event => {
            const product = event.detail.product;
            // Passe le coût d'achat par défaut comme prix unitaire initial pour une commande fournisseur
            addProductRow(product, 1, product.cout_achat); 
        });
    });
</script>
@endsection