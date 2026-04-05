<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->foreignId('lapangan_id')->constrained()->cascadeOnDelete();

            $table->date('tanggal_main');
            $table->time('jam_mulai');
            $table->time('jam_selesai');

            $table->integer('total_harga');
            $table->enum('status', ['pending', 'dibayar', 'batal', 'selesai'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
