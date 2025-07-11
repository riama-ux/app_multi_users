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
        Schema::create('ligne_transferts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfert_id');
            $table->unsignedBigInteger('produit_id');
            $table->decimal('quantite', 10, 0);
            $table->timestamps();

            $table->foreign('transfert_id')->references('id')->on('transferts')->onDelete('cascade');
            $table->foreign('produit_id')->references('id')->on('produits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_transferts');
    }
};
