<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreDoctorTariffRequest;
use App\Http\Requests\Settings\UpdateDoctorTariffRequest;
use App\Models\DoctorTariff;
use App\Models\Employee;
use App\Models\Specialization;

class DoctorTariffController extends Controller
{
    public function index()
    {
        $doctorTariffs = DoctorTariff::with(['employee', 'specialization'])->orderBy('id')->get();

        return view('settings.doctor-tariffs.index', compact('doctorTariffs'));
    }

    public function create()
    {
        $doctors = Employee::where('employee_type', 'dokter')->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $specializations = Specialization::orderBy('name')->get(['id', 'name']);

        return view('settings.doctor-tariffs.create', compact('doctors', 'specializations'));
    }

    public function store(StoreDoctorTariffRequest $request)
    {
        DoctorTariff::create($request->validated());

        return redirect()->route('settings.doctor-tariffs.index')->with('success', 'Doctor tariff created successfully.');
    }

    public function edit(DoctorTariff $doctorTariff)
    {
        $doctors = Employee::where('employee_type', 'dokter')->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $specializations = Specialization::orderBy('name')->get(['id', 'name']);

        return view('settings.doctor-tariffs.edit', compact('doctorTariff', 'doctors', 'specializations'));
    }

    public function update(UpdateDoctorTariffRequest $request, DoctorTariff $doctorTariff)
    {
        $doctorTariff->update($request->validated());

        return redirect()->route('settings.doctor-tariffs.index')->with('success', 'Doctor tariff updated successfully.');
    }

    public function destroy(DoctorTariff $doctorTariff)
    {
        $doctorTariff->delete();

        return redirect()->route('settings.doctor-tariffs.index')->with('success', 'Doctor tariff deleted successfully.');
    }
}
