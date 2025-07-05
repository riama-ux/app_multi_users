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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('magasin_id');
            $table->enum('statut', ['en_attente', 'livree', 'annulee'])->default('en_attente');
            $table->datetime('date_commande');
            $table->datetime('date_reception')->nullable();
            $table->decimal('cout_transport', 10, 2)->nullable();
            $table->decimal('frais_suppl', 10, 2)->nullable();
            $table->decimal('cout_total', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('fournisseur_id')->references('id')->on('fournisseurs');
            $table->foreign('magasin_id')->references('id')->on('magasins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
