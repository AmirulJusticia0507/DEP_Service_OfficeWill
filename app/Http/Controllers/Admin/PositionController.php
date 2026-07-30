<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    public function index()
    {
        $positions = MasterJob::where('company_id', Auth::guard('employee')->user()->company_id)
            ->orderBy('display_order')
            ->paginate(20);

        return view('admin.positions.index', compact('positions'));
    }

    public function create()
    {
        return view('admin.positions.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = Auth::guard('employee')->user()->company_id;

        $data = $request->validate([
            'job_id' => 'required|max:20|unique:master_jobs,job_id,NULL,id,company_id,' . $companyId,
            'job_title' => 'required|max:100',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $data['company_id'] = $companyId;

        MasterJob::create($data);

        return redirect('/admin/positions')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(MasterJob $position)
    {
        return view('admin.positions.form', compact('position'));
    }

    public function update(Request $request, MasterJob $position): RedirectResponse
    {
        $companyId = Auth::guard('employee')->user()->company_id;

        $data = $request->validate([
            'job_id' => 'required|max:20|unique:master_jobs,job_id,' . $position->id . ',id,company_id,' . $companyId,
            'job_title' => 'required|max:100',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $position->update($data);

        return redirect('/admin/positions')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(MasterJob $position): RedirectResponse
    {
        $position->delete();
        return redirect('/admin/positions')->with('success', 'Jabatan berhasil dihapus.');
    }
}
