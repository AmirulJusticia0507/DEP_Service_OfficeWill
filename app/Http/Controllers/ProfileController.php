<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CourseEnrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $employee = Auth::guard('employee')->user();
        $employee->load('employeeAffiliations.affiliation', 'enrollments.course');

        return view('profile.show', compact('employee'));
    }

    public function edit()
    {
        $employee = Auth::guard('employee')->user();

        return view('profile.edit', compact('employee'));
    }

    public function update(Request $request): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();

        $data = $request->validate([
            'full_name' => 'required|max:255',
            'kana_name' => 'nullable|max:255',
            'phone_number' => 'nullable|max:20',
            'place_of_birth' => 'nullable|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:MALE,FEMALE',
            'address' => 'nullable',
        ]);

        if ($request->hasFile('photo')) {
            $request->validate(['photo' => 'image|mimes:jpg,jpeg,png|max:2048']);
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $employee->update($data);

        $employee->update([
            'mfa_enabled' => $request->boolean('mfa_enabled'),
        ]);

        return redirect()->route('profile.show')->with('success', __('Updated successfully'));
    }

    public function transcript()
    {
        $employee = Auth::guard('employee')->user();
        $enrollments = CourseEnrollment::with('course', 'todoResponses.courseTodo')
            ->where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $certificates = Certificate::with('course')
            ->where('employee_id', $employee->id)
            ->get()
            ->keyBy('course_id');

        return view('profile.transcript', compact('employee', 'enrollments', 'certificates'));
    }
}
