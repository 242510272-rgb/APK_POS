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
        Schema::table('item_penjualan', function (Blueprint $table) {
            // Tambahkan kolom penjualan_id
            $table->unsignedBigInteger('penjualan_id')->nullable()->after('id');
            
            // Tambahkan foreign key constraint
            $table->foreign('penjualan_id')
                  ->references('id')
                  ->on('penjualan')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_penjualan', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['penjualan_id']);
            
            // Hapus kolom
            $table->dropColumn('penjualan_id');
        });
    }
};