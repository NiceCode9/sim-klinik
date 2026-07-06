<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::orderBy('name')->get();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(StorePatientRequest $request)
    {
        $validated = $request->validated();
        $validated['medical_record_number'] = 'RM-'.date('Ymd').'-'.Str::upper(Str::random(6));

        Patient::create($validated);

        return redirect()->route('patients.index')->with('success', 'Patient created successfully.');
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $patient->update($request->validated());

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }
}
