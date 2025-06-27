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
            $table->foreignId('categorie_id')->constrained()->onDelete('cascade');
            $table->foreignId('magasin_id')->constrained()->onDelete('cascade'); // ✅ Ajouté pour multi-magasin
            $table->string('code'); // ex : code-barres
            $table->integer('prix_achat');
            $table->integer('cout_achat')->nullable(); // facultatif si tu ne le gères pas toujours
            $table->integer('prix_vente');
            $table->text('description')->nullable();
            $table->timestamps();

            // ✅ Définition de l'unicité combinée en dehors des colonnes
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
