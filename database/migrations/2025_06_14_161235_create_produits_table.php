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
        Schema::create('produits', function (Blueprint $table) {
            $table->id(); 
            $table->string('nom');
            $table->string('reference');
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('magasin_id')->constrained('magasins')->onDelete('cascade');

            $table->decimal('cout_achat', 10, 2)->nullable();
            $table->decimal('prix_vente', 10, 2)->nullable();
            $table->decimal('marge', 5, 2)->default(0);
            $table->integer('seuil_alerte')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['magasin_id', 'reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
