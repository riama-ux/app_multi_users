<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Produit;
use Livewire\Attributes\On;

class RetourClientProductSearch extends Component
{
    public $search = '';
    public $products = [];
    public $selectedProduct = null;

    public function updatedSearch()
    {
        $magasinId = session('magasin_actif_id');
        if (empty($magasinId)) {
            $this->products = [];
            return;
        }

        if (strlen($this->search) > 2) {
            $this->products = Produit::where('magasin_id', $magasinId)
                                     ->where(function($query) {
                                         $query->where('nom', 'like', '%' . $this->search . '%')
                                               ->orWhere('reference', 'like', '%' . $this->search . '%')
                                               ->orWhere('code', 'like', '%' . $this->search . '%');
                                     })
                                     ->limit(10) // Limiter les résultats
                                     ->get();
        } else {
            $this->products = [];
        }
    }

    public function selectProduct($productId)
    {
        $this->selectedProduct = Produit::find($productId);

        if ($this->selectedProduct) {
            // Dispatch l'événement pour ajouter le produit au tableau de retour
            $this->dispatch('productSelectedForRetourClient', product: $this->selectedProduct);
            $this->search = ''; // Effacer la recherche
            $this->products = []; // Effacer les résultats
        }
    }

    #[On('productCreated')]
    public function onProductCreated($product)
    {
        // Si un nouveau produit est créé via le modal, l'ajouter directement au tableau de retour
        $this->dispatch('productSelectedForRetourClient', product: $product);
    }

    public function render()
    {
        return view('livewire.retour-client-product-search');
    }
}
