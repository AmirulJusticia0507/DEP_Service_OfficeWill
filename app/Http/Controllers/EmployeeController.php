<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Mail\AccountRegisteredMail;
use App\Models\Affiliation;
use App\Models\Employee;
use App\Models\EmployeeAffiliation;
use App\Models\MasterJob;
use App\Services\ScopeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function __construct(
        private ScopeService $scope
    ) {}

    public function index(Request $request)
    {
        $operator = Auth::guard('employee')->user();

        $this->authorize('viewAny', Employee::class);

        $query = $this->scope->scopeEmployeeQuery(
            Employee::query()->where('company_id', $operator->company_id),
            $operator
        );

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                    ->orWhere('employee_code', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $employees = $query->paginate(20)->withQueryString();

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $operator = Auth::guard('employee')->user();

        $this->authorize('create', Employee::class);

        $company = $operator->company;
        $jobs = MasterJob::where('company_id', $company->id)->get();
        $canManageAuthorities = $operator->is_sys_admin;

        return view('employees.create', compact('company', 'jobs', 'canManageAuthorities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $operator = Auth::guard('employee')->user();

        $this->authorize('create', Employee::class);

        $companyId = $operator->company_id;
        $canManageAuthorities = $operator->is_sys_admin;

        $rules = [
            'employee_code' => 'required|unique:employees,employee_code,NULL,id,company_id,'.$companyId,
            'full_name' => 'required|max:100',
            'kana_name' => 'nullable|max:100',
            'email' => 'required|email|unique:employees,email,NULL,id,company_id,'.$companyId,
            'phone_number' => 'nullable|max:30',
            'place_of_birth' => 'nullable|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE',
            'address' => 'nullable',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        if ($canManageAuthorities) {
            $rules += [
                'authority_effective_range' => 'required|in:ONLY,BELOW,ALL',
                'authority_effective_affiliation_code' => 'nullable|max:20',
                'can_register_employee' => 'boolean',
                'can_register_course' => 'boolean',
                'can_setting_attendance' => 'boolean',
            ];
        }

        $data = $request->validate($rules);

        $password = Str::random(12);

        $data['company_id'] = $companyId;
        $data['password'] = Hash::make($password);
        $data['is_sys_admin'] = false;
        $data['can_register_employee'] = $canManageAuthorities ? $request->boolean('can_register_employee') : false;
        $data['can_register_course'] = $canManageAuthorities ? $request->boolean('can_register_course') : false;
        $data['can_setting_attendance'] = $canManageAuthorities ? $request->boolean('can_setting_attendance') : false;

        if (! $canManageAuthorities) {
            unset($data['authority_effective_range'], $data['authority_effective_affiliation_code']);
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $employee = Employee::create($data);

        Mail::to($employee->email)->send(new AccountRegisteredMail(
            $employee->full_name,
            $employee->email,
            $password,
            config('app.url').'/login',
        ));

        NotificationHelper::send(
            $employee,
            'account_registered',
            'Account Created',
            'Your DEP Service account has been created. Check your email for login credentials.',
            route('login')
        );

        return redirect('/employees')->with('success', 'Karyawan berhasil didaftarkan.');
    }

    public function edit(Employee $employee)
    {
        $operator = Auth::guard('employee')->user();

        $this->authorize('update', $employee);

        $company = $employee->company;
        $canManageAuthorities = $operator->is_sys_admin;

        $assignments = $employee->employeeAffiliations()
            ->orderByDesc('start_date')
            ->get();
        $jobs = MasterJob::where('company_id', $company->id)->get();
        $affiliations = Affiliation::where('company_id', $company->id)
            ->whereIn('affiliation_code', $this->scope->authorizedAffiliationCodes($operator))
            ->orderBy('affiliation_name')
            ->get();

        return view('employees.edit', compact('employee', 'company', 'canManageAuthorities', 'assignments', 'jobs', 'affiliations'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $operator = Auth::guard('employee')->user();

        $this->authorize('update', $employee);

        $companyId = $employee->company_id;
        $canManageAuthorities = $operator->is_sys_admin;

        $rules = [
            'employee_code' => 'required|unique:employees,employee_code,'.$employee->id.',id,company_id,'.$companyId,
            'full_name' => 'required|max:100',
            'kana_name' => 'nullable|max:100',
            'email' => 'required|email|unique:employees,email,'.$employee->id.',id,company_id,'.$companyId,
            'phone_number' => 'nullable|max:30',
            'place_of_birth' => 'nullable|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE',
            'address' => 'nullable',
        ];

        if ($canManageAuthorities) {
            $rules += [
                'account_status' => 'required|in:ACTIVE,LOCKED,INACTIVE',
                'authority_effective_range' => 'required|in:ONLY,BELOW,ALL',
                'authority_effective_affiliation_code' => 'nullable|max:20',
                'can_register_employee' => 'boolean',
                'can_register_course' => 'boolean',
                'can_setting_attendance' => 'boolean',
            ];
        }

        $data = $request->validate($rules);

        if ($canManageAuthorities) {
            $data['can_register_employee'] = $request->boolean('can_register_employee');
            $data['can_register_course'] = $request->boolean('can_register_course');
            $data['can_setting_attendance'] = $request->boolean('can_setting_attendance');
        } else {
            unset($data['account_status'], $data['authority_effective_range'], $data['authority_effective_affiliation_code']);
            $data['can_register_employee'] = false;
            $data['can_register_course'] = false;
            $data['can_setting_attendance'] = false;
        }

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

    public function storeAssignment(Request $request, Employee $employee): RedirectResponse
    {
        $operator = Auth::guard('employee')->user();

        $this->authorize('update', $employee);

        $data = $request->validate([
            'affiliation_code' => 'required|exists:affiliations,affiliation_code,company_id,'.$employee->company_id,
            'job_id' => 'nullable|exists:master_jobs,job_id,company_id,'.$employee->company_id,
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if (! $this->scope->canAccessAffiliation($operator, $data['affiliation_code'])) {
            abort(403);
        }

        $conflict = DB::transaction(function () use ($employee, $data) {
            Employee::whereKey($employee->id)->lockForUpdate()->first();

            return EmployeeAffiliation::where('employee_id', $employee->id)
                ->where(function ($q) use ($data) {
                    $q->where('affiliation_code', $data['affiliation_code']);
                    if (! empty($data['job_id'])) {
                        $q->orWhere('job_id', $data['job_id']);
                    }
                })
                ->overlapping($data['start_date'], $data['end_date'])
                ->exists();
        });

        if ($conflict) {
            return back()->with('error', 'Periode penugasan tumpang tindih dengan penugasan yang sudah ada.');
        }

        EmployeeAffiliation::create([
            'company_id' => $employee->company_id,
            'employee_id' => $employee->id,
            'affiliation_code' => $data['affiliation_code'],
            'job_id' => $data['job_id'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
        ]);

        return back()->with('success', 'Penugasan berhasil ditambahkan.');
    }

    public function endAssignment(Request $request, Employee $employee, EmployeeAffiliation $assignment): RedirectResponse
    {
        $this->authorize('update', $employee);

        if ($assignment->employee_id !== $employee->id) {
            abort(404);
        }

        if ($assignment->end_date !== null) {
            return back()->with('error', 'Penugasan ini sudah ditutup.');
        }

        $data = $request->validate([
            'end_date' => 'nullable|date',
        ]);

        $endDate = $data['end_date'] ?? now()->toDateString();

        if ($endDate < $assignment->start_date->toDateString()) {
            return back()->with('error', 'Tanggal penutupan tidak boleh sebelum tanggal mulai.');
        }

        $assignment->update(['end_date' => $endDate]);

        return back()->with('success', 'Penugasan berhasil ditutup.');
    }

    public function destroyAssignment(Employee $employee, EmployeeAffiliation $assignment): RedirectResponse
    {
        $this->authorize('update', $employee);

        if ($assignment->employee_id !== $employee->id) {
            abort(404);
        }

        $assignment->delete();

        return back()->with('success', 'Penugasan berhasil dihapus.');
    }
}
