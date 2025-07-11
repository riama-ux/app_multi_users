<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Produit;

class CommandeProductSearch extends Component
{
    public $query = '';
    public $products = [];
    public $magasinId;

    public function mount()
    {
        // Initialisez le magasinId depuis la session du magasin actif
        $this->magasinId = session('magasin_actif_id');
    }

    public function updatedQuery()
    {
        // La recherche se déclenche uniquement avec 3 caractères ou plus
        if (strlen($this->query) < 3) {
            $this->products = [];
            return;
        }

        // Rechercher les produits dans le magasin actif
        $produits = Produit::where('magasin_id', $this->magasinId)
            ->where(function ($q) {
                $q->where('nom', 'LIKE', '%' . $this->query . '%')
                  ->orWhere('reference', 'LIKE', '%' . $this->query . '%')
                  ->orWhere('code', 'LIKE', '%' . $this->query . '%')
                  ->orWhere('description', 'LIKE', '%' . $this->query . '%');
            })
            // Pour les commandes, nous n'avons pas besoin de filtrer par 'quantite > 0' ici,
            // car on peut commander des produits même s'ils sont en rupture de stock.
            ->select('id', 'nom', 'reference', 'code', 'description', 'cout_achat', 'prix_vente') // Inclure cout_achat pour le prix unitaire par défaut
            ->limit(10) // Limiter le nombre de résultats
            ->get();

        $this->products = $produits->toArray(); // Convertir la collection en tableau pour Livewire
    }

    // Méthode appelée lorsque l'utilisateur sélectionne un produit
    public function selectProduct($productId)
    {
        $product = Produit::where('magasin_id', $this->magasinId)->find($productId);

        if ($product) {
            // Émettre un événement JavaScript que la vue Blade de la commande écoutera
            // Nous passons toutes les informations nécessaires pour créer une ligne de commande
            $this->dispatch('productSelectedForCommande', product: [
                'id' => $product->id,
                'nom' => $product->nom,
                'code' => $product->code,
                'reference' => $product->reference,
                'description' => $product->description,
                'prix_unitaire_defaut' => $product->cout_achat, // Utiliser le coût d'achat par défaut comme prix unitaire initial
            ]);

            // Optionnel: Vider la recherche après sélection
            $this->query = '';
            $this->products = [];
        }
    }

    public function render()
    {
        return view('livewire.commande-product-search');
    }
}
