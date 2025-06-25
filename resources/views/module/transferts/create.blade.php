@extends('pages.admin.shared.layout')

@section('content')
<h3>Créer un nouveau transfert</h3>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form action="{{ route('module.transferts.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Magasin source :</label>
        <input type="text" class="form-control" value="{{ \App\Models\Magasin::find(session('magasin_actif_id'))->nom ?? '-' }}" disabled>
    </div>

    <div class="mb-3">
        <label for="magasin_destination_id">Magasin destination :</label>
        <select name="magasin_destination_id" id="magasin_destination_id" class="form-control" required>
            <option value="">-- Choisir --</option>
            @foreach($magasins as $magasin)
                <option value="{{ $magasin->id }}" {{ old('magasin_destination_id') == $magasin->id ? 'selected' : '' }}>
                    {{ $magasin->nom }}
                </option>
            @endforeach
        </select>
        @error('magasin_destination_id')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <div class="mb-3">
        <label for="date_transfert">Date de transfert :</label>
        <input type="date" name="date_transfert" id="date_transfert" class="form-control" value="{{ old('date_transfert', date('Y-m-d')) }}" required>
        @error('date_transfert')<small class="text-danger">{{ $message }}</small>@enderror
    </div>

    <hr>

    <h5>Produits à transférer</h5>

    <table class="table table-bordered" id="table-produits">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th><button type="button" class="btn btn-sm btn-primary" id="ajouter-produit">+</button></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select name="produits[]" class="form-control" required>
                        <option value="">-- Choisir --</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id }}">{{ $produit->nom }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="quantites[]" class="form-control" min="1" required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger supprimer-ligne">-</button>
                </td>
            </tr>
        </tbody>
    </table>

    <button type="submit" class="btn btn-success">Enregistrer</button>
    <a href="{{ route('module.transferts.index') }}" class="btn btn-secondary">Annuler</a>
</form>

<script>
    document.getElementById('ajouter-produit').addEventListener('click', function () {
        let tableBody = document.querySelector('#table-produits tbody');
        let newRow = tableBody.rows[0].cloneNode(true);

        newRow.querySelector('select').value = '';
        newRow.querySelector('input').value = '';

        tableBody.appendChild(newRow);
    });

    document.querySelector('#table-produits').addEventListener('click', function(e) {
        if(e.target.classList.contains('supprimer-ligne')) {
            let rows = document.querySelectorAll('#table-produits tbody tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert('Il doit y avoir au moins un produit.');
            }
        }
    });
</script>
@endsection
