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
        Schema::table('ligne_transferts', function (Blueprint $table) {
            $table->unsignedBigInteger('lot_id')->nullable()->after('quantite');
            $table->foreign('lot_id')->references('id')->on('stock_lots');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ligne_transferts', function (Blueprint $table) {
            $table->dropForeign(['lot_id']);
            $table->dropColumn('lot_id');
        });
    }
};
