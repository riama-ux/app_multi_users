<div>
    <div class="form-group mb-3">
        <label for="search-product-commande">Rechercher un produit à commander</label>
        <input type="text" id="search-product-commande" wire:model.live="query" placeholder="Nom, référence, code ou description..." class="form-control">
    </div>

    @if (strlen($query) >= 3 && count($products) > 0)
        <ul class="list-group mb-3">
            @foreach ($products as $product)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $product['nom'] }}</strong> ({{ $product['reference'] }})<br>
                        <small>Code: {{ $product['code'] }}</small>
                        <small class="text-muted">Description: {{ Str::limit($product['description'], 50) }}</small>
                    </div>
                    <div>
                        <span class="badge bg-secondary me-2">Coût Achat: {{ number_format($product['cout_achat'], 2) }}</span>
                        <button type="button" wire:click="selectProduct({{ $product['id'] }})" class="btn btn-success btn-sm">Ajouter</button>
                    </div>
                </li>
            @endforeach
        </ul>
    @elseif (strlen($query) >= 3 && count($products) === 0)
        <p class="text-muted">Aucun produit trouvé pour "{{ $query }}" dans ce magasin.</p>
    @endif
</div>
