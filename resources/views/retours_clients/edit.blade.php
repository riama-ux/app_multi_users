@extends('pages.admin.shared.layout')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Modifier le retour client #{{ $retour->id }}</h3>
                            <div class="nk-block-desc">
                                <p>Mettez à jour les détails du retour client.</p>
                            </div>
                        </div><div class="nk-block-head-content">
                            <a href="{{ route('retours_clients.index') }}" class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                                <em class="icon ni ni-arrow-left"></em><span>Retour à la liste</span>
                            </a>
                        </div></div></div><div class="nk-block">
                    <div class="card card-bordered card-preview">
                        <div class="card-inner">
                            <form action="{{ route('retours_clients.update', $retour->id) }}" method="POST" id="retourClientEditForm">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-12">
                                        <h4>Informations du retour</h4>
                                        <hr class="preview-hr mt-0 mb-3">
                                    </div>

                                    @if($retour->vente)
                                        <div class="col-12">
                                            <div class="alert alert-info alert-icon">
                                                <em class="icon ni ni-info-fill"></em>
                                                Ce retour est lié à la vente #{{ $retour->vente->id }} du {{ $retour->vente->date_vente->format('d/m/Y H:i') }} pour le client {{ $retour->vente->client->nom ?? 'N/A' }}.
                                                <input type="hidden" name="vente_id" value="{{ $retour->vente->id }}">
                                            </div>
                                        </div>
                                    @else
                                        <input type="hidden" name="vente_id" value="{{ old('vente_id', $retour->vente_id) }}">
                                    @endif

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="client_id">Client *</label>
                                            <div class="form-control-wrap">
                                                <div class="input-group">
                                                    <select name="client_id" id="client_id" class="form-control form-select" data-search="on" required>
                                                        <option value="">-- Choisir un client --</option>
                                                        @foreach($clients as $client)
                                                            <option value="{{ $client->id }}" {{ old('client_id', $retour->client_id) == $client->id ? 'selected' : '' }}>{{ $client->nom }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalClient">
                                                        <em class="icon ni ni-plus"></em>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="date_retour">Date et heure du retour *</label>
                                            <div class="form-control-wrap">
                                                <input type="datetime-local" name="date_retour" id="date_retour" class="form-control" value="{{ old('date_retour', $retour->date_retour->format('Y-m-d\TH:i')) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label" for="motif_global">Motif global du retour</label>
                                            <div class="form-control-wrap">
                                                <textarea name="motif_global" id="motif_global" class="form-control" rows="3">{{ old('motif_global', $retour->motif_global) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4">
                                    <div class="col-12">
                                        <h4>Produits à retourner</h4>
                                        <hr class="preview-hr mt-0 mb-3">
                                    </div>

                                    @if($retour->vente && $lignesVente->isNotEmpty())
                                        <div class="col-12">
                                            <div class="card bg-light">
                                                <div class="card-inner">
                                                    <h5>Produits de la vente #{{ $retour->vente->id }}</h5>
                                                    <p>Sélectionnez les produits de la vente à retourner :</p>
                                                    <div class="list-group list-group-flush border-bottom">
                                                        @foreach($lignesVente as $ligne)
                                                            <button type="button" class="list-group-item list-group-item-action add-product-from-sale py-2"
                                                                data-product-id="{{ $ligne->produit->id }}"
                                                                data-product-name="{{ $ligne->produit->nom }}"
                                                                data-product-stock="{{ $ligne->produit->quantite }}"
                                                                data-product-qty-sold="{{ $ligne->quantite }}"
                                                                data-product-price-sold="{{ $ligne->prix_unitaire }}"
                                                                data-lot-id="{{ $ligne->lot_id }}"
                                                            >
                                                                <em class="icon ni ni-plus-circle me-2"></em>
                                                                {{ $ligne->produit->nom }} (Qté vendue: {{ $ligne->quantite }}) - Prix unitaire vendu: {{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} FCFA
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-12 mt-4">
                                        <div class="mb-4">
                                            @livewire('retour-client-product-search')
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-striped" id="produitsRetourTable">
                                                <thead>
                                                    <tr>
                                                        <th>Produit</th>
                                                        <th>Stock Actuel</th>
                                                        <th>Quantité retournée *</th>
                                                        <th>Prix unitaire retour *</th>
                                                        <th>Motif spécifique</th>
                                                        <th style="width: 100px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr style="display: none;" class="product-retour-row-template">
                                                        <td>
                                                            <input type="hidden" class="product-id-input">
                                                            <input type="hidden" class="ligne-retour-id-input"> {{-- Pour les lignes existantes --}}
                                                            <input type="hidden" class="lot-id-input"> {{-- Pour le lot d'origine --}}
                                                            <span class="product-name-display"></span>
                                                        </td>
                                                        <td>
                                                            <span class="product-current-stock-display text-info fw-bold"></span>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="lignes[0][quantite_retournee]" class="form-control quantite-retournee-input" min="0.01" value="1" required dislable>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01"  name="lignes[0][prix_unitaire_retour]"  class="form-control prix-unitaire-retour-input" min="0" required dislable>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control motif-ligne-input" placeholder="Motif spécifique (optionnel)">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-icon btn-danger removeRow"><em class="icon ni ni-trash-fill"></em></button>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="montant_rembourse">Montant à rembourser (FCFA)</label>
                                            <div class="form-control-wrap">
                                                <input type="number" step="0.01" min="0" name="montant_rembourse" id="montant_rembourse" class="form-control" value="{{ old('montant_rembourse', $retour->montant_rembourse) }}">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 mt-4">
                                        <ul class="d-flex justify-content-end gx-3">
                                            <li><button type="submit" class="btn btn-primary">Mettre à jour le retour</button></li>
                                            <li><a href="{{ route('retours_clients.index') }}" class="btn btn-light">Annuler</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div></div></div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsRetourTableBody = document.querySelector('#produitsRetourTable tbody');
        const productRetourRowTemplate = document.querySelector('.product-retour-row-template');
        const retourClientEditForm = document.getElementById('retourClientEditForm'); 

        let currentLigneIndex = {{ old('lignes') ? count(old('lignes')) : ($retour->lignesRetour->count() ?? 0) }};

        // Function to add a product return row
        function addProductRetourRow(product, quantiteRetournee = 1, prixUnitaireRetour = '', motifLigne = '', lotId = null, ligneId = null) {
            
            // Check for duplicates only if it's a new line being added (not pre-filling existing lines)
            if (ligneId === null) { 
                let existingRowInput = Array.from(produitsRetourTableBody.querySelectorAll('.product-retour-row .product-id-input'))
                                             .find(input => input.value == product.id);

                if (existingRowInput) {
                    alert('Ce produit est déjà dans la liste de retour.');
                    return;
                }
            }

            const newRow = productRetourRowTemplate.cloneNode(true);
            newRow.style.display = '';
            newRow.classList.remove('product-retour-row-template');
            newRow.classList.add('product-retour-row'); 

            newRow.querySelector('.product-id-input').value = product.id;
            newRow.querySelector('.product-name-display').textContent = product.nom;
            newRow.querySelector('.product-current-stock-display').textContent = product.quantite;

            if (lotId) {
                newRow.querySelector('.lot-id-input').value = lotId;
            }
            if (ligneId) {
                // Ensure existing lines retain their ID for update purposes
                const ligneRetourIdInput = newRow.querySelector('.ligne-retour-id-input');
                if (ligneRetourIdInput) {
                    ligneRetourIdInput.value = ligneId;
                } else {
                    // Fallback in case template structure is missing the hidden input
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.classList.add('ligne-retour-id-input');
                    input.value = ligneId;
                    newRow.querySelector('td').prepend(input);
                }
            }

            const quantiteRetourneeInput = newRow.querySelector('.quantite-retournee-input');
            quantiteRetourneeInput.value = quantiteRetournee;

            const prixUnitaireRetourInput = newRow.querySelector('.prix-unitaire-retour-input');
            prixUnitaireRetourInput.value = prixUnitaireRetour;

            const motifLigneInput = newRow.querySelector('.motif-ligne-input');
            motifLigneInput.value = motifLigne;

            newRow.querySelector('.removeRow').addEventListener('click', function() {
                newRow.remove();
                updateRowIndexes();
            });

            produitsRetourTableBody.appendChild(newRow);
            updateRowIndexes();
        }

        // Pre-fill existing return lines
        const existingLignesRetour = @json($retour->lignesRetour ?? []);
        existingLignesRetour.forEach(ligne => {
            if (ligne.produit) {
                const productData = {
                    id: ligne.produit.id,
                    nom: ligne.produit.nom,
                    quantite: ligne.produit.quantite // Current stock
                };
                addProductRetourRow(productData, ligne.quantite_retournee, ligne.prix_unitaire_retour, ligne.motif_ligne, ligne.lot_id, ligne.id);
            } else {
                console.warn('Produit is null or undefined for return line:', ligne);
            }
        });

        // Event listener for "Add product from sale" buttons
        document.querySelectorAll('.add-product-from-sale').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const productName = this.dataset.productName;
                const productStock = this.dataset.productStock;
                const qtySold = this.dataset.qtySold;
                const priceSold = this.dataset.priceSold;
                const lotId = this.dataset.lotId;

                const productData = {
                    id: productId,
                    nom: productName,
                    quantite: productStock 
                };
                addProductRetourRow(productData, qtySold, priceSold, 'Retour de vente', lotId);
            });
        });

        // Function to reindex input names
        function updateRowIndexes() {
            let index = 0; 
            produitsRetourTableBody.querySelectorAll('tr.product-retour-row').forEach((row) => {
                row.querySelector('.product-id-input').setAttribute('name', `lignes[${index}][produit_id]`);
                
                const ligneRetourIdInput = row.querySelector('.ligne-retour-id-input');
                if (ligneRetourIdInput) {
                    ligneRetourIdInput.setAttribute('name', `lignes[${index}][id]`);
                }
                
                const lotIdInput = row.querySelector('.lot-id-input');
                if (lotIdInput) {
                    lotIdInput.setAttribute('name', `lignes[${index}][lot_id]`);
                }
                
                row.querySelector('.quantite-retournee-input').setAttribute('name', `lignes[${index}][quantite_retournee]`);
                row.querySelector('.prix-unitaire-retour-input').setAttribute('name', `lignes[${index}][prix_unitaire_retour]`);
                row.querySelector('.motif-ligne-input').setAttribute('name', `lignes[${index}][motif_ligne]`);
                index++;
            });
            currentLigneIndex = index;
        }

        // Livewire event listener to add a product from the search component
        window.addEventListener('productSelectedForRetourClient', event => {
            const product = event.detail.product;
            addProductRetourRow(product, 1, product.prix_vente);
        });

        // Ensure inputs are correctly indexed before form submission
        retourClientEditForm.addEventListener('submit', function(e) {
            updateRowIndexes();
        });

        // =====================================================================
        // Modal Product, Category, and Client management
        // =====================================================================

        // Modal Produit (Product Modal)
        const formProduit = document.getElementById('form-produit-modal');
        const modalProduit = new bootstrap.Modal(document.getElementById('modalProduit'));
        const productErrorAlert = document.getElementById('product-error-alert');

        document.getElementById('modalProduit').addEventListener('show.bs.modal', function (event) {
            formProduit.reset();
            productErrorAlert.classList.add('d-none');
            // Remove validation errors
            formProduit.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            formProduit.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        });

        formProduit.addEventListener('submit', function(e) {
            e.preventDefault();
            productErrorAlert.classList.add('d-none');
            productErrorAlert.innerHTML = '';
            
            // Clear previous validation styles
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
                    // Dispatch event to Livewire to add the new product to the list
                    Livewire.dispatch('productSelectedForRetourClient', { product: data.produit });
                    formProduit.reset();
                } else {
                    let errorMessage = data.message || 'An unknown error occurred.';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).map(arr => arr.join('<br>')).join('<br>');
                    }
                    productErrorAlert.innerHTML = errorMessage;
                    productErrorAlert.classList.remove('d-none');
                }
            })
            .catch(err => {
                let errorMessage = 'Une erreur est survenue lors de la création du produit.';
                if (err.errors) {
                    // Handle validation errors from Laravel
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
                    errorMessage = Object.values(err.errors).map(arr => arr.join('<br>')).join('<br>');
                } else if (err.message) {
                    errorMessage = err.message;
                }
                productErrorAlert.innerHTML = errorMessage;
                productErrorAlert.classList.remove('d-none');
            });
        });

        // Modal Catégorie (Category Modal)
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
                } else {
                    alert(data.message || 'Impossible d\'ajouter la catégorie.');
                }
            })
            .catch(err => {
                alert(err.message || 'Une erreur est survenue lors de l\'ajout de la catégorie.');
            });
        });

        // Modal Client (Client Modal)
        const formClient = document.getElementById('form-client-modal');
        const modalClient = new bootstrap.Modal(document.getElementById('modalClient'));
        const selectClient = document.getElementById('client_id');
        const clientErrorDiv = document.getElementById('client-error');
        const clientSuccessDiv = document.getElementById('client-success');

        formClient.addEventListener('submit', function(e) {
            e.preventDefault();

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
                    const option = new Option(data.client.nom, data.client.id);
                    selectClient.appendChild(option);
                    selectClient.value = data.client.id; // Select the new client
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
    });
</script>
@endsection