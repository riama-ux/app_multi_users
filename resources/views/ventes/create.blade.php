@extends('pages.admin.shared.layout')

@section('content')
<h1>Nouvelle vente</h1>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
    </ul>
</div>
@endif

<form action="{{ route('ventes.store') }}" method="POST" id="venteForm">
    @csrf

    <div class="mb-3">
        <label for="client_id" class="form-label">Client</label>
        <select name="client_id" id="client_id" class="form-control" required>
            <option value="">-- Choisir un client --</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->nom }}</option>
            @endforeach
        </select>
    </div>

    <h4>Produits</h4>
    <div id="produitsContainer">
        @php
            $oldProduits = old('produits', [['produit_id' => '', 'quantite' => 1, 'prix_unitaire' => '']]);
        @endphp

        @foreach($oldProduits as $index => $oldProduit)
        <div class="produit-ligne row mb-2 align-items-center">
            <div class="col-md-3">
                <select name="produits[{{ $index }}][produit_id]" class="form-control produit-select" required>
                    <option value="">-- Choisir un produit --</option>
                    @foreach($produits as $produit)
                        <option value="{{ $produit->id }}" data-prix="{{ $produit->prix_vente }}"
                            {{ $oldProduit['produit_id'] == $produit->id ? 'selected' : '' }}>
                            {{ $produit->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="produits[{{ $index }}][quantite]" class="form-control quantite" min="1" value="{{ $oldProduit['quantite'] }}" required>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="produits[{{ $index }}][prix_unitaire]" class="form-control prix-unitaire" placeholder="Prix unitaire" value="{{ $oldProduit['prix_unitaire'] }}" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control sous-total" placeholder="Sous-total" readonly>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-remove-ligne">X</button>
            </div>
        </div>
        @endforeach
    </div>

    <button type="button" id="addProduitBtn" class="btn btn-secondary mb-3">Ajouter un produit</button>

    <h4>Résumé</h4>
    <div class="row mb-3">
        <div class="col-md-3">
            <label>Remise (FCFA)</label>
            <input type="number" step="0.01" min="0" name="remise" id="remise" class="form-control" value="{{ old('remise', 0) }}">
        </div>
        <div class="col-md-3">
            <label>Total TTC</label>
            <input type="text" name="total_ttc" id="total_ttc" class="form-control" readonly value="0">
        </div>
        <div class="col-md-3">
            <label>Montant payé</label>
            <input type="number" step="0.01" min="0" name="montant_paye" id="montant_paye" class="form-control" value="{{ old('montant_paye', 0) }}" required>
        </div>
        <div class="col-md-3 mt-3">
            <label>Reste à payer</label>
            <input type="text" id="reste_a_payer" class="form-control" readonly value="0">
        </div>
    </div>

    <div class="mb-3 col-md-3">
        <label for="mode_paiement">Mode de paiement</label>
        <select name="mode_paiement" id="mode_paiement" class="form-control" required>
            <option value="especes" {{ old('mode_paiement') == 'especes' ? 'selected' : '' }}>Espèces</option>
            <option value="mobile_money" {{ old('mode_paiement') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
            <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
            <option value="cheque" {{ old('mode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary mt-4">Enregistrer la vente</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let produitIndex = {{ count($oldProduits) }};

        // Met à jour le prix unitaire à la sélection d'un produit
        document.querySelector('#produitsContainer').addEventListener('change', function(e){
            if(e.target.classList.contains('produit-select')){
                const option = e.target.selectedOptions[0];
                const prix = option?.getAttribute('data-prix') || '';
                const prixInput = e.target.closest('.produit-ligne').querySelector('.prix-unitaire');
                prixInput.value = prix;
                calculerTotaux();
            }
        });

        // Ajout d'une ligne produit
        document.querySelector('#addProduitBtn').addEventListener('click', function(){
            const container = document.querySelector('#produitsContainer');
            const newLine = container.querySelector('.produit-ligne').cloneNode(true);

            // Reset valeurs
            newLine.querySelector('select').value = '';
            newLine.querySelector('input.quantite').value = 1;
            newLine.querySelector('input.prix-unitaire').value = '';

            // Met à jour les noms des inputs avec un index unique
            newLine.querySelectorAll('select, input').forEach(input => {
                let name = input.getAttribute('name');
                if(name){
                    name = name.replace(/\d+/, produitIndex);
                    input.setAttribute('name', name);
                }
            });
            produitIndex++;

            container.appendChild(newLine);
        });

        // Suppression d'une ligne produit
        document.querySelector('#produitsContainer').addEventListener('click', function(e){
            if(e.target.classList.contains('btn-remove-ligne')){
                const lignes = document.querySelectorAll('.produit-ligne');
                if(lignes.length > 1){
                    e.target.closest('.produit-ligne').remove();
                    calculerTotaux();
                } else {
                    alert('Au moins un produit est requis.');
                }
            }
        });

        // Calcul automatique à chaque modification quantité/prix/remise/montant payé
        document.querySelector('#produitsContainer').addEventListener('input', function(e){
            if(e.target.classList.contains('quantite') || e.target.classList.contains('prix-unitaire')){
                calculerTotaux();
            }
        });
        document.querySelector('#remise').addEventListener('input', calculerTotaux);
        document.querySelector('#montant_paye').addEventListener('input', calculerTotaux);

        // Fonction de calcul
        function calculerTotaux(){
            let totalLignes = 0;

            document.querySelectorAll('.produit-ligne').forEach(ligne => {
                const qte = parseFloat(ligne.querySelector('.quantite').value) || 0;
                const prix = parseFloat(ligne.querySelector('.prix-unitaire').value) || 0;
                const sousTotal = qte * prix;

                // Affichage du sous-total par ligne
                ligne.querySelector('.sous-total').value = sousTotal.toFixed(2);

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
    });
</script>
@endsection



