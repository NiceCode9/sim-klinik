<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreMenuRequest;
use App\Http\Requests\Settings\UpdateMenuRequest;
use App\Models\Menu;
use Spatie\Permission\Models\Permission;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::whereNull('parent_id')->orderBy('order')->get();

        return view('settings.menus.index', compact('menus'));
    }

    public function create()
    {
        $menus = Menu::whereNull('parent_id')->orderBy('order')->get();
        $permissions = Permission::all()->pluck('name');

        return view('settings.menus.create', compact('menus', 'permissions'));
    }

    public function store(StoreMenuRequest $request)
    {
        Menu::create($request->validated());

        return redirect()->route('settings.menus.index')->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu)
    {
        $menus = Menu::whereNull('parent_id')->where('id', '!=', $menu->id)->orderBy('order')->get();
        $permissions = Permission::all()->pluck('name');

        return view('settings.menus.edit', compact('menu', 'menus', 'permissions'));
    }

    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $menu->update($request->validated());

        return redirect()->route('settings.menus.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        $menu->children()->delete();
        $menu->delete();

        return redirect()->route('settings.menus.index')->with('success', 'Menu deleted successfully.');
    }
}
