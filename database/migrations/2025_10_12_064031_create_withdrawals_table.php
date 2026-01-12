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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2); // Jumlah yang diminta
            $table->decimal('commission', 15, 2)->default(0); // Komisi 2%
            $table->decimal('net_amount', 15, 2); // Jumlah setelah potong komisi

            // Bank details
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_holder_name');

            // Status & workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('notes')->nullable(); // Catatan admin
            $table->string('proof_of_transfer')->nullable(); // Upload bukti transfer

            // Timestamps for tracking
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Track who approved
            $table->foreignId('approved_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
