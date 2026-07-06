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

        Menu::firstOrCreate(
            ['name' => 'Patients'],
            [
                'icon' => 'bi bi-people',
                'route_name' => 'patients.index',
                'permission_name' => 'master-data.patient.view',
                'order' => 2,
                'is_active' => true,
            ]
        );

        $registrationMenu = Menu::firstOrCreate(
            ['name' => 'Registration'],
            [
                'icon' => 'bi bi-calendar-check',
                'route_name' => 'registration.queue.index',
                'permission_name' => 'registration.queue.view',
                'order' => 3,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Display Antrian', 'parent_id' => $registrationMenu->id],
            [
                'icon' => 'bi bi-tv',
                'route_name' => 'display-antrian.index',
                'permission_name' => 'display-antrian.view',
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
            ['name' => 'Specializations', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-tags',
                'route_name' => 'settings.specializations.index',
                'permission_name' => 'master-data.specialization.view',
                'order' => 1,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Employees', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-person-badge',
                'route_name' => 'settings.employees.index',
                'permission_name' => 'master-data.employee.view',
                'order' => 2,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Drugs', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-capsule',
                'route_name' => 'settings.drugs.index',
                'permission_name' => 'master-data.drug.view',
                'order' => 3,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Tariffs', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-currency-dollar',
                'route_name' => 'settings.tariffs.index',
                'permission_name' => 'master-data.tariff.view',
                'order' => 4,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Lab Tests', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-flask',
                'route_name' => 'settings.lab-test-masters.index',
                'permission_name' => 'master-data.lab-test.view',
                'order' => 5,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Doctor Tariffs', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-file-medical',
                'route_name' => 'settings.doctor-tariffs.index',
                'permission_name' => 'master-data.doctor-tariff.view',
                'order' => 6,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Procedure Tariffs', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-clipboard-plus',
                'route_name' => 'settings.procedure-tariffs.index',
                'permission_name' => 'master-data.procedure-tariff.view',
                'order' => 7,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Menus', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-list',
                'route_name' => 'settings.menus.index',
                'permission_name' => 'settings.menu.view',
                'order' => 8,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Roles', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-shield-check',
                'route_name' => 'settings.roles.index',
                'permission_name' => 'settings.role.view',
                'order' => 9,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'User Permissions', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-person-lock',
                'route_name' => 'settings.user-permissions.index',
                'permission_name' => 'settings.permission.view',
                'order' => 10,
                'is_active' => true,
            ]
        );

        Menu::firstOrCreate(
            ['name' => 'Activity Log', 'parent_id' => $settingsMenu->id],
            [
                'icon' => 'bi bi-clock-history',
                'route_name' => 'activity-log.index',
                'permission_name' => 'activity-log.view',
                'order' => 11,
                'is_active' => true,
            ]
        );
    }
}
