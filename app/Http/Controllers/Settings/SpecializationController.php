<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreSpecializationRequest;
use App\Http\Requests\Settings\UpdateSpecializationRequest;
use App\Models\Specialization;

class SpecializationController extends Controller
{
    public function index()
    {
        $specializations = Specialization::orderBy('name')->get();

        return view('settings.specializations.index', compact('specializations'));
    }

    public function create()
    {
        return view('settings.specializations.create');
    }

    public function store(StoreSpecializationRequest $request)
    {
        Specialization::create($request->validated());

        return redirect()->route('settings.specializations.index')->with('success', 'Specialization created successfully.');
    }

    public function edit(Specialization $specialization)
    {
        return view('settings.specializations.edit', compact('specialization'));
    }

    public function update(UpdateSpecializationRequest $request, Specialization $specialization)
    {
        $specialization->update($request->validated());

        return redirect()->route('settings.specializations.index')->with('success', 'Specialization updated successfully.');
    }

    public function destroy(Specialization $specialization)
    {
        $specialization->delete();

        return redirect()->route('settings.specializations.index')->with('success', 'Specialization deleted successfully.');
    }
}
