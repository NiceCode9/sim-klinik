<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $specializations = [
            'Penyakit Dalam',
            'Saraf',
            'Gigi',
            'Radiologi',
            'Laboratorium',
            'Umum',
        ];

        foreach ($specializations as $name) {
            Specialization::firstOrCreate(['name' => $name]);
        }
    }
}
