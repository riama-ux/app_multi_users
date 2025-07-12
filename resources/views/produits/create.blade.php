@extends('pages.admin.shared.layout')

@section('content')

<div class="nk-block nk-block-lg">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">Créer un produit</h4>
            <div class="nk-block-des">
                <p>Remplissez les informations ci-dessous pour ajouter un nouveau produit.</p>
            </div>
        </div>
    </div><div class="card card-bordered">
        <div class="card-inner">
            
            @if ($errors->any())
                <div class="alert alert-danger alert-icon">
                    <em class="icon ni ni-cross-circle"></em>
                    <p>
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </p>
                </div>
            @endif

            <form action="{{ route('produits.store') }}" method="POST" class="form-validate is-alter">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="categorie_id" class="form-label">Catégorie *</label>
                            <div class="form-control-wrap">
                                <select name="categorie_id" id="categorie_id" class="form-select form-control" required>
                                    <option value="">-- Choisir une catégorie --</option>
                                    @foreach ($categories as $categorie)
                                        <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                            {{ $categorie->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nom" class="form-label">Nom *</label>
                            <div class="form-control-wrap">
                                <input type="text" name="nom" id="nom" class="form-control" value="{{ old('nom') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reference" class="form-label">Référence</label>
                            <div class="form-control-wrap">
                                <input type="text" name="reference" id="reference" class="form-control" value="{{ old('reference') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code" class="form-label">Code (laisser vide pour générer automatiquement)</label>
                            <div class="form-control-wrap">
                                <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="marque" class="form-label">Marque *</label>
                            <div class="form-control-wrap">
                                <input type="text" name="marque" id="marque" class="form-control" value="{{ old('marque', $produit->marque ?? '') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="unite" class="form-label">Unité*</label>
                            <div class="form-control-wrap">
                                <select name="unite" id="unite" class="form-select form-control" required>
                                    <option value="">-- Sélectionner --</option>
                                    @foreach(['pièce', 'kg', 'litre', 'mètre', 'paquet'] as $unit)
                                        <option value="{{ $unit }}" {{ old('unite', $produit->unite ?? '') == $unit ? 'selected' : '' }}>
                                            {{ ucfirst($unit) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label for="description" class="form-label">Description *</label>
                    <div class="form-control-wrap">
                        <textarea name="description" id="description" class="form-control" required>{{ old('description', $produit->description ?? '') }}</textarea>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cout_achat" class="form-label">Coût d'achat par défaut *</label>
                            <div class="form-control-wrap">
                                <input type="number" step="0.01" name="cout_achat" id="cout_achat" class="form-control" value="{{ old('cout_achat') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="prix_vente" class="form-label">Prix de vente par défaut *</label>
                            <div class="form-control-wrap">
                                <input type="number" step="0.01" name="prix_vente" id="prix_vente" class="form-control" value="{{ old('prix_vente') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="marge" class="form-label">Marge (%) *</label>
                            <div class="form-control-wrap">
                                <input type="number" step="0.01" name="marge" id="marge" class="form-control" value="{{ old('marge') }}" required readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label for="seuil_alerte" class="form-label">Seuil d'alerte (quantité) *</label>
                    <div class="form-control-wrap">
                        <input type="number" name="seuil_alerte" id="seuil_alerte" class="form-control" value="{{ old('seuil_alerte') }}">
                    </div>
                </div>

                <div class="form-group mt-5">
                    <button type="submit" class="btn btn-primary p-3 me-2">Créer</button>
                    <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary p-3">Retour</a>
                </div>
            </form>

        </div></div></div>
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

        // Écouteurs d'événements pour les champs de coût d'achat et de prix de vente
        coutAchatInput.addEventListener('input', updateMarge);
        prixVenteInput.addEventListener('input', updateMarge);
    });
</script>
