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
        Schema::create('ligne_ajustements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajustement_id')->constrained('ajustements')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
            $table->decimal('quantite_ajustee', 10, 0); // Quantité ajustée pour cette ligne
            $table->decimal('prix_unitaire_ajuste', 10, 0)->nullable(); // Prix unitaire pour l'ajustement (peut être 0)
            $table->string('motif_ligne')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_ajustements');
    }
};
