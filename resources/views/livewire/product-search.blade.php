<div>
    <div class="form-group mb-3">
        <label for="search-product">Rechercher un produit</label>
        <input type="text" id="search-product" wire:model.live="query" placeholder="Nom, référence, code ou description..." class="form-control">
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
                        {{-- Ici, on utilise directement $product['quantite'] au lieu de $product['quantite_disponible'] --}}
                        <span class="badge bg-info text-dark me-2">Stock dispo: {{ $product['quantite'] }}</span>
                        <button type="button" wire:click="selectProduct({{ $product['id'] }})" class="btn btn-success btn-sm">Ajouter</button>
                    </div>
                </li>
            @endforeach
        </ul>
    @elseif (strlen($query) >= 3 && count($products) === 0)
        <p class="text-muted">Aucun produit trouvé pour "{{ $query }}" dans ce magasin avec du stock.</p>
    @endif
</div>