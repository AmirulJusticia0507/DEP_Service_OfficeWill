<?php

namespace App\Http\Controllers;

use App\Actions\Employee\FilterEmployeeByScopeAction;
use App\Mail\AccountRegisteredMail;
use App\Models\Company;
use App\Models\Employee;
use App\Models\MasterJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function __construct(
        private FilterEmployeeByScopeAction $filterScope
    ) {}

    public function index(Request $request)
    {
        $operator = Auth::guard('employee')->user();
        $query = Employee::query()->where('company_id', $operator->company_id);

        $query = $this->filterScope->execute($query, $operator);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('employee_code', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->paginate(20);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $company = Auth::guard('employee')->user()->company;
        $jobs = MasterJob::where('company_id', $company->id)->get();

        return view('employees.create', compact('company', 'jobs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $operator = Auth::guard('employee')->user();
        $companyId = $operator->company_id;

        $data = $request->validate([
            'employee_code' => 'required|unique:employees,employee_code,NULL,id,company_id,' . $companyId,
            'full_name' => 'required|max:100',
            'kana_name' => 'nullable|max:100',
            'email' => 'required|email|unique:employees,email,NULL,id,company_id,' . $companyId,
            'phone_number' => 'nullable|max:30',
            'place_of_birth' => 'nullable|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE',
            'address' => 'nullable',
            'authority_effective_range' => 'required|in:ONLY,BELOW,ALL',
            'authority_effective_affiliation_code' => 'nullable|max:20',
            'can_register_employee' => 'boolean',
            'can_register_course' => 'boolean',
            'can_setting_attendance' => 'boolean',
        ]);

        $password = Str::random(12);

        $data['company_id'] = $companyId;
        $data['password'] = Hash::make($password);
        $data['can_register_employee'] = $request->boolean('can_register_employee');
        $data['can_register_course'] = $request->boolean('can_register_course');
        $data['can_setting_attendance'] = $request->boolean('can_setting_attendance');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $employee = Employee::create($data);

        Mail::to($employee->email)->send(new AccountRegisteredMail(
            $employee->full_name,
            $employee->email,
            $password,
            config('app.url') . '/login',
        ));

        return redirect('/employees')->with('success', 'Karyawan berhasil didaftarkan.');
    }

    public function edit(Employee $employee)
    {
        $company = $employee->company;

        return view('employees.edit', compact('employee', 'company'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $companyId = $employee->company_id;

        $data = $request->validate([
            'employee_code' => 'required|unique:employees,employee_code,' . $employee->id . ',id,company_id,' . $companyId,
            'full_name' => 'required|max:100',
            'kana_name' => 'nullable|max:100',
            'email' => 'required|email|unique:employees,email,' . $employee->id . ',id,company_id,' . $companyId,
            'phone_number' => 'nullable|max:30',
            'place_of_birth' => 'nullable|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE',
            'address' => 'nullable',
            'account_status' => 'required|in:ACTIVE,LOCKED,INACTIVE',
            'authority_effective_range' => 'required|in:ONLY,BELOW,ALL',
            'authority_effective_affiliation_code' => 'nullable|max:20',
            'can_register_employee' => 'boolean',
            'can_register_course' => 'boolean',
            'can_setting_attendance' => 'boolean',
        ]);

        $data['can_register_employee'] = $request->boolean('can_register_employee');
        $data['can_register_course'] = $request->boolean('can_register_course');
        $data['can_setting_attendance'] = $request->boolean('can_setting_attendance');

        if ($request->hasFile('photo')) {
            $request->validate(['photo' => 'image|mimes:jpg,jpeg,png|max:2048']);
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $employee->update($data);

        return redirect('/employees')->with('success', 'Data karyawan berhasil diperbarui.');
    }
}
