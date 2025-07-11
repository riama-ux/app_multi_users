<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Produit; // Assurez-vous d'importer votre modèle Produit
use App\Models\Stock; 

class ProductSearch extends Component
{
    public $query = '';
    public $products = [];
    public $magasinId;

    public function mount()
    {
        // Initialisez le magasinId ici, par exemple depuis la session
        $this->magasinId = session('magasin_actif_id');
    }

    public function updatedQuery()
    {
        // Assurez-vous que la recherche se déclenche uniquement avec 3 caractères ou plus
        if (strlen($this->query) < 3) {
            $this->products = [];
            return;
        }

        $produits = Produit::where('magasin_id', $this->magasinId)
            // Filtre par le nom, référence, code ou description
            ->where(function ($q) {
                $q->where('nom', 'LIKE', '%' . $this->query . '%')
                  ->orWhere('reference', 'LIKE', '%' . $this->query . '%')
                  ->orWhere('code', 'LIKE', '%' . $this->query . '%')
                  ->orWhere('description', 'LIKE', '%' . $this->query . '%');
            })
            // Ajouter une condition pour s'assurer que la quantité est supérieure à 0
            ->where('quantite', '>', 0)
            // Sélectionner les champs pertinents, y compris 'quantite'
            ->select('id', 'nom', 'reference', 'code', 'description', 'quantite')
            ->limit(10) // Limiter le nombre de résultats
            ->get();

        // Plus besoin de boucler pour chercher le stock, car 'quantite' est directement sur le produit
        $this->products = $produits->toArray(); // Convertir la collection en tableau pour Livewire
    }

    // Nouvelle méthode pour sélectionner un produit
    public function selectProduct($productId)
    {
        $product = Produit::where('magasin_id', $this->magasinId)->find($productId);

        if ($product) {
            // Émettre un événement JavaScript que la vue Blade écoutera
            $this->dispatch('productSelected', product: [
                'id' => $product->id,
                'nom' => $product->nom,
                'code' => $product->code,
                'reference' => $product->reference,
                'description' => $product->description, // Assurez-vous que la description est passée
            ], availableStock: $product->quantite); // Passe la quantité agrégée comme stock disponible

            // Optionnel: Vider la recherche après sélection
            $this->query = '';
            $this->products = [];
        }
    }

    public function render()
    {
        return view('livewire.product-search');
    }
}
