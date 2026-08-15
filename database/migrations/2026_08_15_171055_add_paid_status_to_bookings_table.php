<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah status 'paid' ke enum bookings.
     *
     * Alur: pending (booking dibuat, belum bayar) -> paid (pembayaran sukses,
     * menunggu konfirmasi barber di Booking Masuk) -> accepted (barber klik
     * Terima) -> waiting/serving/completed.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'accepted', 'waiting', 'late', 'serving', 'completed', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'waiting', 'late', 'serving', 'completed', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }
};