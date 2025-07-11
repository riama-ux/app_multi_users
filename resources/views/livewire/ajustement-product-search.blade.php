<div>
    <div class="mb-3">
        <label for="search-ajustement-product" class="form-label">Rechercher un produit à ajuster</label>
        <input type="text"
               id="search-ajustement-product"
               class="form-control"
               placeholder="Nom, Référence ou Code du produit"
               wire:model.live.debounce.300ms="search">
    </div>

    @if (strlen($search) > 2 && count($products) > 0)
        <ul class="list-group mb-3">
            @foreach ($products as $product)
                <li class="list-group-item list-group-item-action" wire:click="selectProduct({{ $product['id'] }})">
                    <div>
                        <strong>{{ $product['nom'] }}</strong> ({{ $product['reference'] }})<br>
                        <small>Code: {{ $product['code'] }}</small><br>
                        <span class="badge bg-info">Stock actuel: {{ $product['quantite'] }}</span>
                    </div>
                </li>
            @endforeach
        </ul>
    @elseif (strlen($search) > 2 && count($products) === 0)
        <p class="text-muted">Aucun produit trouvé pour "{{ $search }}" dans ce magasin.</p>
    @endif
</div>
