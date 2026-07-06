<?php

namespace App\Http\Controllers\Settings;

use App\Enums\EmployeeType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreEmployeeRequest;
use App\Http\Requests\Settings\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Specialization;
use App\Models\User;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'specialization'])->orderBy('name')->get();

        return view('settings.employees.index', compact('employees'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $specializations = Specialization::orderBy('name')->get();
        $employeeTypes = EmployeeType::cases();

        return view('settings.employees.create', compact('users', 'specializations', 'employeeTypes'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        Employee::create($request->validated());

        return redirect()->route('settings.employees.index')->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $specializations = Specialization::orderBy('name')->get();
        $employeeTypes = EmployeeType::cases();

        return view('settings.employees.edit', compact('employee', 'users', 'specializations', 'employeeTypes'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $employee->update($request->validated());

        return redirect()->route('settings.employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('settings.employees.index')->with('success', 'Employee deleted successfully.');
    }
}
