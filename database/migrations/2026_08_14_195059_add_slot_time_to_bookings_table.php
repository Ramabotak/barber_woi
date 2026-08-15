<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simpan jam slot 30-menit yang dipilih customer (mis. 09:00) pada booking.
     * Schedule tetap menyimpan rentang hari (start_time - end_time), slot
     * spesifik disimpan di sini supaya dua customer tidak bisa booking
     * slot yang sama.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->time('slot_time')->nullable()->after('schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('slot_time');
        });
    }
};
