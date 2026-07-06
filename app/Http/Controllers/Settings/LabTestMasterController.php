<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreLabTestMasterRequest;
use App\Http\Requests\Settings\UpdateLabTestMasterRequest;
use App\Models\LabTestMaster;
use App\Models\Tariff;

class LabTestMasterController extends Controller
{
    public function index()
    {
        $labTests = LabTestMaster::with('tariff')->orderBy('name')->get();

        return view('settings.lab-test-masters.index', compact('labTests'));
    }

    public function create()
    {
        $tariffs = Tariff::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('settings.lab-test-masters.create', compact('tariffs'));
    }

    public function store(StoreLabTestMasterRequest $request)
    {
        LabTestMaster::create($request->validated());

        return redirect()->route('settings.lab-test-masters.index')->with('success', 'Lab test created successfully.');
    }

    public function edit(LabTestMaster $labTestMaster)
    {
        $tariffs = Tariff::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('settings.lab-test-masters.edit', compact('labTestMaster', 'tariffs'));
    }

    public function update(UpdateLabTestMasterRequest $request, LabTestMaster $labTestMaster)
    {
        $labTestMaster->update($request->validated());

        return redirect()->route('settings.lab-test-masters.index')->with('success', 'Lab test updated successfully.');
    }

    public function destroy(LabTestMaster $labTestMaster)
    {
        $labTestMaster->delete();

        return redirect()->route('settings.lab-test-masters.index')->with('success', 'Lab test deleted successfully.');
    }
}
