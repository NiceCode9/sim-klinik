<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\VisitVital;

class VitalSignController extends Controller
{
    public function create(Visit $visit)
    {
        return view('registration.vitals.create', compact('visit'));
    }

    public function store(Visit $visit)
    {
        $data = request()->validate([
            'blood_pressure' => 'nullable|string|max:20',
            'pulse' => 'nullable|string|max:20',
            'temperature' => 'nullable|string|max:20',
            'respiration_rate' => 'nullable|string|max:20',
            'height_cm' => 'nullable|numeric|min:0|max:300',
            'weight_kg' => 'nullable|numeric|min:0|max:500',
            'chief_complaint' => 'nullable|string',
        ]);

        $data['employee_id'] = auth()->id();
        $data['visit_id'] = $visit->id;
        $data['recorded_at'] = now();

        VisitVital::create($data);

        $visit->update(['status' => 'waiting_doctor']);

        return redirect()->route('registration.queue.index')
            ->with('success', 'Vital signs recorded successfully.');
    }

    public function edit(Visit $visit)
    {
        $vital = $visit->vitalSigns;

        if (! $vital) {
            return redirect()->route('registration.vitals.create', $visit);
        }

        return view('registration.vitals.edit', compact('visit', 'vital'));
    }

    public function update(Visit $visit)
    {
        $data = request()->validate([
            'blood_pressure' => 'nullable|string|max:20',
            'pulse' => 'nullable|string|max:20',
            'temperature' => 'nullable|string|max:20',
            'respiration_rate' => 'nullable|string|max:20',
            'height_cm' => 'nullable|numeric|min:0|max:300',
            'weight_kg' => 'nullable|numeric|min:0|max:500',
            'chief_complaint' => 'nullable|string',
        ]);

        $data['employee_id'] = auth()->id();
        $data['recorded_at'] = now();

        $visit->vitalSigns->update($data);

        return redirect()->route('registration.queue.index')
            ->with('success', 'Vital signs updated successfully.');
    }
}
