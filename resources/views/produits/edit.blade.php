@extends('pages.admin.shared.layout')

@section('content')

<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
    <div class="nk-block-head-content">
        {{-- Mettez en surbrillance le nom du produit avec text-primary ou un autre accent --}}
        <h3 class="nk-block-title page-title">Modifier le Produit : <span class="text-primary">{{ $produit->nom }}</span></h3>
        <div class="nk-block-des text-soft">
            {{-- Utilisation de p.lead-text pour un sous-titre un peu plus grand si désiré, ou simple p --}}
            <p>Mettez à jour les informations du produit sélectionné.</p>
        </div>
    </div><div class="nk-block-head-content">
        {{-- Bouton de retour stylisé avec btn-dim pour un effet de transparence et btn-outline-secondary pour la couleur --}}
        <a href="{{ route('produits.index') }}" class="btn btn-dim btn-outline-secondary d-none d-sm-inline-flex">
            <em class="icon ni ni-arrow-left"></em><span>Retour à la liste</span>
        </a>
        {{-- Bouton icône pour les mobiles --}}
        <a href="{{ route('produits.index') }}" class="btn btn-icon btn-dim btn-outline-secondary d-inline-flex d-sm-none">
            <em class="icon ni ni-arrow-left"></em>
        </a>
    </div></div>
</div><div class="nk-block">
    <div class="card card-bordered">
        <div class="card-inner">

            {{-- Gestion des erreurs DashLite --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-icon">
                    <em class="icon ni ni-alert-circle"></em>
                    <p>Le formulaire contient des erreurs. Veuillez les corriger :</p>
                    <ul class="mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('produits.update', $produit->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-gs">
                    {{-- Colonne 1: Informations de base --}}
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label" for="nom">Nom *</label>
                            <div class="form-control-wrap">
                                <input type="text" name="nom" id="nom" class="form-control form-control-lg @error('nom') is-invalid @enderror" value="{{ old('nom', $produit->nom) }}" required>
                                @error('nom')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reference">Référence</label>
                            <div class="form-control-wrap">
                                {{-- La référence est généralement non modifiable après création --}}
                                <input type="text" name="reference" id="reference" class="form-control form-control-lg" value="{{ old('reference', $produit->reference) }}" readonly>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="code">Code</label>
                            <div class="form-control-wrap">
                                <input type="text" name="code" id="code" class="form-control form-control-lg @error('code') is-invalid @enderror" value="{{ old('code', $produit->code ?? '') }}" required>
                                @error('code')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="marque">Marque</label>
                            <div class="form-control-wrap">
                                <input type="text" name="marque" id="marque" class="form-control form-control-lg @error('marque') is-invalid @enderror" value="{{ old('marque', $produit->marque ?? '') }}" required>
                                @error('marque')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">Description</label>
                            <div class="form-control-wrap">
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $produit->description ?? '') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Colonne 2: Catégorie, Unité et Prix --}}
                    <div class="col-lg-6">

                        <div class="form-group">
                            <label class="form-label" for="categorie_id">Catégorie *</label>
                            <div class="form-control-wrap">
                                <select name="categorie_id" id="categorie_id" class="form-select form-control-lg @error('categorie_id') is-invalid @enderror" data-search="on" required>
                                    <option value="">-- Choisir une catégorie --</option>
                                    @foreach ($categories as $categorie)
                                        <option value="{{ $categorie->id }}" {{ old('categorie_id', $produit->categorie_id) == $categorie->id ? 'selected' : '' }}>
                                            {{ $categorie->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categorie_id')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="unite">Unité *</label>
                            <div class="form-control-wrap">
                                <select name="unite" id="unite" class="form-select form-control-lg @error('unite') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner --</option>
                                    @foreach(['pièce', 'kg', 'litre', 'mètre', 'paquet'] as $unit)
                                        <option value="{{ $unit }}" {{ old('unite', $produit->unite ?? '') == $unit ? 'selected' : '' }}>
                                            {{ ucfirst($unit) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unite')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="cout_achat">Coût d'achat par défaut *</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-right"><em class="icon ni ni-dollar"></em></div>
                                <input type="number" step="0.01" name="cout_achat" id="cout_achat" class="form-control form-control-lg @error('cout_achat') is-invalid @enderror" value="{{ old('cout_achat', $produit->cout_achat) }}" required>
                                @error('cout_achat')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="prix_vente">Prix de vente par défaut *</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-right"><em class="icon ni ni-dollar"></em></div>
                                <input type="number" step="0.01" name="prix_vente" id="prix_vente" class="form-control form-control-lg @error('prix_vente') is-invalid @enderror" value="{{ old('prix_vente', $produit->prix_vente) }}" required>
                                @error('prix_vente')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="marge">Marge (%) *</label>
                            <div class="form-control-wrap">
                                <div class="form-icon form-icon-right"><em class="icon ni ni-percent"></em></div>
                                <input type="number" step="0.01" name="marge" id="marge" class="form-control form-control-lg @error('marge') is-invalid @enderror" value="{{ old('marge', $produit->marge) }}" required readonly>
                                @error('marge')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="seuil_alerte">Seuil d'alerte (quantité)</label>
                            <div class="form-control-wrap">
                                <input type="number" name="seuil_alerte" id="seuil_alerte" class="form-control form-control-lg @error('seuil_alerte') is-invalid @enderror" value="{{ old('seuil_alerte', $produit->seuil_alerte) }}">
                                @error('seuil_alerte')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Boutons de soumission --}}
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg me-2"><em class="icon ni ni-save me-2"></em> Mettre à jour</button>
                    <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary btn-lg">Annuler</a>
                </div>
            </form>
        </div></div></div>@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const coutAchatInput = document.getElementById('cout_achat');
        const prixVenteInput = document.getElementById('prix_vente');
        const margeInput = document.getElementById('marge');

        /**
         * Calcule et met à jour la marge en fonction du coût d'achat et du prix de vente.
         */
        function updateMarge() {
            const cout = parseFloat(coutAchatInput.value);
            const prix = parseFloat(prixVenteInput.value);

            // Vérifie que les valeurs sont des nombres valides et que le coût d'achat est supérieur à 0
            if (!isNaN(cout) && cout > 0 && !isNaN(prix)) {
                // Calcule la marge : ((Prix Vente - Coût Achat) / Coût Achat) * 100
                const marge = ((prix - cout) / cout) * 100;
                // Affiche la marge arrondie à 2 décimales
                margeInput.value = marge.toFixed(2);
            } else {
                // Si les valeurs ne sont pas valides, la marge est à 0 ou vide
                margeInput.value = '';
            }
        }

        // Écouteurs d'événements pour mettre à jour la marge lors de la saisie
        coutAchatInput.addEventListener('input', updateMarge);
        prixVenteInput.addEventListener('input', updateMarge);

        // Appelle la fonction une fois au chargement pour s'assurer que la marge est calculée si les champs ont déjà des valeurs (comme avec old() ou le modèle chargé)
        updateMarge();
    });
</script>
@endsection