@extends('pages.admin.shared.layout')

@section('content')
    <h1>Nouvelle commande</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('commandes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="fournisseur_id" class="form-label">Fournisseur *</label>
            <select name="fournisseur_id" id="fournisseur_id" class="form-select" required>
                <option value="">-- Choisir un fournisseur --</option>
                @foreach ($fournisseurs as $fournisseur)
                    <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                        {{ $fournisseur->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="date_commande" class="form-label">Date de commande *</label>
            <input type="date" name="date_commande" id="date_commande" class="form-control" value="{{ old('date_commande', date('Y-m-d')) }}" required>
        </div>

        <h4>Lignes de commande</h4>

        <table class="table" id="lignes-commande-table">
            <thead>
                <tr>
                    <th>Produit *</th>
                    <th>Quantité *</th>
                    <th>Prix unitaire *</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr class="ligne-commande">
                    <td>
                        <select name="lignes[0][produit_id]" class="form-select" required>
                            <option value="">-- Choisir un produit --</option>
                            @foreach ($produits as $produit)
                                <option value="{{ $produit->id }}">{{ $produit->nom }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="lignes[0][quantite]" class="form-control" min="1" required></td>
                    <td><input type="number" step="0.01" name="lignes[0][prix_unitaire]" class="form-control" min="0" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-ligne">-</button></td>
                </tr>
            </tbody>
        </table>

        <button type="button" id="add-ligne" class="btn btn-secondary mb-3">Ajouter une ligne</button>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="{{ route('commandes.index') }}" class="btn btn-secondary">Annuler</a>
    </form>

    <script>
        let index = 1;
        document.getElementById('add-ligne').addEventListener('click', () => {
            const tbody = document.querySelector('#lignes-commande-table tbody');
            const newRow = document.querySelector('.ligne-commande').cloneNode(true);

            newRow.querySelectorAll('select, input').forEach(el => {
                if (el.name.includes('lignes[0]')) {
                    el.name = el.name.replace('lignes[0]', `lignes[${index}]`);
                    el.value = '';
                }
            });

            tbody.appendChild(newRow);
            index++;

            // Bouton supprimer ligne
            newRow.querySelector('.remove-ligne').addEventListener('click', function () {
                this.closest('tr').remove();
            });
        });

        // Supprimer la première ligne si bouton cliqué
        document.querySelectorAll('.remove-ligne').forEach(btn => {
            btn.addEventListener('click', function () {
                this.closest('tr').remove();
            });
        });
    </script>
@endsection
