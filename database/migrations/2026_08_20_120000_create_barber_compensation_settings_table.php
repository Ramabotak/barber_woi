<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barber_compensation_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barber_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('fixed_salary', 14, 2)->default(0);
            $table->enum('commission_type', ['none', 'per_booking', 'percentage'])->default('none');
            $table->decimal('commission_value', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barber_compensation_settings');
    }
};
