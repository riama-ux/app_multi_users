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
        Schema::create('retour_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->nullable()->constrained('ventes')->onDelete('set null'); // Peut être lié à une vente ou non
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('magasin_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('date_retour');
            $table->decimal('montant_rembourse', 10, 0)->default(0);
            $table->text('motif_global')->nullable();
            $table->string('statut')->default('en_attente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retour_clients');
    }
};
