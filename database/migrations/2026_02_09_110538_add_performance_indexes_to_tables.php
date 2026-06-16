<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users table - Add indexes if they don't exist
        $this->addIndexIfNotExists('users', 'is_active');
        $this->addIndexIfNotExists('users', 'last_login_at');

        // Campaigns table - Additional indexes
        $this->addIndexIfNotExists('campaigns', 'status');
        $this->addIndexIfNotExists('campaigns', 'type');
        $this->addIndexIfNotExists('campaigns', 'created_at');
        $this->addCompositeIndexIfNotExists('campaigns', ['status', 'created_at'], 'campaigns_status_created_idx');
        $this->addCompositeIndexIfNotExists('campaigns', ['is_featured', 'status', 'created_at'], 'campaigns_featured_status_created_idx');

        // Donations table - Additional indexes
        $this->addIndexIfNotExists('donations', 'status');
        $this->addIndexIfNotExists('donations', 'donation_type');
        $this->addIndexIfNotExists('donations', 'payment_method');
        $this->addIndexIfNotExists('donations', 'verified_at');
        $this->addIndexIfNotExists('donations', 'created_at');
        $this->addCompositeIndexIfNotExists('donations', ['status', 'created_at'], 'donations_status_created_idx');
        $this->addCompositeIndexIfNotExists('donations', ['verified_at', 'status'], 'donations_verified_status_idx');

        // Blog posts table - Additional indexes
        $this->addIndexIfNotExists('blog_posts', 'status');
        $this->addIndexIfNotExists('blog_posts', 'author_id');
        $this->addIndexIfNotExists('blog_posts', 'created_at');

        // Muzakkis table - Index for search
        $this->addIndexIfNotExists('muzakkis', 'email');
        $this->addIndexIfNotExists('muzakkis', 'phone');
        $this->addIndexIfNotExists('muzakkis', 'created_at');

        // Mustahiks table - Index for filtering
        $this->addIndexIfNotExists('mustahiks', 'asnaf_type');
        $this->addIndexIfNotExists('mustahiks', 'created_at');

        // Distributors table - Index for filtering
        $this->addIndexIfNotExists('distributors', 'is_active');
        $this->addIndexIfNotExists('distributors', 'created_at');

        // Withdrawals table - Index for status filtering
        $this->addIndexIfNotExists('withdrawals', 'status');
        $this->addIndexIfNotExists('withdrawals', 'created_at');
        $this->addCompositeIndexIfNotExists('withdrawals', ['status', 'created_at'], 'withdrawals_status_created_idx');

        // Settings table - Index for key lookup
        $this->addIndexIfNotExists('settings', 'key');

        // Banners table - Index for active banners
        $this->addIndexIfNotExists('banners', 'is_active');
        $this->addIndexIfNotExists('banners', 'order');
        $this->addCompositeIndexIfNotExists('banners', ['is_active', 'order'], 'banners_active_order_idx');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        $this->dropIndexIfExists('users', 'users_is_active_index');
        $this->dropIndexIfExists('users', 'users_last_login_at_index');

        $this->dropIndexIfExists('campaigns', 'campaigns_status_index');
        $this->dropIndexIfExists('campaigns', 'campaigns_type_index');
        $this->dropIndexIfExists('campaigns', 'campaigns_created_at_index');
        $this->dropIndexIfExists('campaigns', 'campaigns_status_created_idx');
        $this->dropIndexIfExists('campaigns', 'campaigns_featured_status_created_idx');

        $this->dropIndexIfExists('donations', 'donations_status_index');
        $this->dropIndexIfExists('donations', 'donations_donation_type_index');
        $this->dropIndexIfExists('donations', 'donations_payment_method_index');
        $this->dropIndexIfExists('donations', 'donations_verified_at_index');
        $this->dropIndexIfExists('donations', 'donations_created_at_index');
        $this->dropIndexIfExists('donations', 'donations_status_created_idx');
        $this->dropIndexIfExists('donations', 'donations_verified_status_idx');

        $this->dropIndexIfExists('blog_posts', 'blog_posts_status_index');
        $this->dropIndexIfExists('blog_posts', 'blog_posts_author_id_index');
        $this->dropIndexIfExists('blog_posts', 'blog_posts_created_at_index');

        $this->dropIndexIfExists('muzakkis', 'muzakkis_email_index');
        $this->dropIndexIfExists('muzakkis', 'muzakkis_phone_index');
        $this->dropIndexIfExists('muzakkis', 'muzakkis_created_at_index');

        $this->dropIndexIfExists('mustahiks', 'mustahiks_asnaf_type_index');
        $this->dropIndexIfExists('mustahiks', 'mustahiks_created_at_index');

        $this->dropIndexIfExists('distributors', 'distributors_is_active_index');
        $this->dropIndexIfExists('distributors', 'distributors_created_at_index');

        $this->dropIndexIfExists('withdrawals', 'withdrawals_status_index');
        $this->dropIndexIfExists('withdrawals', 'withdrawals_created_at_index');
        $this->dropIndexIfExists('withdrawals', 'withdrawals_status_created_idx');

        $this->dropIndexIfExists('settings', 'settings_key_index');

        $this->dropIndexIfExists('banners', 'banners_is_active_index');
        $this->dropIndexIfExists('banners', 'banners_order_index');
        $this->dropIndexIfExists('banners', 'banners_active_order_idx');
    }

    /**
     * Add index if it doesn't exist.
     */
    private function addIndexIfNotExists(string $table, string $column): void
    {
        $indexName = "{$table}_{$column}_index";

        if (! $this->indexExists($table, $indexName)) {
            try {
                Schema::table($table, function ($t) use ($column) {
                    $t->index($column);
                });
            } catch (\Exception $e) {
                // Silently skip if index already exists (for SQLite)
                if (! str_contains($e->getMessage(), 'already exists')) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Add composite index if it doesn't exist.
     */
    private function addCompositeIndexIfNotExists(string $table, array $columns, string $indexName): void
    {
        if (! $this->indexExists($table, $indexName)) {
            try {
                Schema::table($table, function ($t) use ($columns, $indexName) {
                    $t->index($columns, $indexName);
                });
            } catch (\Exception $e) {
                // Silently skip if index already exists (for SQLite)
                if (! str_contains($e->getMessage(), 'already exists')) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Drop index if it exists.
     */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            Schema::table($table, function ($t) use ($indexName) {
                $t->dropIndex($indexName);
            });
        }
    }

    /**
     * Check if index exists.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();

        /** @var \Illuminate\Database\MySqlConnection|\Illuminate\Database\PostgresConnection|\Illuminate\Database\SQLiteConnection $connection */
        $driver = $connection->getDriverName();

        // Skip index check for SQLite (used in tests)
        if ($driver === 'sqlite') {
            return false; // Always try to create indexes in SQLite
        }

        if ($driver === 'pgsql') {
            $result = DB::select(
                'SELECT count(*) as count FROM pg_indexes
                 WHERE schemaname = \'public\' AND tablename = ? AND indexname = ?',
                [$table, $indexName]
            );

            return $result[0]->count > 0;
        }

        $database = $connection->getDatabaseName();

        $result = DB::select(
            'SELECT COUNT(*) as count FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $indexName]
        );

        return $result[0]->count > 0;
    }
};
