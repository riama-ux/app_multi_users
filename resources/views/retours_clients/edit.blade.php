@extends('pages.admin.shared.layout')

@section('content')
<div class="container-fluid">
    <h1>Modifier le retour client #{{ $retour->id }}</h1>

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

    <form action="{{ route('retours_clients.update', $retour->id) }}" method="POST" id="retourClientEditForm">
        @csrf
        @method('PUT')

        @if($retour->vente)
            <div class="alert alert-info">
                Ce retour est lié à la vente #{{ $retour->vente->id }} du {{ $retour->vente->date_vente->format('d/m/Y H:i') }} pour le client {{ $retour->vente->client->nom ?? 'N/A' }}.
                <input type="hidden" name="vente_id" value="{{ $retour->vente->id }}">
            </div>
        @else
            <input type="hidden" name="vente_id" value="{{ old('vente_id', $retour->vente_id) }}">
        @endif

        <div class="mb-3">
            <label for="client_id" class="form-label">Client *</label>
            <select name="client_id" id="client_id" class="form-control" required>
                <option value="">-- Choisir un client --</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id', $retour->client_id) == $client->id ? 'selected' : '' }}>{{ $client->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="date_retour" class="form-label">Date et heure du retour *</label>
            <input type="datetime-local" name="date_retour" id="date_retour" class="form-control" value="{{ old('date_retour', $retour->date_retour->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="mb-3">
            <label for="motif_global" class="form-label">Motif global du retour</label>
            <textarea name="motif_global" id="motif_global" class="form-control" rows="3">{{ old('motif_global', $retour->motif_global) }}</textarea>
        </div>

        <h4>Produits à retourner</h4>

        {{-- Section pour ajouter des produits depuis la vente --}}
        @if($retour->vente && $lignesVente->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header">Produits de la vente #{{ $retour->vente->id }}</div>
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
                        <input type="number" class="form-control quantite-retournee-input" min="0.01" value="1" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" class="form-control prix-unitaire-retour-input" min="0" required>
                    </td>
                    <td>
                        <input type="text" class="form-control motif-ligne-input" placeholder="Motif spécifique (optionnel)">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">Supprimer</button>
                    </td>
                </tr>
                {{-- Les lignes de produits existantes seront pré-remplies ici par JavaScript --}}
            </tbody>
        </table>

        <div class="mb-3">
            <label for="montant_rembourse" class="form-label">Montant à rembourser (FCFA)</label>
            <input type="number" step="0.01" min="0" name="montant_rembourse" id="montant_rembourse" class="form-control" value="{{ old('montant_rembourse', $retour->montant_rembourse) }}">
        </div>

        <button type="submit" class="btn btn-primary mt-4">Mettre à jour le retour</button>
        <a href="{{ route('retours_clients.index') }}" class="btn btn-secondary mt-4">Annuler</a>
    </form>

    {{-- Modals existants (Produit, Catégorie, Client) --}}
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
                            <label for="client-nom" class="form-label">Nom *</label>
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
                        <button type="submit" class="btn btn-success">Enregistrer</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsRetourTableBody = document.querySelector('#produitsRetourTable tbody');
        const productRetourRowTemplate = document.querySelector('.product-retour-row-template');
        const retourClientEditForm = document.getElementById('retourClientEditForm'); // Formulaire d'édition

        let currentLigneIndex = {{ old('lignes') ? count(old('lignes')) : ($retour->lignesRetour->count() ?? 0) }};

        // Fonction pour ajouter une ligne de produit au tableau de retour
        function addProductRetourRow(product, quantiteRetournee = 1, prixUnitaireRetour = '', motifLigne = '', lotId = null, ligneId = null) {
            // Vérifier si le produit existe déjà par son ID pour éviter les doublons lors de l'ajout via Livewire
            if (ligneId === null) { // Seulement si ce n'est pas une ligne existante qu'on pré-remplit
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
            newRow.classList.add('product-retour-row'); // Ajouter une classe pour les lignes réelles

            newRow.querySelector('.product-id-input').value = product.id;
            newRow.querySelector('.product-name-display').textContent = product.nom;
            newRow.querySelector('.product-current-stock-display').textContent = product.quantite; // Afficher le stock actuel

            if (lotId) {
                newRow.querySelector('.lot-id-input').value = lotId;
            }
            if (ligneId) {
                const ligneRetourIdInput = document.createElement('input');
                ligneRetourIdInput.type = 'hidden';
                ligneRetourIdInput.classList.add('ligne-retour-id-input');
                ligneRetourIdInput.value = ligneId;
                newRow.querySelector('td').prepend(ligneRetourIdInput);
            }


            const quantiteRetourneeInput = newRow.querySelector('.quantite-retournee-input');
            quantiteRetourneeInput.value = quantiteRetournee;
            quantiteRetourneeInput.setAttribute('required', 'required');

            const prixUnitaireRetourInput = newRow.querySelector('.prix-unitaire-retour-input');
            prixUnitaireRetourInput.value = prixUnitaireRetour;
            prixUnitaireRetourInput.setAttribute('required', 'required');

            const motifLigneInput = newRow.querySelector('.motif-ligne-input');
            motifLigneInput.value = motifLigne;

            newRow.querySelector('.removeRow').addEventListener('click', function() {
                newRow.remove();
                updateRowIndexes();
            });

            produitsRetourTableBody.appendChild(newRow);
            updateRowIndexes();
        }

        // Pré-remplir les lignes de retour existantes
        const existingLignesRetour = @json($retour->lignesRetour ?? []);
        existingLignesRetour.forEach(ligne => {
            if (ligne.produit) {
                const productData = {
                    id: ligne.produit.id,
                    nom: ligne.produit.nom,
                    quantite: ligne.produit.quantite // Stock actuel du produit
                };
                addProductRetourRow(productData, ligne.quantite_retournee, ligne.prix_unitaire_retour, ligne.motif_ligne, ligne.lot_id, ligne.id);
            } else {
                console.warn('Produit est null ou indéfini pour la ligne de retour :', ligne);
            }
        });

        // Écouteur pour les boutons "Ajouter produit de la vente"
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
                    quantite: productStock // Stock actuel
                };
                addProductRetourRow(productData, qtySold, priceSold, 'Retour de vente', lotId);
            });
        });

        // Fonction pour réindexer les noms des inputs après suppression ou ajout
        function updateRowIndexes() {
            let index = 0; // Réinitialiser l'index
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
            currentLigneIndex = index; // Mettre à jour l'index global pour les futures additions
        }

        // Écouteur d'événement Livewire pour ajouter un produit à la table
        window.addEventListener('productSelectedForRetourClient', event => {
            const product = event.detail.product;
            addProductRetourRow(product, 1, product.prix_vente); // Par défaut 1 quantité, prix de vente comme prix de retour
        });

        // =====================================================================
        // CORRECTION ICI : Gestion de la soumission du formulaire
        // =====================================================================
        retourClientEditForm.addEventListener('submit', function(e) {
            // S'assurer que tous les inputs ont les bons noms avant la soumission
            updateRowIndexes();
            console.log('Formulaire de modification de retour client soumis.'); // Pour le débogage
        });


        // Scripts pour le modal de création de produit (inchangés)
        const formProduit = document.getElementById('form-produit-modal');
        const modalProduit = new bootstrap.Modal(document.getElementById('modalProduit'));

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
                    Livewire.dispatch('productSelectedForRetourClient', { product: data.produit });
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
                } else if (err.message) {
                    errorMessage = err.message;
                }
                alert(errorMessage);
            });
        });

        // Script pour le modal de création de catégorie (inchangé)
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

        // Script pour le modal de création de client (inchangé)
        const formClient = document.getElementById('form-client-modal');
        const modalClient = new bootstrap.Modal(document.getElementById('modalClient'));
        const selectClient = document.getElementById('client_id'); // Le sélecteur de client principal
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
    });
</script>
@endsection
