<div class="mb-3">
    <label for="search-retour-product" class="form-label">Rechercher un produit</label>
    <input type="text"
           id="search-retour-product"
           class="form-control"
           placeholder="Nom, Référence ou Code du produit"
           wire:model.live.debounce.300ms="search">

    @if (!empty($products))
        <ul class="list-group mt-2" style="max-height: 200px; overflow-y: auto;">
            @foreach ($products as $product)
                <li class="list-group-item list-group-item-action"
                    wire:click="selectProduct({{ $product->id }})"
                    style="cursor: pointer;">
                    {{ $product->nom }} ({{ $product->reference }}) - Stock actuel: {{ $product->quantite }}
                </li>
            @endforeach
        </ul>
    @elseif (strlen($search) > 2 && empty($products))
        <p class="text-muted mt-2">Aucun produit trouvé pour "{{ $search }}".</p>
    @endif
    {{-- Bouton pour ouvrir le modal de création de produit --}}
    <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#modalProduit">
        + Créer un nouveau produit
    </button>
</div>
