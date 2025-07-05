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
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // utilisateur ayant effectué la vente
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('magasin_id');
            $table->decimal('total_ht', 10, 2);
            $table->decimal('remise', 10, 2)->default(0);
            $table->decimal('total_ttc', 10, 2);
            $table->decimal('montant_paye', 10, 2);
            $table->decimal('reste_a_payer', 10, 2);
            $table->enum('statut', ['payee', 'partielle', 'credit'])->default('payee');
            $table->enum('mode_paiement', ['especes', 'mobile_money', 'virement', 'cheque', 'autre'])->default('especes');
            $table->datetime('date_vente');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('magasin_id')->references('id')->on('magasins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};
