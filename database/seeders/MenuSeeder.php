<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::firstOrCreate(
            ['name' => 'Dashboard'],
            [
                'icon' => 'bi bi-speedometer',
                'route_name' => 'dashboard',
                'permission_name' => 'dashboard.view',
                'order' => 1,
                'is_active' => true,
            ]
        );

        $settingsMenu = Menu::firstOrCreate(
            ['name' => 'Settings'],
            [
                'icon' => 'bi bi-gear',
                'route_name' => null,
                'permission_name' => null,
                'order' => 999,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Menus', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-list',
                'route_name' => 'settings.menus.index',
                'permission_name' => 'settings.menu.view',
                'order' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Roles', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-shield-check',
                'route_name' => 'settings.roles.index',
                'permission_name' => 'settings.role.view',
                'order' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'User Permissions', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-person-lock',
                'route_name' => 'settings.user-permissions.index',
                'permission_name' => 'settings.permission.view',
                'order' => 3,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Activity Log', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-clock-history',
                'route_name' => 'activity-log.index',
                'permission_name' => 'activity-log.view',
                'order' => 4,
                'is_active' => true,
            ]
        );
    }
}
