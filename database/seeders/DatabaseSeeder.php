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
            SpecializationSeeder::class,
            MenuSeeder::class,
        ]);

        $superadminUser = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Superadmin',
                'password' => bcrypt('password'),
            ]
        );
        $superadminUser->assignRole('superadmin');
    }
}
