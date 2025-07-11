<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Produit; // Assurez-vous d'importer le modèle Produit

class AjustementProductSearch extends Component
{
    public $search = '';
    public $products = [];
    public $magasinId;

    public function mount()
    {
        // Initialisez le magasinId depuis la session du magasin actif
        $this->magasinId = session('magasin_actif_id');
    }

    public function updatedSearch()
    {
        // La recherche se déclenche uniquement avec 3 caractères ou plus
        if (strlen($this->search) > 2) {
            $this->products = Produit::where('magasin_id', $this->magasinId)
                                    ->where(function($query) {
                                        $query->where('nom', 'like', '%' . $this->search . '%')
                                              ->orWhere('reference', 'like', '%' . $this->search . '%')
                                              ->orWhere('code', 'like', '%' . $this->search . '%');
                                    })
                                    // Pour les ajustements de sortie, il faut que le produit soit en stock.
                                    // Pour les ajustements d'entrée, on peut ajouter même si stock 0.
                                    // La vérification du stock réel sera faite côté serveur lors de la soumission.
                                    ->select('id', 'nom', 'reference', 'code', 'quantite', 'cout_achat') // Inclure quantite pour affichage et cout_achat pour prix unitaire par défaut
                                    ->limit(10) // Limiter le nombre de résultats
                                    ->get();
        } else {
            $this->products = [];
        }
    }

    // Méthode appelée lorsque l'utilisateur sélectionne un produit
    public function selectProduct($productId)
    {
        $product = Produit::where('magasin_id', $this->magasinId)->find($productId);

        if ($product) {
            // Émettre un événement JavaScript que la vue Blade de l'ajustement écoutera
            $this->dispatch('productSelectedForAjustement', product: [
                'id' => $product->id,
                'nom' => $product->nom,
                'code' => $product->code,
                'reference' => $product->reference,
                'quantite' => $product->quantite, // Stock actuel du produit
                'cout_achat' => $product->cout_achat, // Coût d'achat pour pré-remplir le prix unitaire ajusté
            ]);

            // Optionnel: Vider la recherche après sélection
            $this->search = '';
            $this->products = [];
        }
    }

    public function render()
    {
        return view('livewire.ajustement-product-search');
    }
}
