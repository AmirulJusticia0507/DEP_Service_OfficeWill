<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorityController extends Controller
{
    public function index(Request $request)
    {
        $operator = Auth::guard('employee')->user();

        $this->authorize('viewAuthority', Employee::class);

        $query = Employee::where('company_id', $operator->company_id);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('employee_code', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->orderBy('is_sys_admin', 'desc')->orderBy('employee_code')->paginate(20)->withQueryString();

        return view('admin.authorities.index', compact('employees'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $operator = Auth::guard('employee')->user();

        $this->authorize('manageAuthority', $employee);

        $data = $request->validate([
            'is_sys_admin' => 'sometimes|boolean',
            'can_register_employee' => 'sometimes|boolean',
            'can_register_course' => 'sometimes|boolean',
            'can_setting_attendance' => 'sometimes|boolean',
            'authority_effective_range' => 'required|in:ONLY,BELOW,ALL',
            'authority_effective_affiliation_code' => 'nullable|string|max:20',
        ]);

        $data['is_sys_admin'] = $request->boolean('is_sys_admin');
        $data['can_register_employee'] = $request->boolean('can_register_employee');
        $data['can_register_course'] = $request->boolean('can_register_course');
        $data['can_setting_attendance'] = $request->boolean('can_setting_attendance');

        $employee->update($data);

        return back()->with('success', 'Authority updated for '.$employee->full_name);
    }
}
