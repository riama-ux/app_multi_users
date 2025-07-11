@extends('pages.admin.shared.layout')

@section('content')
<div class="container">
    <h2>Nouveau Transfert</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('transferts.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="magasin_destination_id">Magasin de destination</label>
            <select name="magasin_destination_id" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                @foreach($magasins as $magasin)
                    <option value="{{ $magasin->id }}" {{ old('magasin_destination_id') == $magasin->id ? 'selected' : '' }}>{{ $magasin->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="date_transfert">Date du transfert</label>
            <input type="date" name="date_transfert" class="form-control" value="{{ old('date_transfert', date('Y-m-d')) }}" required>
        </div>

        <hr>
        <h5>Produits à transférer</h5>

        {{-- Intégration du composant Livewire pour la recherche de produits --}}
        <div class="mb-4">
            @livewire('product-search')
        </div>

        <table class="table table-bordered" id="produitsTable">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- Ligne de modèle cachée pour le clonage par JavaScript --}}
                <tr style="display: none;" class="product-row-template">
                    <td>
                        <input type="hidden" name="produits[]" class="product-id-input">
                        <span class="product-name-display"></span>
                        <br><small class="product-ref-code"></small>
                    </td>
                    <td>
                        {{-- **IMPORTANT : Retire 'required' de la ligne de modèle.**
                             Il sera ajouté dynamiquement via JS lorsque la ligne est clonée et affichée. --}}
                        <input type="number"  class="form-control product-qty-input" min="1" step="1">
                        <small class="text-muted">Stock dispo: <span class="product-available-stock">0</span></small>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">Supprimer</button>
                    </td>
                </tr>
                {{-- Les produits ajoutés dynamiquement par Livewire/JS seront insérés ici --}}
            </tbody>
        </table>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Enregistrer le transfert</button>
            <a href="{{ route('transferts.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const produitsTableBody = document.querySelector('#produitsTable tbody');
        // IMPORTANT: Assurez-vous que le template est bien un ELEMENT et non une NodeList
        const productRowTemplate = document.querySelector('.product-row-template');

        // Écouteur pour le bouton "Supprimer" sur les lignes de produits
        produitsTableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow')) {
                const row = e.target.closest('tr');
                // S'assurer qu'il reste au moins une ligne si vous voulez en garder une comme template,
                // sinon on supprime juste la ligne.
                row.remove();
            }
        });

        // Livewire event listener pour ajouter un produit à la table
        // Cet événement sera déclenché depuis le composant Livewire (productSelected)
        window.addEventListener('productSelected', event => {
            const product = event.detail.product; // Le produit sélectionné depuis Livewire
            const availableStock = event.detail.availableStock; // Quantité disponible

            // Vérifier si le produit est déjà dans la table
            let existingRow = Array.from(produitsTableBody.querySelectorAll('.product-id-input'))
                                .find(input => input.value == product.id);

            if (existingRow) {
                // Si le produit existe, incrémenter la quantité par défaut ou alerter
                const qtyInput = existingRow.closest('tr').querySelector('.product-qty-input');
                qtyInput.value = parseInt(qtyInput.value) + 1; // Incrémente la quantité
            } else {
                // Cloner la ligne template et la remplir
                const newRow = productRowTemplate.cloneNode(true);
                newRow.style.display = ''; // Rendre la ligne visible
                newRow.classList.remove('product-row-template'); // Retirer la classe template

                newRow.querySelector('.product-id-input').value = product.id;
                newRow.querySelector('.product-name-display').textContent = product.nom;
                newRow.querySelector('.product-ref-code').textContent = `Référence: ${product.reference} / Code: ${product.code}`;
                newRow.querySelector('.product-available-stock').textContent = availableStock;

                const qtyInput = newRow.querySelector('.product-qty-input');
                qtyInput.max = availableStock; // Définir la quantité maximale basée sur le stock disponible
                qtyInput.value = 1; // Définir la quantité initiale à 1 lors de l'ajout
                // **IMPORTANT : Ajouter l'attribut 'required' ici**
                qtyInput.setAttribute('name', 'quantites[]');
                qtyInput.setAttribute('required', 'required');

                produitsTableBody.appendChild(newRow);
            }
        });
    });
</script>
@endsection

{{-- **TRÈS IMPORTANT :**
Assurez-vous que les directives Livewire Styles et Scripts sont incluses DANS VOTRE LAYOUT PRINCIPAL.
Si votre layout principal est `pages.admin.shared.layout.blade.php`, vérifiez :

Dans `pages/admin/shared/layout.blade.php` :
<head>
    @livewireStyles
</head>
<body>
    @livewireScripts
</body>
--}}