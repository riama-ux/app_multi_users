@extends('pages.admin.shared.layout')

@section('content')
<div class="container">
    <h1>Modifier la commande #{{ $commande->id }}</h1>

    @include('flash-message')

    <form action="{{ route('module.commandes.update', $commande->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="fournisseur_id" class="form-label">Fournisseur</label>
            <select name="fournisseur_id" id="fournisseur_id" class="form-control @error('fournisseur_id') is-invalid @enderror" required>
                <option value="">-- Choisir un fournisseur --</option>
                @foreach($fournisseurs as $fournisseur)
                    <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $commande->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>
                        {{ $fournisseur->nom }}
                    </option>
                @endforeach
            </select>
            @error('fournisseur_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="date_commande" class="form-label">Date de la commande</label>
            <input type="date" name="date_commande" id="date_commande" 
                   class="form-control @error('date_commande') is-invalid @enderror" 
                   value="{{ old('date_commande', $commande->date_commande ? $commande->date_commande->format('Y-m-d') : '') }}" required>
            @error('date_commande')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <hr>

        <h4>Lignes de commande</h4>

        <table class="table" id="lignes-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th><button type="button" class="btn btn-sm btn-success" id="add-ligne">+</button></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $oldLignes = old('produits') ?? [];
                @endphp

                @forelse($oldLignes as $i => $oldProduit)
                    <tr>
                        <td>
                            <select name="produits[]" class="form-control" required>
                                <option value="">-- Choisir un produit --</option>
                                @foreach($produits as $produit)
                                    <option value="{{ $produit->id }}" {{ $oldProduit == $produit->id ? 'selected' : '' }}>
                                        {{ $produit->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="quantites[]" min="1" class="form-control" value="{{ old('quantites')[$i] ?? 1 }}" required></td>
                        <td><input type="number" name="prix_unitaires[]" min="0" step="0.01" class="form-control" value="{{ old('prix_unitaires')[$i] ?? 0 }}" required></td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-ligne">-</button></td>
                    </tr>
                @empty
                    @foreach($commande->lignes as $ligne)
                        <tr>
                            <td>
                                <select name="produits[]" class="form-control" required>
                                    <option value="">-- Choisir un produit --</option>
                                    @foreach($produits as $produit)
                                        <option value="{{ $produit->id }}" {{ $ligne->produit_id == $produit->id ? 'selected' : '' }}>
                                            {{ $produit->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="quantites[]" min="1" class="form-control" value="{{ $ligne->quantite }}" required></td>
                            <td><input type="number" name="prix_unitaires[]" min="0" step="0.01" class="form-control" value="{{ $ligne->prix_unitaire }}" required></td>
                            <td><button type="button" class="btn btn-sm btn-danger remove-ligne">-</button></td>
                        </tr>
                    @endforeach
                @endforelse
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Modifier la commande</button>
        <a href="{{ route('module.commandes.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>

{{-- Script pour ajouter/supprimer des lignes dynamiquement --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addBtn = document.getElementById('add-ligne');
    const tbody = document.querySelector('#lignes-table tbody');

    addBtn.addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="produits[]" class="form-control" required>
                    <option value="">-- Choisir un produit --</option>
                    @foreach($produits as $produit)
                        <option value="{{ $produit->id }}">{{ $produit->nom }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="quantites[]" min="1" class="form-control" value="1" required></td>
            <td><input type="number" name="prix_unitaires[]" min="0" step="0.01" class="form-control" value="0" required></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-ligne">-</button></td>
        `;

        tbody.appendChild(tr);

        tr.querySelector('.remove-ligne').addEventListener('click', function () {
            tr.remove();
        });
    });

    tbody.querySelectorAll('.remove-ligne').forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('tr').remove();
        });
    });
});
</script>
@endsection

@endsection

