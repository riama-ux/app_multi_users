@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid">

    {{-- Page Heading and Alerts --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nouvelle vente</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Form Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Nouvelle vente</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('ventes.store') }}" method="POST" id="venteForm">
                @csrf

                <div class="row mb-4 align-items-end">
                    <div class="col-md-6">
                        <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="client_id" id="client_id" class="form-select" required>
                                <option value="">-- Choisir un client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->nom }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalClient" title="Ajouter un nouveau client">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Products Section (Livewire) --}}
                <h4 class="mb-3 text-gray-800">Produits</h4>
                <div class="mb-4">
                    @livewire('vente-product-search')
                </div>

                {{-- Products Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="produitsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th style="width: 15%;">Quantité</th>
                                <th style="width: 20%;">Prix Unitaire</th>
                                <th style="width: 20%;">Sous-total</th>
                                <th style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Hidden template row for dynamic adding --}}
                            <tr style="display: none;" class="product-row-template">
                                <td>
                                    <input type="hidden" class="product-id-input">
                                    <span class="product-name-display"></span>
                                </td>
                                <td>
                                    <input type="number" class="form-control quantite-input" min="1" value="1">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control prix-unitaire-input" min="0">
                                </td>
                                <td>
                                    <input type="text" class="form-control sous-total-display" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm removeRow" title="Supprimer le produit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            {{-- Product rows will be inserted here dynamically --}}
                        </tbody>
                    </table>
                </div>

                {{-- Summary and Payment Details --}}
                <h4 class="mt-5 mb-3 text-gray-800">Résumé et Paiement</h4>
                <div class="row mb-5">
                    <div class="col-md-3">
                        <label for="remise" class="form-label">Remise (FCFA)</label>
                        <input type="number" step="0.01" min="0" name="remise" id="remise" class="form-control" value="{{ old('remise', 0) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="total_ttc" class="form-label">Total TTC</label>
                        <input type="text" name="total_ttc" id="total_ttc" class="form-control" readonly value="0">
                    </div>
                    <div class="col-md-3">
                        <label for="montant_paye" class="form-label">Montant payé <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="montant_paye" id="montant_paye" class="form-control" value="{{ old('montant_paye', 0) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="reste_a_payer" class="form-label">Reste à payer</label>
                        <input type="text" id="reste_a_payer" class="form-control" readonly value="0">
                    </div>
                </div>

                <div class="mb-4 col-md-4">
                    <label for="mode_paiement" class="form-label">Mode de paiement <span class="text-danger">*</span></label>
                    <select name="mode_paiement" id="mode_paiement" class="form-select" required>
                        <option value="especes" {{ old('mode_paiement') == 'especes' ? 'selected' : '' }}>Espèces</option>
                        <option value="mobile_money" {{ old('mode_paiement') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
                        <option value="cheque" {{ old('mode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save mx-2"></i> Enregistrer la vente
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Modals --}}

{{-- Modal Nouveau Produit --}}
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
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="reference" class="form-label">Référence</label>
                        <input type="text" name="reference" id="reference" class="form-control" value="{{ old('reference') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="marque" class="form-label">Marque <span class="text-danger">*</span></label>
                        <input type="text" name="marque" class="form-control" value="{{ old('marque', $produit->marque ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="unite" class="form-label">Unité <span class="text-danger">*</span></label>
                        <select name="unite" class="form-select" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach(['pièce', 'kg', 'litre', 'mètre', 'paquet'] as $unit)
                                <option value="{{ $unit }}" {{ old('unite', $produit->unite ?? '') == $unit ? 'selected' : '' }}>
                                    {{ ucfirst($unit) }}
                                </option>
                            @endforeach
                        </select>
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
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalCategorie" title="Ajouter une catégorie">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" required>{{ old('description', $produit->description ?? '') }}</textarea>
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
                        <input type="number" step="0.01" name="marge" id="marge" class="form-control" value="{{ old('marge') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="seuil_alerte" class="form-label">Seuil d'alerte (quantité) <span class="text-danger">*</span></label>
                        <input type="number" name="seuil_alerte" id="seuil_alerte" class="form-control" value="{{ old('seuil_alerte') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Créer</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Nouvelle Catégorie --}}
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
                    <div class="mb-3">
                        <label for="category-nom" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="category-nom" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Créer</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Nouveau Client --}}
<div class="modal fade" id="modalClient" tabindex="-1" aria-labelledby="modalClientLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-client-modal" method="POST" action="{{ route('module.clients.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalClientLabel">Ajouter un client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="client-nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="nom" id="client-nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="client-telephone" class="form-label">Téléphone</label>
                        <input type="text" name="telephone" id="client-telephone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="client-email" class="form-label">Email</label>
                        <input type="email" name="email" id="client-email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="client-adresse" class="form-label">Adresse</label>
                        <input type="text" name="adresse" id="client-adresse" class="form-control">
                    </div>
                    <div id="client-error" class="alert alert-danger d-none"></div>
                    <div id="client-success" class="alert alert-success d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Scripts JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsTableBody = document.querySelector('#produitsTable tbody');
        const productRowTemplate = document.querySelector('.product-row-template');

        let currentLigneIndex = 0; // Index for input names

        // Function to add a product row to the table
        function addProductRow(product, quantity = 1) {
            // Check if the product already exists by ID
            let existingRowInput = Array.from(produitsTableBody.querySelectorAll('.product-id-input'))
                                        .find(input => input.value == product.id);

            if (existingRowInput) {
                const qtyInput = existingRowInput.closest('tr').querySelector('.quantite-input');
                qtyInput.value = parseInt(qtyInput.value) + 1;
                calculerTotaux();
                return;
            }

            const newRow = productRowTemplate.cloneNode(true);
            newRow.style.display = '';
            newRow.classList.remove('product-row-template');

            newRow.querySelector('.product-id-input').value = product.id;
            newRow.querySelector('.product-name-display').textContent = product.nom;

            const quantiteInput = newRow.querySelector('.quantite-input');
            quantiteInput.value = quantity;
            quantiteInput.setAttribute('required', 'required');
            quantiteInput.addEventListener('input', calculerTotaux);

            const prixUnitaireInput = newRow.querySelector('.prix-unitaire-input');
            prixUnitaireInput.value = product.prix_vente || '';
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

        // Function to reindex input names after deletion or addition
        function updateRowIndexes() {
            currentLigneIndex = 0;
            produitsTableBody.querySelectorAll('tr:not(.product-row-template)').forEach((row) => {
                row.querySelector('.product-id-input').setAttribute('name', `produits[${currentLigneIndex}][produit_id]`);
                row.querySelector('.quantite-input').setAttribute('name', `produits[${currentLigneIndex}][quantite]`);
                row.querySelector('.prix-unitaire-input').setAttribute('name', `produits[${currentLigneIndex}][prix_unitaire]`);
                currentLigneIndex++;
            });
        }

        // Livewire event listener to add a product to the table
        window.addEventListener('productSelectedForVente', event => {
            const product = event.detail.product;
            addProductRow(product);
        });

        // Automatic calculation on input changes (remise, montant payé)
        document.querySelector('#remise').addEventListener('input', calculerTotaux);
        document.querySelector('#montant_paye').addEventListener('input', calculerTotaux);

        // Calculation of totals
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

        // Initial calculation
        calculerTotaux();

        // --- Client Modal Handling (New) ---
        const formClient = document.getElementById('form-client-modal');
        const modalClient = new bootstrap.Modal(document.getElementById('modalClient'));
        const selectClient = document.getElementById('client_id');
        const clientErrorDiv = document.getElementById('client-error');
        const clientSuccessDiv = document.getElementById('client-success');

        formClient.addEventListener('submit', function(e) {
            e.preventDefault();

            // Clear previous alerts
            clientErrorDiv.classList.add('d-none');
            clientSuccessDiv.classList.add('d-none');
            clientErrorDiv.innerHTML = '';
            clientSuccessDiv.innerHTML = '';

            const formData = new FormData(formClient);

            fetch(formClient.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': formClient.querySelector('input[name="_token"]').value
                },
                body: formData
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
                    // Optionally show success alert on main page or console log
                    console.log('Client ajouté avec succès!');
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

        // --- Product Modal Handling ---
        const formProduit = document.getElementById('form-produit-modal');
        const modalProduit = new bootstrap.Modal(document.getElementById('modalProduit'));

        document.getElementById('modalProduit').addEventListener('show.bs.modal', function (event) {
            formProduit.reset();
            formProduit.querySelectorAll('.alert').forEach(alert => alert.classList.add('d-none'));
            formProduit.querySelectorAll('.invalid-feedback').forEach(error => error.remove());
            formProduit.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
        });

        formProduit.addEventListener('submit', function(e) {
            e.preventDefault();

            // Reset validation states
            formProduit.querySelectorAll('.invalid-feedback').forEach(error => error.remove());
            formProduit.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));

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
                    // Dispatch Livewire event to add the new product to the sale table
                    Livewire.dispatch('productSelectedForVente', { product: data.produit });
                    formProduit.reset();
                } else {
                    alert(data.message || 'Une erreur est survenue.');
                }
            })
            .catch(err => {
                if (err.errors) {
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
                } else {
                    alert(err.message || 'Une erreur est survenue.');
                }
            });
        });

        // --- Category Modal Handling ---
        const formCategorie = document.querySelector('#modalCategorie form');
        const modalCategorie = new bootstrap.Modal(document.getElementById('modalCategorie'));
        const selectCategorie = document.getElementById('selectCategorie');

        formCategorie.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(formCategorie);

            fetch(formCategorie.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': formCategorie.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) return response.json().then(data => Promise.reject(data));
                return response.json();
            })
            .then(data => {
                if (data.success && data.categorie) {
                    modalCategorie.hide();
                    const option = new Option(data.categorie.nom, data.categorie.id);
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