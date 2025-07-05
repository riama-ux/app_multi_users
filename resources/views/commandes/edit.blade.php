@extends('pages.admin.shared.layout')

@section('content')
    <h1>Modifier la commande #{{ $commande->id }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('commandes.update', $commande) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="fournisseur_id" class="form-label">Fournisseur *</label>
            <select name="fournisseur_id" id="fournisseur_id" class="form-select" required>
                <option value="">-- Choisir un fournisseur --</option>
                @foreach ($fournisseurs as $fournisseur)
                    <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $commande->fournisseur_id) == $fournisseur->id ? 'selected' : '' }}>
                        {{ $fournisseur->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="date_commande" class="form-label">Date de commande *</label>
            <input type="date" name="date_commande" id="date_commande" class="form-control" value="{{ old('date_commande', $commande->date_commande->format('Y-m-d')) }}" required>
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
                @foreach ($commande->lignesCommande as $i => $ligne)
                    <tr class="ligne-commande">
                        <td>
                            <select name="lignes[{{ $i }}][produit_id]" class="form-select" required>
                                <option value="">-- Choisir un produit --</option>
                                @foreach ($produits as $produit)
                                    <option value="{{ $produit->id }}" {{ old("lignes.$i.produit_id", $ligne->produit_id) == $produit->id ? 'selected' : '' }}>
                                        {{ $produit->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="lignes[{{ $i }}][id]" value="{{ $ligne->id }}">
                        </td>
                        <td><input type="number" name="lignes[{{ $i }}][quantite]" class="form-control" min="1" value="{{ old("lignes.$i.quantite", $ligne->quantite) }}" required></td>
                        <td><input type="number" step="0.01" name="lignes[{{ $i }}][prix_unitaire]" class="form-control" min="0" value="{{ old("lignes.$i.prix_unitaire", $ligne->prix_unitaire) }}" required></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-ligne">-</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" id="add-ligne" class="btn btn-secondary mb-3">Ajouter une ligne</button>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="{{ route('commandes.index') }}" class="btn btn-secondary">Annuler</a>
    </form>

    <script>
        let index = {{ $commande->lignesCommande->count() }};

        document.getElementById('add-ligne').addEventListener('click', () => {
            const tbody = document.querySelector('#lignes-commande-table tbody');
            const newRow = document.querySelector('.ligne-commande').cloneNode(true);

            newRow.querySelectorAll('select, input').forEach(el => {
                if (el.name.match(/lignes\[\d+\]/)) {
                    el.name = el.name.replace(/lignes\[\d+\]/, `lignes[${index}]`);
                    if (el.type !== 'hidden') el.value = '';
                }
            });

            tbody.appendChild(newRow);
            index++;

            newRow.querySelector('.remove-ligne').addEventListener('click', function () {
                this.closest('tr').remove();
            });
        });

        document.querySelectorAll('.remove-ligne').forEach(btn => {
            btn.addEventListener('click', function () {
                this.closest('tr').remove();
            });
        });
    </script>
@endsection
