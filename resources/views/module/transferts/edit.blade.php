@extends('pages.admin.shared.layout')

@section('content')
<h3>Modifier le transfert</h3>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form action="{{ route('module.transferts.update', ['transfert' => $transfert->id]) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Magasin source :</label>
        <input type="text" class="form-control" value="{{ $transfert->magasinSource->nom ?? '-' }}" disabled>
    </div>

    <div class="mb-3">
        <label for="magasin_destination_id">Magasin destination :</label>
        <select name="magasin_destination_id" id="magasin_destination_id" class="form-control" required>
            @foreach($magasins as $magasin)
                <option value="{{ $magasin->id }}" {{ $transfert->magasin_destination_id == $magasin->id ? 'selected' : '' }}>
                    {{ $magasin->nom }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="date_transfert">Date de transfert :</label>
        <input type="date" name="date_transfert" id="date_transfert" class="form-control" value="{{ $transfert->date_transfert }}" required>
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
            @foreach($transfert->lignes as $ligne)
            <tr>
                <td>
                    <select name="produits[]" class="form-control" required>
                        <option value="">-- Choisir --</option>
                        @foreach($produits as $produit)
                            <option value="{{ $produit->id }}" {{ $produit->id == $ligne->produit_id ? 'selected' : '' }}>
                                {{ $produit->nom }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="quantites[]" class="form-control" min="1" value="{{ $ligne->quantite }}" required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger supprimer-ligne">-</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
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
