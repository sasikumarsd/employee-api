<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    /**
     * Search employees by name or email.
     */
    public function search(Request $request)
    {
        $keyword = trim($request->query('search'), "\"'");
        
        $employees = Employee::with(['contacts', 'addresses', 'department'])
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            })
            ->get();

        return response()->json([
            'message' => 'Search completed',
            'results' => $employees
        ]);
    }

    /**
     * Display a listing of the employees.
     */
    public function index(Request $request)
    {
        // $employees = Employee::with(['department', 'contacts', 'addresses'])->get();

        // return response()->json([
        //     'message' => 'Employee list fetched successfully',
        //     'data' => $employees
        // ]);

        $perPage = $request->get('per_page', 10); // Default 10 records per page

        $employees = Employee::with(['contacts', 'addresses', 'department'])
            ->paginate($perPage);

        return response()->json($employees);
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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

        foreach ($request->contacts ?? [] as $phone) {
            $employee->contacts()->create(['phone' => $phone]);
        }

        foreach ($request->addresses ?? [] as $addr) {
            $employee->addresses()->create($addr);
        }

        return response()->json([
            'message' => 'Employee created successfully',
            'employee' => $employee->load(['department', 'contacts', 'addresses'])
        ], 201);
    }

    /**
     * Display the specified employee.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['department', 'contacts', 'addresses'])->find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        return response()->json([
            'message' => 'Employee details fetched successfully',
            'employee' => $employee
        ]);
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $validated = $request->validate([
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

        $employee->contacts()->delete();
        foreach ($request->contacts ?? [] as $phone) {
            $employee->contacts()->create(['phone' => $phone]);
        }

        $employee->addresses()->delete();
        foreach ($request->addresses ?? [] as $addr) {
            $employee->addresses()->create($addr);
        }

        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => $employee->load(['department', 'contacts', 'addresses'])
        ]);
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(string $id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
