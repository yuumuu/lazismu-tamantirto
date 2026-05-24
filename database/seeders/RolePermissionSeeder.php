<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * All permissions grouped by resource.
     */
    private array $permissions = [
        // Campaign permissions
        'campaign.view_any',
        'campaign.view',
        'campaign.create',
        'campaign.update',
        'campaign.delete',
        'campaign.restore',
        'campaign.force_delete',
        'campaign.publish',

        // Donation permissions
        'donation.view_any',
        'donation.view',
        'donation.verify',
        'donation.reject',
        'donation.export',
        'donation.view_sensitive_data',
        'donation.send_notification',
        'donation.manual_create',
        'donation.refund',
        'donation.view_reports',

        // Blog permissions
        'blog.view_any',
        'blog.view',
        'blog.create',
        'blog.update',
        'blog.delete',
        'blog.restore',
        'blog.force_delete',
        'blog.publish',

        // Page permissions
        'page.view_any',
        'page.view',
        'page.create',
        'page.update',
        'page.delete',
        'page.publish',

        // Organization permissions
        'organization.manage_structure',
        'organization.manage_members',
        'organization.view_hierarchy',
        'organization.export',

        // Media permissions
        'media.upload',
        'media.delete',
        'media.organize',

        // System permissions
        'system.manage_settings',
        'system.manage_menus',
        'system.view_audit_logs',
        'system.manage_users',
        'system.manage_roles',
        'system.backup_database',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->createPermissions();
        $this->createRoles();
    }

    private function createPermissions(): void
    {
        foreach ($this->permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('Created '.count($this->permissions).' permissions.');
    }

    private function createRoles(): void
    {
        // Super Admin - all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());
        $this->command->info('Created Super Admin role with ALL permissions.');

        // Admin - most permissions except some dangerous ones
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $adminPermissions = collect($this->permissions)
            ->filter(fn ($p) => ! in_array($p, [
                'donation.refund',
                'system.manage_roles',
                'system.backup_database',
            ]))
            ->toArray();
        $admin->givePermissionTo($adminPermissions);
        $this->command->info('Created Admin role with '.count($adminPermissions).' permissions.');

        // Editor - content management permissions
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editorPermissions = [
            'campaign.view_any',
            'campaign.view',
            'campaign.create',
            'campaign.update',
            'donation.view_any',
            'donation.view',
            'donation.verify',
            'blog.view_any',
            'blog.view',
            'blog.create',
            'blog.update',
            'page.view_any',
            'page.view',
            'page.create',
            'page.update',
            'organization.view_hierarchy',
            'media.upload',
            'media.organize',
        ];
        $editor->givePermissionTo($editorPermissions);
        $this->command->info('Created Editor role with '.count($editorPermissions).' permissions.');

        // Viewer - read-only permissions
        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewerPermissions = [
            'campaign.view_any',
            'campaign.view',
            'donation.view_any',
            'donation.view',
            'blog.view_any',
            'blog.view',
            'page.view_any',
            'page.view',
            'organization.view_hierarchy',
        ];
        $viewer->givePermissionTo($viewerPermissions);
        $this->command->info('Created Viewer role with '.count($viewerPermissions).' permissions.');
    }

    private function createDefaultSuperAdmin(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@lazismu.org'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('SuperAdmin123!'),
                'email_verified_at' => now(),
                'is_active' => true,
                'masjid_id' => 1,
            ]
        );

        $user->assignRole('super_admin');

        $this->command->info('Created default Super Admin: admin@lazismu.org');
    }
}
