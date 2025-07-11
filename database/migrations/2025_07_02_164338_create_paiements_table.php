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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vente_id');
            $table->decimal('montant', 10, 0);
            $table->enum('mode_paiement', ['especes', 'mobile_money', 'virement', 'cheque', 'autre'])->default('especes');
            $table->datetime('date_paiement');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('vente_id')->references('id')->on('ventes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
