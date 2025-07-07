@extends('pages.admin.shared.layout')


@section('content')
<div class="container">
    <h2>Nouveau Transfert</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('transferts.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="magasin_destination_id">Magasin de destination</label>
            <select name="magasin_destination_id" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                @foreach($magasins as $magasin)
                    <option value="{{ $magasin->id }}">{{ $magasin->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="date_transfert">Date du transfert</label>
            <input type="date" name="date_transfert" class="form-control" required>
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
                <tr>
                    <td>
                        <select name="produits[]" class="form-control" required>
                            <option value="">-- Produit --</option>
                            @foreach($produits as $produit)
                                <option value="{{ $produit->id }}">{{ $produit->nom }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="quantites[]" class="form-control" min="1" step="1" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm removeRow">Supprimer</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-secondary" id="addRow">+ Ajouter un produit</button>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Enregistrer le transfert</button>
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





