<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            MenuSeeder::class,
        ]);

        $superadminUser = User::factory()->create([
            'name' => 'Superadmin',
            'email' => 'superadmin@example.com',
        ]);
        $superadminUser->assignRole('superadmin');
    }
}
