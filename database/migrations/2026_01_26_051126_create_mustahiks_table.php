<?php

declare(strict_types=1);

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
        Schema::create('mustahiks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone')->nullable()->index();
            $table->string('asnaf_type')->index(); // fakir, miskin, etc
            $table->string('nik', 16)->nullable()->index();
            $table->string('family_card_number', 16)->nullable()->index();
            $table->string('occupation')->nullable();
            $table->string('income_range')->nullable(); // info only
            $table->string('resides_at')->nullable(); // owned, rented, etc
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mustahiks');
    }
};
