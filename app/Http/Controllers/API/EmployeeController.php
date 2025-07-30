<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeContact;
use App\Models\EmployeeAddress;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Employee::with(['department', 'contacts', 'addresses'])->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd('Reached store method');
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:employees',
            'department_id' => 'required|exists:departments,id',
            'contacts' => 'array',
            'contacts.*' => 'required|string',
            'addresses' => 'array',
            'addresses.*.address_line1' => 'required|string',
            'addresses.*.city' => 'required|string',
            'addresses.*.state' => 'required|string',
            'addresses.*.postal_code' => 'required|string',
        ]);

        $employee = Employee::create($request->only('name', 'email', 'department_id'));

        // Save contacts
        foreach ($request->contacts ?? [] as $phone) {
            $employee->contacts()->create(['phone' => $phone]);
        }

        // Save addresses
        foreach ($request->addresses ?? [] as $addr) {
            $employee->addresses()->create($addr);
        }

        return response()->json($employee->load(['department', 'contacts', 'addresses']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['department', 'contacts', 'addresses'])->find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:employees,email,' . $id,
            'department_id' => 'required|exists:departments,id',
            'contacts' => 'array',
            'contacts.*' => 'required|string',
            'addresses' => 'array',
            'addresses.*.address_line1' => 'required|string',
            'addresses.*.city' => 'required|string',
            'addresses.*.state' => 'required|string',
            'addresses.*.postal_code' => 'required|string',
        ]);

        $employee->update($request->only('name', 'email', 'department_id'));

        // Delete old contacts/addresses
        $employee->contacts()->delete();
        $employee->addresses()->delete();

        foreach ($request->contacts ?? [] as $phone) {
            $employee->contacts()->create(['phone' => $phone]);
        }

        foreach ($request->addresses ?? [] as $addr) {
            $employee->addresses()->create($addr);
        }

        return response()->json($employee->load(['department', 'contacts', 'addresses']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted']);
    }
}
