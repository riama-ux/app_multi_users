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
        Schema::create('transferts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('magasin_source_id');
            $table->unsignedBigInteger('magasin_destination_id');
            $table->unsignedBigInteger('user_id');
            $table->datetime('date_transfert');
            $table->timestamps();

            $table->foreign('magasin_source_id')->references('id')->on('magasins');
            $table->foreign('magasin_destination_id')->references('id')->on('magasins');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferts');
    }
};
