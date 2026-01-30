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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->string('transaction_id', 20)->unique();
            $table->string('donor_name', 100);
            $table->string('donor_email', 255);
            $table->string('donor_phone', 20);
            $table->decimal('amount', 15, 2);
            $table->string('donation_type', 20)->default('infaq');
            $table->string('payment_method', 20);
            $table->string('bank_name', 50)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('status', 20)->default('pending');
            $table->string('proof_image', 255)->nullable();
            $table->text('donor_message')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->decimal('admin_fee', 15, 2)->default(0);
            $table->boolean('is_suspicious')->default(false);
            $table->text('suspicious_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('verification_notes')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
            $table->index(['donor_email', 'created_at']);
            $table->index(['status', 'is_suspicious']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
