@extends('pages.admin.shared.layout')

@section('content')
<h1>Modifier la vente #{{ $vente->id }}</h1>

@if ($errors->any())
<div class="alert alert-danger">
  <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ route('ventes.update', $vente) }}" method="POST" id="venteEditForm">
  @csrf
  @method('PUT')

  <div class="mb-3">
    <label for="client_id" class="form-label">Client</label>
    <select name="client_id" id="client_id" class="form-control" required>
      <option value="">-- Choisir un client --</option>
      @foreach($clients as $client)
        <option value="{{ $client->id }}" {{ (old('client_id', $vente->client_id) == $client->id) ? 'selected' : '' }}>
          {{ $client->nom }}
        </option>
      @endforeach
    </select>
  </div>

  @php
    $oldProduits = old('produits', $vente->ligneVentes->toArray());
  @endphp

  <div id="produitsContainer">
    <h4>Produits</h4>

    @foreach($oldProduits as $index => $ligne)
    <div class="produit-ligne row mb-2">
      <div class="col-md-5">
        <select name="produits[{{ $index }}][produit_id]" class="form-control produit-select" required>
          <option value="">-- Choisir un produit --</option>
          @foreach($produits as $produit)
            <option value="{{ $produit->id }}"
              {{ ($ligne['produit_id'] ?? $ligne['produit_id']) == $produit->id ? 'selected' : '' }}
              data-prix="{{ $produit->prix_vente }}">
              {{ $produit->nom }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <input type="number" name="produits[{{ $index }}][quantite]" class="form-control" min="1"
          value="{{ $ligne['quantite'] ?? 1 }}" required>
      </div>
      <div class="col-md-3">
        <input type="number" step="0.01" name="produits[{{ $index }}][prix_unitaire]" class="form-control prix-unitaire"
          placeholder="Prix unitaire" value="{{ $ligne['prix_unitaire'] ?? '' }}" required>
      </div>
      <div class="col-md-2">
        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-ligne">✕</button>
      </div>
    </div>
    @endforeach
  </div>

  <button type="button" id="addProduitBtn" class="btn btn-secondary mb-3">Ajouter un produit</button>

  <div class="mb-3">
    <label for="remise" class="form-label">Remise globale (en FCFA)</label>
    <input type="number" step="0.01" min="0" name="remise" id="remise" class="form-control"
      value="{{ old('remise', $vente->remise) }}">
  </div>

  <div class="mb-3">
    <label for="montant_paye" class="form-label">Montant payé</label>
    <input type="number" step="0.01" min="0" name="montant_paye" id="montant_paye" class="form-control"
      value="{{ old('montant_paye', $vente->montant_paye) }}" required>
  </div>

  <div class="mb-3">
    <label for="mode_paiement" class="form-label">Mode de paiement</label>
    <select name="mode_paiement" id="mode_paiement" class="form-control" required>
      <option value="especes" {{ (old('mode_paiement', $vente->mode_paiement) == 'especes') ? 'selected' : '' }}>Espèces</option>
      <option value="mobile_money" {{ (old('mode_paiement', $vente->mode_paiement) == 'mobile_money') ? 'selected' : '' }}>Mobile Money</option>
      <option value="virement" {{ (old('mode_paiement', $vente->mode_paiement) == 'virement') ? 'selected' : '' }}>Virement</option>
      <option value="cheque" {{ (old('mode_paiement', $vente->mode_paiement) == 'cheque') ? 'selected' : '' }}>Chèque</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Total TTC (estimation)</label>
    <input type="text" class="form-control" id="total_ttc" readonly>
  </div>

  <button type="submit" class="btn btn-primary">Modifier la vente</button>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    let produitIndex = {{ count($oldProduits) }};

    const container = document.querySelector('#produitsContainer');

    // Ajout d'une nouvelle ligne produit
    document.querySelector('#addProduitBtn').addEventListener('click', function () {
      const newLine = container.querySelector('.produit-ligne').cloneNode(true);
      newLine.querySelectorAll('select, input').forEach(input => {
        const name = input.getAttribute('name');
        const newName = name.replace(/\[\d+\]/, `[${produitIndex}]`);
        input.setAttribute('name', newName);
        if (input.tagName === 'SELECT') input.selectedIndex = 0;
        else input.value = input.classList.contains('prix-unitaire') ? '' : 1;
      });
      produitIndex++;
      container.appendChild(newLine);
      calculerTotalTTC();
    });

    // Auto-remplir le prix unitaire à la sélection d’un produit
    container.addEventListener('change', function (e) {
      if (e.target.classList.contains('produit-select')) {
        const option = e.target.selectedOptions[0];
        const prix = option.getAttribute('data-prix') || '';
        const prixInput = e.target.closest('.produit-ligne').querySelector('.prix-unitaire');
        prixInput.value = prix;
        calculerTotalTTC();
      }
    });

    // Suppression de ligne
    container.addEventListener('click', function (e) {
      if (e.target.classList.contains('btn-remove-ligne')) {
        const lignes = container.querySelectorAll('.produit-ligne');
        if (lignes.length > 1) {
          e.target.closest('.produit-ligne').remove();
          calculerTotalTTC();
        } else {
          alert('Au moins un produit est requis.');
        }
      }
    });

    // Calcul TTC
    document.querySelector('#venteEditForm').addEventListener('input', calculerTotalTTC);
    calculerTotalTTC();

    function calculerTotalTTC() {
      let total = 0;
      container.querySelectorAll('.produit-ligne').forEach(row => {
        const quantite = parseFloat(row.querySelector('input[name$="[quantite]"]').value) || 0;
        const prix = parseFloat(row.querySelector('input[name$="[prix_unitaire]"]').value) || 0;
        total += quantite * prix;
      });
      const remise = parseFloat(document.querySelector('#remise').value) || 0;
      const ttc = total - remise;
      document.querySelector('#total_ttc').value = ttc.toFixed(2);
    }
  });
</script>
@endsection
