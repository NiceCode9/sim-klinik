<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreDrugRequest;
use App\Http\Requests\Settings\UpdateDrugRequest;
use App\Models\Drug;

class DrugController extends Controller
{
    public function index()
    {
        $drugs = Drug::orderBy('name')->get();

        return view('settings.drugs.index', compact('drugs'));
    }

    public function create()
    {
        return view('settings.drugs.create');
    }

    public function store(StoreDrugRequest $request)
    {
        Drug::create($request->validated());

        return redirect()->route('settings.drugs.index')->with('success', 'Drug created successfully.');
    }

    public function edit(Drug $drug)
    {
        return view('settings.drugs.edit', compact('drug'));
    }

    public function update(UpdateDrugRequest $request, Drug $drug)
    {
        $drug->update($request->validated());

        return redirect()->route('settings.drugs.index')->with('success', 'Drug updated successfully.');
    }

    public function destroy(Drug $drug)
    {
        $drug->delete();

        return redirect()->route('settings.drugs.index')->with('success', 'Drug deleted successfully.');
    }
}
