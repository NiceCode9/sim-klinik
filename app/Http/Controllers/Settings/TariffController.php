<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreTariffRequest;
use App\Http\Requests\Settings\UpdateTariffRequest;
use App\Models\Tariff;

class TariffController extends Controller
{
    public function index()
    {
        $tariffs = Tariff::orderBy('tariff_type')->orderBy('name')->get();

        return view('settings.tariffs.index', compact('tariffs'));
    }

    public function create()
    {
        return view('settings.tariffs.create');
    }

    public function store(StoreTariffRequest $request)
    {
        Tariff::create($request->validated());

        return redirect()->route('settings.tariffs.index')->with('success', 'Tariff created successfully.');
    }

    public function edit(Tariff $tariff)
    {
        return view('settings.tariffs.edit', compact('tariff'));
    }

    public function update(UpdateTariffRequest $request, Tariff $tariff)
    {
        $tariff->update($request->validated());

        return redirect()->route('settings.tariffs.index')->with('success', 'Tariff updated successfully.');
    }

    public function destroy(Tariff $tariff)
    {
        $tariff->delete();

        return redirect()->route('settings.tariffs.index')->with('success', 'Tariff deleted successfully.');
    }
}
