<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('needs', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_token', 8)->unique();
            $table->string('applicant_name');
            $table->string('applicant_phone');
            $table->text('applicant_address');
            $table->string('applicant_email')->nullable();
            $table->string('title');
            $table->text('description');
            $table->enum('category', ['health', 'education', 'business', 'basic_needs', 'other']);
            $table->decimal('amount_requested', 15, 2);
            $table->string('attachment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'funded'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->decimal('amount_approved', 15, 2)->nullable();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('needs');
    }
};
