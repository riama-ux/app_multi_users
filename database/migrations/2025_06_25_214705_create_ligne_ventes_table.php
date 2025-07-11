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
        Schema::create('ligne_ventes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vente_id');
            $table->unsignedBigInteger('produit_id');
            $table->decimal('quantite', 10, 0);
            $table->decimal('prix_unitaire', 10, 0);
            $table->decimal('prix_total', 10, 0);
            $table->foreignId('lot_id')->nullable()->constrained('stock_lots')->onDelete('set null');
            $table->timestamps();

            $table->foreign('vente_id')->references('id')->on('ventes')->onDelete('cascade');
            $table->foreign('produit_id')->references('id')->on('produits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_ventes');
    }
};
