@extends('pages.admin.shared.layout')

@section('content')
    <h3>Nouvelle commande fournisseur</h3>

    <form action="{{ route('module.commandes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Fournisseur</label>
            <select name="fournisseur_id" class="form-select" required>
                <option value="">-- Sélectionnez --</option>
                @foreach ($fournisseurs as $f)
                    <option value="{{ $f->id }}">{{ $f->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Date commande</label>
            <input type="date" name="date_commande" class="form-control" required value="{{ date('Y-m-d') }}">
        </div>

        <h5 class="mt-4">Produits</h5>
        <div id="ligne-produits">
            <div class="row mb-2">
                <div class="col-md-5">
                    <select name="produits[]" class="form-select" required>
                        <option value="">-- Produit --</option>
                        @foreach ($produits as $p)
                            <option value="{{ $p->id }}">{{ $p->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="quantites[]" class="form-control" placeholder="Qté" required min="1">
                </div>
                <div class="col-md-3">
                    <input type="number" name="prix_unitaires[]" class="form-control" placeholder="Prix U." required>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="ajouterLigne()">+ Ajouter un produit</button>

        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('module.commandes.index') }}" class="btn btn-secondary">Annuler</a>
    </form>

<script>
    function ajouterLigne() {
        const div = document.createElement('div');
        div.className = 'row mb-2';
        div.innerHTML = `
            <div class="col-md-5">
                <select name="produits[]" class="form-select" required>
                    <option value="">-- Produit --</option>
                    @foreach ($produits as $p)
                        <option value="{{ $p->id }}">{{ $p->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="quantites[]" class="form-control" placeholder="Qté" required min="1">
            </div>
            <div class="col-md-3">
                <input type="number" name="prix_unitaires[]" class="form-control" placeholder="Prix U." required>
            </div>
        `;
        document.getElementById('ligne-produits').appendChild(div);
    }
</script>
@endsection
