<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view',
            'settings.menu.view',
            'settings.menu.create',
            'settings.menu.edit',
            'settings.menu.delete',
            'settings.role.view',
            'settings.role.create',
            'settings.role.edit',
            'settings.role.delete',
            'settings.permission.view',
            'settings.permission.assign',
            'activity-log.view',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }
}
