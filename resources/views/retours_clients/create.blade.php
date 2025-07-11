@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid">
    <h1>Enregistrer un nouveau retour client</h1>

    {{-- Affichage des erreurs de validation ou de session --}}
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

    {{-- Bouton Nouveau Client --}}
    <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalClient">
        + Nouveau client
    </button>

    <form action="{{ route('retours_clients.store') }}" method="POST" id="retourClientForm">
        @csrf

        {{-- Section liée à la vente existante (si applicable) --}}
        @if($vente)
            <div class="alert alert-info">
                Ce retour est lié à la vente #{{ $vente->id }} du {{ $vente->date_vente->format('d/m/Y H:i') }} pour le client {{ $vente->client->nom ?? 'N/A' }}.
                <input type="hidden" name="vente_id" value="{{ $vente->id }}">
            </div>
        @endif

        {{-- Sélection du client --}}
        <div class="mb-3">
            <label for="client_id" class="form-label">Client *</label>
            <select name="client_id" id="client_id" class="form-control" required>
                <option value="">-- Choisir un client --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id', $vente->client_id ?? '') == $client->id ? 'selected' : '' }}>{{ $client->nom }}</option>
                @endforeach
            </select>
        </div>

        {{-- Date et heure du retour --}}
        <div class="mb-3">
            <label for="date_retour" class="form-label">Date et heure du retour *</label>
            <input type="datetime-local" name="date_retour" id="date_retour" class="form-control" value="{{ old('date_retour', now()->format('Y-m-d\TH:i')) }}" required>
        </div>

        {{-- Motif global du retour --}}
        <div class="mb-3">
            <label for="motif_global" class="form-label">Motif global du retour</label>
            <textarea name="motif_global" id="motif_global" class="form-control" rows="3">{{ old('motif_global') }}</textarea>
        </div>

        <h4>Produits à retourner</h4>

        {{-- Section pour ajouter des produits depuis la vente (si applicable) --}}
        @if($vente && $lignesVente->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">Produits de la vente #{{ $vente->id }}</div>
                <div class="card-body">
                    <p>Sélectionnez les produits de la vente à retourner :</p>
                    <div class="list-group">
                        @foreach($lignesVente as $ligne)
                            <button type="button" class="list-group-item list-group-item-action add-product-from-sale"
                                data-product-id="{{ $ligne->produit->id }}"
                                data-product-name="{{ $ligne->produit->nom }}"
                                data-product-stock="{{ $ligne->produit->quantite }}"
                                data-product-qty-sold="{{ $ligne->quantite }}"
                                data-product-price-sold="{{ $ligne->prix_unitaire }}"
                                data-lot-id="{{ $ligne->lot_id }}"
                            >
                                {{ $ligne->produit->nom }} (Qté vendue: {{ $ligne->quantite }}) - Prix unitaire vendu: {{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} FCFA
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Composant Livewire pour la recherche de produits généraux --}}
        <div class="mb-4">
            @livewire('retour-client-product-search')
        </div>

        {{-- Tableau des produits à retourner --}}
        <table class="table table-bordered" id="produitsRetourTable">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Stock Actuel</th>
                    <th>Quantité retournée *</th>
                    <th>Prix unitaire retour *</th>
                    <th>Motif spécifique</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- Ligne de modèle cachée pour le clonage par JavaScript --}}
                {{-- **IMPORTANT:** Ajout de l'attribut 'disabled' aux champs du template --}}
                <tr style="display: none;" class="product-retour-row-template">
                    <td>
                        <input type="hidden" class="product-id-input" disabled>
                        <input type="hidden" class="lot-id-input" disabled>
                        <span class="product-name-display"></span>
                    </td>
                    <td>
                        <span class="product-current-stock-display text-info fw-bold"></span>
                    </td>
                    <td>
                        <input type="number" class="form-control quantite-retournee-input" min="1" value="1" required disabled>
                    </td>
                    <td>
                        <input type="number" step="1" class="form-control prix-unitaire-retour-input" min="1" required disabled>
                    </td>
                    <td>
                        <input type="text" class="form-control motif-ligne-input" placeholder="Motif spécifique (optionnel)" disabled>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">Supprimer</button>
                    </td>
                </tr>
                {{-- Lignes de produits pré-remplies par old('lignes') après une erreur de validation --}}
                @if(old('lignes'))
                    @foreach(old('lignes') as $index => $oldLigne)
                        @php
                            $product = \App\Models\Produit::find($oldLigne['produit_id']);
                        @endphp
                        @if($product)
                            <tr class="product-retour-row">
                                <td>
                                    <input type="hidden" name="lignes[{{ $index }}][produit_id]" value="{{ $product->id }}" class="product-id-input">
                                    <input type="hidden" name="lignes[{{ $index }}][lot_id]" value="{{ $oldLigne['lot_id'] ?? '' }}" class="lot-id-input">
                                    <span class="product-name-display">{{ $product->nom }}</span>
                                </td>
                                <td>
                                    <span class="product-current-stock-display text-info fw-bold">{{ $product->quantite }}</span>
                                </td>
                                <td>
                                    <input type="number" name="lignes[{{ $index }}][quantite_retournee]" class="form-control quantite-retournee-input" min="1" value="{{ $oldLigne['quantite_retournee'] }}" required>
                                </td>
                                <td>
                                    <input type="number" step="1" name="lignes[{{ $index }}][prix_unitaire_retour]" class="form-control prix-unitaire-retour-input" min="1" value="{{ $oldLigne['prix_unitaire_retour'] }}" required>
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

        {{-- Montant à rembourser --}}
        <div class="mb-3">
            <label for="montant_rembourse" class="form-label">Montant à rembourser (FCFA)</label>
            <input type="number" step="1" min="1" name="montant_rembourse" id="montant_rembourse" class="form-control" value="{{ old('montant_rembourse', 0) }}">
        </div>

        {{-- Boutons de soumission --}}
        <button type="submit" class="btn btn-primary mt-4">Enregistrer le retour</button>
        <a href="{{ route('retours_clients.index') }}" class="btn btn-secondary mt-4">Annuler</a>
    </form>

    
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsRetourTableBody = document.querySelector('#produitsRetourTable tbody');
        const productRetourRowTemplate = document.querySelector('.product-retour-row-template');
        const retourClientForm = document.getElementById('retourClientForm');

        /**
         * Ajoute une nouvelle ligne de produit au tableau de retour.
         */
        function addProductRetourRow(product, quantiteRetournee = 1, prixUnitaireRetour = '', motifLigne = '', lotId = null) {
            
            // Vérifier si le produit est déjà dans la liste (sauf si c'est un lot spécifique)
            if (!lotId) { 
                let existingRowInput = Array.from(produitsRetourTableBody.querySelectorAll('.product-id-input'))
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

            // --- Activation des inputs et assignation des valeurs ---
            
            // Retirer l'attribut 'disabled' des inputs clonés pour qu'ils soient soumis et validables.
            newRow.querySelectorAll('input, select, textarea').forEach(input => {
                input.removeAttribute('disabled');
            });

            newRow.querySelector('.product-id-input').value = product.id;
            newRow.querySelector('.product-name-display').textContent = product.nom;
            newRow.querySelector('.product-current-stock-display').textContent = product.quantite;

            if (lotId) {
                newRow.querySelector('.lot-id-input').value = lotId;
            }
            
            newRow.querySelector('.quantite-retournee-input').value = quantiteRetournee;
            newRow.querySelector('.prix-unitaire-retour-input').value = prixUnitaireRetour;
            newRow.querySelector('.motif-ligne-input').value = motifLigne;

            // Ajouter l'écouteur pour la suppression de ligne
            newRow.querySelector('.removeRow').addEventListener('click', function() {
                newRow.remove();
                updateRowIndexes();
            });

            produitsRetourTableBody.appendChild(newRow);
            updateRowIndexes();
        }

        /**
         * Met à jour les index des noms d'inputs dans le tableau pour assurer l'envoi correct des données.
         * Cible spécifiquement les lignes qui ne sont pas le template.
         */
        function updateRowIndexes() {
            let index = 0; 
            produitsRetourTableBody.querySelectorAll('tr.product-retour-row').forEach((row) => {
                // S'assurer que le nom des champs commence par 'lignes[index]'
                row.querySelector('.product-id-input').setAttribute('name', `lignes[${index}][produit_id]`);
                
                const lotIdInput = row.querySelector('.lot-id-input');
                if (lotIdInput) {
                    lotIdInput.setAttribute('name', `lignes[${index}][lot_id]`);
                }
                
                row.querySelector('.quantite-retournee-input').setAttribute('name', `lignes[${index}][quantite_retournee]`);
                row.querySelector('.prix-unitaire-retour-input').setAttribute('name', `lignes[${index}][prix_unitaire_retour]`);
                row.querySelector('.motif-ligne-input').setAttribute('name', `lignes[${index}][motif_ligne]`);
                index++;
            });
        }

        // --- Intégration Livewire & Gestion des ajouts de produits ---

        // 1. Écouteur pour les produits ajoutés depuis la section "Produits de la vente"
        document.querySelectorAll('.add-product-from-sale').forEach(button => {
            button.addEventListener('click', function() {
                const productData = {
                    id: this.dataset.productId,
                    nom: this.dataset.productName,
                    quantite: this.dataset.productStock
                };
                const qtySold = this.dataset.qtySold;
                const priceSold = this.dataset.priceSold;
                const lotId = this.dataset.lotId;

                addProductRetourRow(productData, qtySold, priceSold, 'Retour de vente', lotId);
            });
        });

        // 2. Écouteur pour les produits sélectionnés via le composant Livewire (recherche générale)
        // Utilise l'événement 'productSelectedForRetourClient' déclenché par le composant Livewire
        window.addEventListener('productSelectedForRetourClient', event => {
            const product = event.detail.product;
            // Ajoute le produit avec la quantité 1 et le prix de vente par défaut
            addProductRetourRow(product, 1, product.prix_vente); 
        });

        // --- Gestion des Modals (Produit, Catégorie, Client) via AJAX ---

        // Modal Produit
        const formProduit = document.getElementById('form-produit-modal');
        const modalProduit = new bootstrap.Modal(document.getElementById('modalProduit'));

        formProduit.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formProduit);

            fetch(formProduit.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 201 || (body.success && body.produit)) {
                    modalProduit.hide();
                    // Dispatch l'événement Livewire pour ajouter le produit au tableau de retour
                    // Note: 'productSelectedForRetourClient' est écouté plus haut.
                    Livewire.dispatch('productSelectedForRetourClient', { product: body.produit });
                    formProduit.reset();
                    // Nettoyer les erreurs visuelles du formulaire modal
                    formProduit.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    formProduit.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                } else {
                    // Gérer les erreurs de validation
                    alert(body.message || 'Erreur lors de la création du produit.');
                    // Afficher les erreurs de validation
                    formProduit.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    formProduit.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                    
                    if (body.errors) {
                        for (const field in body.errors) {
                            const input = formProduit.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const errorDiv = document.createElement('div');
                                errorDiv.classList.add('invalid-feedback');
                                errorDiv.innerHTML = body.errors[field].join('<br>');
                                input.parentNode.appendChild(errorDiv);
                            }
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur de communication est survenue.');
            });
        });

        // Modal Catégorie (AJAX)
        const formCategorie = document.querySelector('#modalCategorie form');
        const modalCategorie = new bootstrap.Modal(document.getElementById('modalCategorie'));
        const selectCategorie = document.getElementById('selectCategorie');

        formCategorie.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(formCategorie);

            fetch(formCategorie.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.categorie) {
                    modalCategorie.hide();
                    const option = new Option(data.categorie.nom, data.categorie.id, true, true);
                    selectCategorie.appendChild(option);
                    formCategorie.reset();
                } else {
                    alert(data.message || 'Impossible d\'ajouter la catégorie.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de l\'ajout de la catégorie.');
            });
        });

        // Modal Client (AJAX)
        const formClient = document.getElementById('form-client-modal');
        const modalClient = new bootstrap.Modal(document.getElementById('modalClient'));
        const selectClient = document.getElementById('client_id');
        const clientErrorDiv = document.getElementById('client-error');
        const clientSuccessDiv = document.getElementById('client-success');

        formClient.addEventListener('submit', function(e) {
            e.preventDefault();

            clientErrorDiv.classList.add('d-none');
            clientSuccessDiv.classList.add('d-none');
            
            const formData = new FormData(formClient);

            fetch(formClient.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.client) {
                    modalClient.hide();
                    // Ajoute le nouveau client à la liste déroulante et le sélectionne
                    const option = new Option(data.client.nom, data.client.id, true, true);
                    selectClient.appendChild(option);
                    clientSuccessDiv.innerHTML = 'Client ajouté avec succès!';
                    clientSuccessDiv.classList.remove('d-none');
                    formClient.reset();
                } else {
                    clientErrorDiv.innerHTML = data.message || 'Impossible d\'ajouter le client.';
                    clientErrorDiv.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                clientErrorDiv.innerHTML = 'Une erreur est survenue lors de l\'ajout du client.';
                clientErrorDiv.classList.remove('d-none');
            });
        });

        // Appel initial pour s'assurer que les lignes existantes (issues de old('lignes')) ont des index corrects au chargement de la page.
        updateRowIndexes();
    });
</script>
@endsection