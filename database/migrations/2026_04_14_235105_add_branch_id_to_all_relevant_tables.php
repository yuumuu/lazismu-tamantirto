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
        $tables = [
            'users',
            'campaign_categories',
            'campaigns',
            'campaign_images',
            'donations',
            'payment_logs',
            'donation_notifications',
            'pages',
            'blog_posts',
            'organization_structures',
            'team_members',
            'media_library',
            'menus',
            'settings',
            'audit_logs',
            'muzakkis',
            'mustahiks',
            'distributors',
            'withdrawals',
            'banners',
            'financial_reports',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('branch_id')
                    ->nullable()
                    ->after('id')
                    ->index()
                    ->constrained('branches')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'campaign_categories',
            'campaigns',
            'campaign_images',
            'donations',
            'payment_logs',
            'donation_notifications',
            'pages',
            'blog_posts',
            'organization_structures',
            'team_members',
            'media_library',
            'menus',
            'settings',
            'audit_logs',
            'muzakkis',
            'mustahiks',
            'distributors',
            'withdrawals',
            'banners',
            'financial_reports',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('branch_id');
            });
        }
    }
};
