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
        Schema::table('blog_categories', function (Blueprint $table) {
            $table->foreignId('masjid_id')
                ->nullable()
                ->after('id')
                ->index()
                ->constrained('masjids')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_categories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('masjid_id');
        });
    }
};
