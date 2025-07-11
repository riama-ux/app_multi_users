<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ligne_retour_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retour_client_id')->constrained('retours_clients')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->decimal('quantite_retournee', 10, 0);
            $table->decimal('prix_unitaire_retour', 10, 0); // Prix auquel le produit est réintégré/remboursé
            $table->text('motif_ligne')->nullable(); // Motif spécifique à cette ligne de produit
            $table->foreignId('lot_id')->nullable()->constrained('stock_lots')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_retour_clients');
    }
};
