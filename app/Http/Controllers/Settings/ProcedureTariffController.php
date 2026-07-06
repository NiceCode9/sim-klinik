<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreProcedureTariffRequest;
use App\Http\Requests\Settings\UpdateProcedureTariffRequest;
use App\Models\Icd9cmCode;
use App\Models\ProcedureTariff;

class ProcedureTariffController extends Controller
{
    public function index()
    {
        $procedureTariffs = ProcedureTariff::with('icd9cmCode')->orderBy('name')->get();

        return view('settings.procedure-tariffs.index', compact('procedureTariffs'));
    }

    public function create()
    {
        $icd9cmCodes = Icd9cmCode::orderBy('code')->get(['id', 'code', 'description']);

        return view('settings.procedure-tariffs.create', compact('icd9cmCodes'));
    }

    public function store(StoreProcedureTariffRequest $request)
    {
        ProcedureTariff::create($request->validated());

        return redirect()->route('settings.procedure-tariffs.index')->with('success', 'Procedure tariff created successfully.');
    }

    public function edit(ProcedureTariff $procedureTariff)
    {
        $icd9cmCodes = Icd9cmCode::orderBy('code')->get(['id', 'code', 'description']);

        return view('settings.procedure-tariffs.edit', compact('procedureTariff', 'icd9cmCodes'));
    }

    public function update(UpdateProcedureTariffRequest $request, ProcedureTariff $procedureTariff)
    {
        $procedureTariff->update($request->validated());

        return redirect()->route('settings.procedure-tariffs.index')->with('success', 'Procedure tariff updated successfully.');
    }

    public function destroy(ProcedureTariff $procedureTariff)
    {
        $procedureTariff->delete();

        return redirect()->route('settings.procedure-tariffs.index')->with('success', 'Procedure tariff deleted successfully.');
    }
}
