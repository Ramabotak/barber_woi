<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('expense_date');
            $table->enum('category', [
                'gaji_komisi', 'produk_stok', 'operasional', 'sewa',
                'perawatan_alat', 'marketing', 'lainnya',
            ]);
            $table->string('description', 255);
            $table->decimal('amount', 14, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'qris', 'ewallet', 'other'])->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('receipt_path')->nullable();
            $table->text('rejection_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('payroll_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['expense_date', 'status']);
            $table->index(['category', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
