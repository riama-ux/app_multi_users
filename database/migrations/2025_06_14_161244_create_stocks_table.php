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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produit_id');
            $table->unsignedBigInteger('magasin_id');
            $table->decimal('quantite', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes(); // ajoute la colonne deleted_at nullable
            $table->foreign('produit_id')->references('id')->on('produits')->onDelete('cascade');
            $table->foreign('magasin_id')->references('id')->on('magasins')->onDelete('cascade');
            $table->unique(['produit_id', 'magasin_id']); // Un seul stock par produit-magasin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
