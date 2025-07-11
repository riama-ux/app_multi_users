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
        Schema::create('ajustements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magasin_id')->constrained('magasins')->onDelete('cascade');
            $table->enum('type', ['entree', 'sortie']); // 'entree' ou 'sortie' comme dans le contrôleur
            $table->string('motif_global')->nullable(); // Motif général de l'ajustement
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('date_ajustement');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajustements');
    }
};
