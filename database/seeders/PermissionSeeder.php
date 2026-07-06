<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
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

            'master-data.specialization.view',
            'master-data.specialization.create',
            'master-data.specialization.edit',
            'master-data.specialization.delete',

            'master-data.employee.view',
            'master-data.employee.create',
            'master-data.employee.edit',
            'master-data.employee.delete',

            'master-data.patient.view',
            'master-data.patient.create',
            'master-data.patient.edit',
            'master-data.patient.delete',

            'master-data.drug.view',
            'master-data.drug.create',
            'master-data.drug.edit',
            'master-data.drug.delete',

            'master-data.tariff.view',
            'master-data.tariff.create',
            'master-data.tariff.edit',
            'master-data.tariff.delete',

            'master-data.lab-test.view',
            'master-data.lab-test.create',
            'master-data.lab-test.edit',
            'master-data.lab-test.delete',

            'master-data.doctor-tariff.view',
            'master-data.doctor-tariff.create',
            'master-data.doctor-tariff.edit',
            'master-data.doctor-tariff.delete',

            'master-data.procedure-tariff.view',
            'master-data.procedure-tariff.create',
            'master-data.procedure-tariff.edit',
            'master-data.procedure-tariff.delete',

            'registration.queue.view',
            'registration.queue.create',
            'registration.queue.check-in',
            'registration.queue.call',
            'registration.queue.skip',
            'registration.queue.done',
            'registration.vitals.view',
            'registration.vitals.create',
            'registration.vitals.edit',

            'display-antrian.view',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }
}
