@extends('pages.admin.shared.layout')

@section('content')
    <h1>Créer un produit</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('produits.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="categorie_id" class="form-label">Catégorie *</label>
            <select name="categorie_id" id="categorie_id" class="form-select" required>
                <option value="">-- Choisir une catégorie --</option>
                @foreach ($categories as $categorie)
                    <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                        {{ $categorie->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="nom" class="form-label">Nom *</label>
            <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
        </div>

        <div class="mb-3">
            <label for="reference" class="form-label">Référence</label>
            <input type="text" name="reference" id="reference" class="form-control" value="{{ old('reference') }}">
        </div>

        <div class="form-group mb-3">
            <label for="code">Code (laisser vide pour générer automatiquement)</label>
            <input type="text" name="code" class="form-control" value="{{ old('code') }}">
        </div>

        <div class="form-group mb-3">
            <label for="marque">Marque *</label>
            <input type="text" name="marque" class="form-control" value="{{ old('marque', $produit->marque ?? '') }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="description">Description *</label>
            <textarea name="description" class="form-control" required>{{ old('description', $produit->description ?? '') }}</textarea>
        </div>

        <div class="form-group mb-3">
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

        <div class="mb-3">
            <label for="cout_achat" class="form-label">Coût d'achat par défaut *</label>
            <input type="number" step="0.01" name="cout_achat" id="cout_achat" class="form-control" value="{{ old('cout_achat') }}" required>
        </div>

        <div class="mb-3">
            <label for="prix_vente" class="form-label">Prix de vente par défaut *</label>
            <input type="number" step="0.01" name="prix_vente" id="prix_vente" class="form-control" value="{{ old('prix_vente') }}" required>
        </div>

        <div class="mb-3">
            <label for="marge" class="form-label">Marge (%) *</label>
            <input type="number" step="0.01" name="marge" id="marge" class="form-control" value="{{ old('marge') }}" required>
        </div>

        <div class="mb-3">
            <label for="seuil_alerte" class="form-label">Seuil d'alerte (quantité) *</label>
            <input type="number" name="seuil_alerte" id="seuil_alerte" class="form-control" value="{{ old('seuil_alerte') }}">
        </div>

        <button type="submit" class="btn btn-primary">Créer</button>
        <a href="{{ route('produits.index') }}" class="btn btn-secondary">Retour</a>
    </form>

@endsection
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
                }
            }

            coutAchatInput.addEventListener('input', updateMarge);
            prixVenteInput.addEventListener('input', updateMarge);
        });
</script>