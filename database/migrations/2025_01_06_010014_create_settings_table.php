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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 50); // text, number, boolean, json, image
            $table->string('group_name', 100); // general, contact, social, appearance, donation
            $table->string('label', 255);
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Show in frontend
            $table->timestamps();

            $table->index(['group_name', 'key']);
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
