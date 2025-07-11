@extends('pages.admin.shared.layout')

@section('content')
    <h1>Modifier la commande #{{ $commande->id }}</h1>

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

    <form action="{{ route('commandes.update', $commande->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="fournisseur_id" class="form-label">Fournisseur *</label>
            <select name="fournisseur_id" id="selectFournisseur" class="form-select" required>
                <option value="">-- Choisir un fournisseur --</option>
                @foreach ($fournisseurs as $fournisseur)
                    <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $commande->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>
                        {{ $fournisseur->nom }}
                    </option>
                @endforeach
            </select>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFournisseur">+</button>
        </div>

        <div class="mb-3">
            <label for="date_commande" class="form-label">Date et heure de commande *</label>
            <input type="datetime-local" name="date_commande" id="date_commande" class="form-control" value="{{ old('date_commande', $commande->date_commande->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="mb-3">
            <label for="date_prevue_livraison" class="form-label">Date et heure prévue de livraison</label>
            <input type="datetime-local" name="date_prevue_livraison" id="date_prevue_livraison" class="form-control" value="{{ old('date_prevue_livraison', $commande->date_prevue_livraison ? $commande->date_prevue_livraison->format('Y-m-d\TH:i') : '') }}" required>
            @error('date_prevue_livraison')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <h4>Lignes de commande</h4>

        {{-- Intégration du composant Livewire pour la recherche de produits --}}
        <div class="mb-4">
            @livewire('commande-product-search')
        </div>

        <table class="table" id="lignes-commande-table">
            <thead>
                <tr>
                    <th>Produit *</th>
                    {{-- Supprimé: <th>Code</th> --}}
                    {{-- Supprimé: <th>Référence</th> --}}
                    {{-- Supprimé: <th>Description</th> --}}
                    <th>Quantité *</th>
                    <th>Prix unitaire *</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {{-- Ligne de modèle cachée pour le clonage par JavaScript --}}
                <tr style="display: none;" class="ligne-commande-template">
                    <td>
                        <input type="hidden" class="produit-id-input">
                        <input type="hidden" class="ligne-id-input"> {{-- Pour les lignes existantes --}}
                        <span class="product-name-display"></span>
                    </td>
                    {{-- Supprimé: <td><input type="text" class="form-control code-input" readonly></td> --}}
                    {{-- Supprimé: <td><input type="text" class="form-control reference-input" readonly></td> --}}
                    {{-- Supprimé: <td><input type="text" class="form-control description-input" readonly></td> --}}
                    <td><input type="number" class="form-control quantite-input" min="1" value="1"></td>
                    <td><input type="number" step="0.01" class="form-control prix-unitaire-input" min="0"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-ligne">-</button></td>
                </tr>
                {{-- Les lignes de commande existantes seront pré-remplies ici par JavaScript --}}
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('commandes.index') }}" class="btn btn-secondary">Annuler</a>
    </form>

    {{-- Modals existants (Produit, Catégorie, Fournisseur) --}}
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

    {{-- Scripts JavaScript --}}
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
                    margeInput.value = '';
                }
            }

            if (coutAchatInput && prixVenteInput && margeInput) {
                coutAchatInput.addEventListener('input', updateMarge);
                prixVenteInput.addEventListener('input', updateMarge);
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formProduit = document.getElementById('form-produit-modal');
            const modalProduit = new bootstrap.Modal(document.getElementById('modalProduit'));
            const lignesCommandeTableBody = document.querySelector('#lignes-commande-table tbody');
            const ligneCommandeTemplate = document.querySelector('.ligne-commande-template');

            let currentLigneIndex = 0;

            function addProductRow(product, quantity = 1, prixUnitaire = '', ligneId = null) {
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
                // Supprimé: newRow.querySelector('.code-input').value = product.code || '';
                // Supprimé: newRow.querySelector('.reference-input').value = product.reference || '';
                // Supprimé: newRow.querySelector('.description-input').value = product.description || '';

                const quantiteInput = newRow.querySelector('.quantite-input');
                quantiteInput.value = quantity;
                quantiteInput.setAttribute('required', 'required');

                const prixUnitaireInput = newRow.querySelector('.prix-unitaire-input');
                prixUnitaireInput.value = prixUnitaire !== '' ? prixUnitaire : (product.prix_unitaire_defaut || '');
                prixUnitaireInput.setAttribute('required', 'required');

                newRow.querySelector('.remove-ligne').addEventListener('click', function() {
                    newRow.remove();
                    updateRowIndexes();
                });

                lignesCommandeTableBody.appendChild(newRow);
                updateRowIndexes();
            }

            // Pré-remplir les lignes de commande existantes
            const existingLignesCommande = @json($commande->lignesCommande ?? []);
            existingLignesCommande.forEach(ligne => {
                if (ligne.produit) {
                    const productData = {
                        id: ligne.produit.id,
                        nom: ligne.produit.nom,
                        code: ligne.produit.code || '', // Garder ces propriétés dans productData pour la cohérence si d'autres parties du code en ont besoin, même si elles ne sont pas affichées
                        reference: ligne.produit.reference || '',
                        description: ligne.produit.description || '',
                        prix_unitaire_defaut: ligne.produit.cout_achat
                    };
                    addProductRow(productData, ligne.quantite, ligne.prix_unitaire, ligne.id);
                } else {
                    console.warn('Produit est null ou indéfini pour la ligne de commande :', ligne);
                }
            });

            lignesCommandeTableBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-ligne')) {
                    const row = e.target.closest('tr');
                    row.remove();
                    updateRowIndexes();
                }
            });

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

            document.getElementById('modalProduit').addEventListener('show.bs.modal', function (event) {
                formProduit.reset();
                formProduit.querySelectorAll('.alert').forEach(alert => alert.classList.add('d-none'));
                formProduit.querySelectorAll('.text-danger').forEach(error => error.remove());
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
                        modalProduit.hide();
                        Livewire.dispatch('productSelectedForCommande', { product: data.produit });
                        formProduit.reset();
                    } else {
                        let errorMessage = 'Une erreur est survenue.';
                        if (data.errors) {
                            errorMessage = Object.values(data.errors).map(arr => arr.join('<br>')).join('<br>');
                            for (const field in data.errors) {
                                const input = formProduit.querySelector(`[name="${field}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const errorDiv = document.createElement('div');
                                    errorDiv.classList.add('invalid-feedback');
                                    errorDiv.innerHTML = data.errors[field].join('<br>');
                                    input.parentNode.appendChild(errorDiv);
                                }
                            }
                        } else if (data.message) {
                            errorMessage = data.message;
                        }
                        alert(errorMessage);
                    }
                })
                .catch(err => {
                    let errorMessage = 'Une erreur est survenue.';
                    if (err.errors) {
                        errorMessage = Object.values(err.errors).map(arr => arr.join('<br>')).join('<br>');
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
                    } else if (err.message) {
                        errorMessage = err.message;
                    }
                    alert(errorMessage);
                });
            });

            window.addEventListener('productSelectedForCommande', event => {
                const product = event.detail.product;
                addProductRow(product, 1, product.prix_unitaire_defaut);
            });

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
                    if (data.success && data.fournisseur) {
                        modalFournisseur.hide();
                        const option = new Option(data.fournisseur.nom, data.fournisseur.id);
                        selectFournisseur.appendChild(option);
                        selectFournisseur.value = data.fournisseur.id;
                        successDiv.innerHTML = 'Fournisseur ajouté avec succès!';
                        successDiv.classList.remove('d-none');
                        formFournisseur.reset();
                    } else {
                        errorDiv.innerHTML = data.message || 'Impossible d\'ajouter le fournisseur.';
                        errorDiv.classList.remove('d-none');
                    }
                })
                .catch(err => {
                    let errorMessage = 'Une erreur est survenue.';
                    if (err.errors) {
                        errorMessage = Object.values(err.errors).map(arr => arr.join('<br>')).join('<br>');
                    } else if (err.message) {
                        errorMessage = err.message;
                    }
                    errorDiv.innerHTML = errorMessage;
                    errorDiv.classList.remove('d-none');
                });
            });
        });
    </script>
@endsection