<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $employees = User::with('roles')
            ->orderBy('firstname')
            ->orderBy('surname')
            ->paginate(15)
            ->through(function (User $employee) {
                return [
                    'id' => $employee->id,
                    'firstname' => $employee->firstname,
                    'surname' => $employee->surname,
                    'email' => $employee->email,
                    'department' => $employee->department,
                    'role' => $employee->roles->first()?->role ?? 'employee',
                ];
            });

        return Inertia::render('Admin/Employees/Index', [
            'employees' => $employees,
        ]);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        return Inertia::render('Admin/Employees/Create');
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'dateOfBirth' => 'nullable|date',
            'contact' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'role' => 'required|in:admin,employee',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $employee = User::create([
            'firstname' => $validated['firstname'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'department' => $validated['department'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $this->syncRole($employee, $validated['role']);

        return redirect()->back()->with('success', 'Employee created successfully!');
    }

    /**
     * Display the specified employee.
     */
    public function show(User $employee)
    {
        return;
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(User $employee)
    {
        // return Inertia::render('Admin/Employees/Edit', [
        //     'employee' => $employee,
        // ]);
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'dateOfBirth' => 'nullable|date',
            'contact' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'role' => 'required|in:admin,employee',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'firstname' => $validated['firstname'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'department' => $validated['department'] ?? null,
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $employee->update($updateData);
        $this->syncRole($employee, $validated['role']);

        return redirect()->back()->with('success', 'Employee updated successfully!');
    }

    /**
     * Update only the specified employee role.
     */
    public function updateRole(Request $request, User $employee)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,employee',
        ]);

        $this->syncRole($employee, $validated['role']);

        return redirect()->back()->with('success', 'Employee role updated successfully!');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(User $employee)
    {
        $employee->delete();

        return redirect()->back()->with('success', 'Employee deleted successfully!');
    }

    /**
     * Ensure the user has a single effective role in the current app flow.
     */
    private function syncRole(User $employee, string $role): void
    {
        if ($employee->roles()->exists()) {
            $employee->roles()->update(['role' => $role]);

            return;
        }

        $employee->roles()->create([
            'role' => $role,
        ]);
    }
}
