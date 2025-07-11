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
        Schema::create('mouvement_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produit_id');
            $table->unsignedBigInteger('magasin_id');
            $table->enum('type', ['entree', 'sortie']);
            $table->decimal('quantite', 10, 0);
            $table->string('source_type');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('motif')->nullable();
            $table->datetime('date');
            $table->foreignId('lot_id')->nullable()->constrained('stock_lots')->onDelete('set null');
            $table->timestamps();

            $table->foreign('produit_id')->references('id')->on('produits');
            $table->foreign('magasin_id')->references('id')->on('magasins');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvement_stocks');
    }
};
