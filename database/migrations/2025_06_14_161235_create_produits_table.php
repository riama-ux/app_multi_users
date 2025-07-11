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
            $table->decimal('cout_achat', 10, 0)->nullable();
            $table->decimal('prix_vente', 10, 0)->nullable();
            $table->decimal('marge', 5, 0)->default(0);
            $table->integer('seuil_alerte')->default(0);
            $table->string('description');
            $table->string('marque');
            $table->string('code');
            $table->enum('unite', ['pièce', 'kg', 'litre', 'mètre', 'paquet'])->default('pièce');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['magasin_id', 'reference']);
            $table->unique(['magasin_id', 'code']);
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
