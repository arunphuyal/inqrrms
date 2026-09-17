<?php

use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Registers the Cash Register, Hotel, Inventory and Kitchens modules
     * and their permissions (via the same seeders used on fresh installs,
     * both idempotent), then retroactively grants the new permissions to
     * every existing restaurant's Admin and Branch Head roles - mirroring
     * what RoleSeeder already does automatically for restaurants created
     * from now on.
     *
     * These new modules are NOT auto-attached to any Package: a superadmin
     * decides which packages/restaurants get them via the existing Package
     * editor (package_modules), same as any other module.
     */
    public function up(): void
    {
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\ModuleSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\PermissionSeeder', '--force' => true]);

        $newModuleNames = ['Cash Register', 'Hotel', 'Inventory', 'Kitchens'];
        $newModuleIds = Module::whereIn('name', $newModuleNames)->where('is_superadmin', 0)->pluck('id');

        $newPermissionNames = Permission::whereIn('module_id', $newModuleIds)->pluck('name')->toArray();

        if (empty($newPermissionNames)) {
            return;
        }

        Role::whereIn('display_name', ['Admin', 'Branch Head'])
            ->whereNotNull('restaurant_id')
            ->get()
            ->each(function (Role $role) use ($newPermissionNames) {
                $role->givePermissionTo($newPermissionNames);
            });

        // Clear cached permissions so users with these roles see the change
        // without needing to log out.
        User::whereHas('roles', function ($query) {
            $query->whereIn('display_name', ['Admin', 'Branch Head']);
        })->pluck('id')->each(function ($userId) {
            cache()->forget('role_permissions_' . $userId);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $moduleNames = ['Cash Register', 'Hotel', 'Inventory', 'Kitchens'];
        $moduleIds = Module::whereIn('name', $moduleNames)->where('is_superadmin', 0)->pluck('id');

        Permission::whereIn('module_id', $moduleIds)->delete();
        Module::whereIn('name', $moduleNames)->where('is_superadmin', 0)->delete();
    }
};
