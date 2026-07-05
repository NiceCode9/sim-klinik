<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $superadminRole = Role::firstOrCreate(['guard_name' => 'web', 'name' => 'superadmin']);
        Role::firstOrCreate(['guard_name' => 'web', 'name' => 'resepsionis']);
        Role::firstOrCreate(['guard_name' => 'web', 'name' => 'perawat']);
        Role::firstOrCreate(['guard_name' => 'web', 'name' => 'dokter']);
        Role::firstOrCreate(['guard_name' => 'web', 'name' => 'apoteker']);
        Role::firstOrCreate(['guard_name' => 'web', 'name' => 'kasir']);

        $allPermissions = Permission::all();
        $superadminRole->syncPermissions($allPermissions);
    }
}
