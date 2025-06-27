@extends('pages.admin.shared.layout')

@section('content')
<div class="container"> <h2>Nouvelle vente</h2>
@include('flash-message')

<form action="{{ route('module.ventes.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="client_id" class="form-label">Client (optionnel)</label>
        <select name="client_id" class="form-select">
            <option value="">-- Aucun --</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}">{{ $client->nom }}</option>
            @endforeach
        </select>
    </div>

    <hr>

    <h5>Produits</h5>
    <table class="table" id="vente-produits">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Sous-total</th>
                <th><button type="button" class="btn btn-sm btn-success" id="add-ligne">+</button></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select name="produits[]" class="form-select produit-select" required>
                        <option value="">-- Sélectionner --</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id }}" data-prix="{{ $produit->prix_vente }}">
                                {{ $produit->nom }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="quantites[]" class="form-control quantite-input" min="1" value="1" required></td>
                <td><input type="number" name="prix_unitaires[]" class="form-control prix-input" readonly></td>
                <td><input type="text" class="form-control subtotal" readonly></td>
                <td><button type="button" class="btn btn-sm btn-danger remove-ligne">-</button></td>
            </tr>
        </tbody>
    </table>
    <hr>
    <div class="mb-3">
        <label>Mode de paiement</label>
        <select name="mode_paiement" class="form-select" required>
            <option value="cash">Cash</option>
            <option value="credit">Crédit</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Remise globale (facultatif)</label>
        <input type="number" name="remise" class="form-control" min="0" value="0">
    </div>


    <div class="text-end">
        <label>Total :</label>
        <input type="text" id="total-general" class="form-control" readonly>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Valider la vente</button>
        <a href="{{ route('module.ventes.index') }}" class="btn btn-secondary">Annuler</a>
    </div>
</form>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tbody = document.querySelector('#vente-produits tbody');

            function updateSubtotal(tr) {
                const qty = parseFloat(tr.querySelector('.quantite-input').value) || 0;
                const prix = parseFloat(tr.querySelector('.prix-input').value) || 0;
                const subtotal = qty * prix;
                tr.querySelector('.subtotal').value = subtotal.toFixed(0);
                return subtotal;
            }

            function updateTotal() {
                let total = 0;
                tbody.querySelectorAll('tr').forEach(tr => {
                    total += updateSubtotal(tr);
                });
                document.getElementById('total-general').value = total.toFixed(0);
            }

            function bindRowEvents(tr) {
                tr.querySelector('.produit-select').addEventListener('change', function () {
                    const prix = this.options[this.selectedIndex].dataset.prix || 0;
                    tr.querySelector('.prix-input').value = prix;
                    updateSubtotal(tr);
                    updateTotal();
                });

                tr.querySelector('.quantite-input').addEventListener('input', function () {
                    updateSubtotal(tr);
                    updateTotal();
                });

                tr.querySelector('.remove-ligne').addEventListener('click', function () {
                    const lignes = tbody.querySelectorAll('tr');
                    if (lignes.length > 1) {
                        tr.remove();
                        updateTotal();
                    } else {
                        alert('Il doit rester au moins un produit.');
                    }
                });
            }

            tbody.querySelectorAll('tr').forEach(bindRowEvents);

            document.getElementById('add-ligne').addEventListener('click', function () {
                const tr = tbody.querySelector('tr').cloneNode(true);
                tr.querySelectorAll('input').forEach(input => input.value = '');
                tr.querySelector('.quantite-input').value = 1;
                tr.querySelector('.prix-input').value = '';
                tr.querySelector('.subtotal').value = '';
                tr.querySelector('select').selectedIndex = 0;
                tbody.appendChild(tr);
                bindRowEvents(tr);
            });
        });
    </script>
@endsection


