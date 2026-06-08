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
    Schema::table('lapangans', function (Blueprint $table) {
        $table->unique(['nama_lapangan', 'mitra_id']);
    });
}

public function down(): void
{
    Schema::table('lapangans', function (Blueprint $table) {
        $table->dropUnique(['lapangans_nama_lapangan_mitra_id_unique']);
    });

    }
};