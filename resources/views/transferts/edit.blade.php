@extends('pages.admin.shared.layout')

@section('content')
<div class="container">
    <h2>Modifier le Transfert</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('transferts.update', $transfert->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="magasin_destination_id">Magasin de destination</label>
            <select name="magasin_destination_id" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                @foreach($magasins as $magasin)
                    <option value="{{ $magasin->id }}" {{ $magasin->id == $transfert->magasin_destination_id ? 'selected' : '' }}>
                        {{ $magasin->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="date_transfert">Date du transfert</label>
            <input type="date" name="date_transfert" class="form-control" value="{{ $transfert->date_transfert->format('Y-m-d') }}" required>
        </div>

        <hr>
        <h5>Produits à transférer</h5>

        <table class="table table-bordered" id="produitsTable">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfert->ligneTransferts as $ligne)
                    <tr>
                        <td>
                            <select name="produits[]" class="form-control" required>
                                <option value="">-- Produit --</option>
                                @foreach($produits as $produit)
                                    <option value="{{ $produit->id }}" {{ $produit->id == $ligne->produit_id ? 'selected' : '' }}>
                                        {{ $produit->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="quantites[]" class="form-control" min="1" step="1" value="{{ $ligne->quantite }}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm removeRow">Supprimer</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" class="btn btn-secondary" id="addRow">+ Ajouter un produit</button>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="{{ route('transferts.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addRowBtn = document.getElementById('addRow');
        const tableBody = document.querySelector('#produitsTable tbody');

        addRowBtn.addEventListener('click', function () {
            const newRow = tableBody.rows[0].cloneNode(true);
            newRow.querySelectorAll('input, select').forEach(input => {
                input.value = '';
            });
            tableBody.appendChild(newRow);
        });

        tableBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow')) {
                const row = e.target.closest('tr');
                if (tableBody.rows.length > 1) {
                    row.remove();
                }
            }
        });
    });
</script>
@endsection



