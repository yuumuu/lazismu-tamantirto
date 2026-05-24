<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that have a masjid_id foreign key column.
     *
     * @var array<int, string>
     */
    private array $tablesWithMasjidId = [
        'users',
        'campaign_categories',
        'campaigns',
        'campaign_images',
        'donations',
        'payment_logs',
        'donation_notifications',
        'pages',
        'blog_posts',
        'blog_categories',
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

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop all foreign keys referencing masjids table first
        foreach ($this->tablesWithMasjidId as $tableName) {
            if (Schema::hasColumn($tableName, 'masjid_id')) {
                $foreignKeys = Schema::getForeignKeys($tableName);
                foreach ($foreignKeys as $fk) {
                    if (in_array('masjid_id', $fk['columns'])) {
                        Schema::table($tableName, function (Blueprint $table) use ($fk) {
                            $table->dropForeign($fk['name']);
                        });
                    }
                }
            }
        }

        // 2. Drop unique constraint on settings that references masjid_id
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['masjid_id', 'key']);
        });

        // 3. Rename the masjids table to branches
        Schema::rename('masjids', 'branches');

        // 4. Add type column to branches table
        Schema::table('branches', function (Blueprint $table) {
            $table->string('type', 50)->default('masjid')->after('slug');
        });

        // 5. Rename masjid_id to branch_id in all tables and re-add foreign key
        foreach ($this->tablesWithMasjidId as $tableName) {
            if (Schema::hasColumn($tableName, 'masjid_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->renameColumn('masjid_id', 'branch_id');
                });

                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreign('branch_id')
                        ->references('id')
                        ->on('branches')
                        ->onDelete('cascade');
                });
            }
        }

        // 6. Re-add unique constraint on settings with new column name
        Schema::table('settings', function (Blueprint $table) {
            $table->unique(['branch_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop all foreign keys referencing branches table
        foreach ($this->tablesWithMasjidId as $tableName) {
            if (Schema::hasColumn($tableName, 'branch_id')) {
                $foreignKeys = Schema::getForeignKeys($tableName);
                foreach ($foreignKeys as $fk) {
                    if (in_array('branch_id', $fk['columns'])) {
                        Schema::table($tableName, function (Blueprint $table) use ($fk) {
                            $table->dropForeign($fk['name']);
                        });
                    }
                }
            }
        }

        // 2. Drop unique constraint on settings
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['branch_id', 'key']);
        });

        // 3. Rename branch_id back to masjid_id in all tables
        foreach ($this->tablesWithMasjidId as $tableName) {
            if (Schema::hasColumn($tableName, 'branch_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->renameColumn('branch_id', 'masjid_id');
                });

                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreign('masjid_id')
                        ->references('id')
                        ->on('masjids')
                        ->onDelete('cascade');
                });
            }
        }

        // 4. Drop type column from branches
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        // 5. Rename branches back to masjids
        Schema::rename('branches', 'masjids');

        // 6. Re-add unique constraint on settings
        Schema::table('settings', function (Blueprint $table) {
            $table->unique(['masjid_id', 'key']);
        });
    }
};
