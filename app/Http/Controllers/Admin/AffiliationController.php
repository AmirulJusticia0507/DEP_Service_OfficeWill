<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliationController extends Controller
{
    public function index()
    {
        $affiliations = Affiliation::where('company_id', Auth::guard('employee')->user()->company_id)
            ->orderBy('display_order')
            ->paginate(20)->withQueryString();

        return view('admin.affiliations.index', compact('affiliations'));
    }

    public function create()
    {
        $parentAffiliations = Affiliation::where('company_id', Auth::guard('employee')->user()->company_id)
            ->orderBy('display_order')
            ->get();

        return view('admin.affiliations.form', compact('parentAffiliations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $companyId = Auth::guard('employee')->user()->company_id;

        $data = $request->validate([
            'affiliation_code' => 'required|max:20|unique:affiliations,affiliation_code,NULL,id,company_id,' . $companyId,
            'affiliation_name' => 'required|max:150',
            'parent_affiliation_code' => 'nullable|max:20|exists:affiliations,affiliation_code',
            'display_order' => 'nullable|integer|min:0',
            'organization_type' => 'nullable|integer|in:1,2',
        ]);

        $data['company_id'] = $companyId;

        Affiliation::create($data);

        return redirect('/admin/affiliations')->with('success', 'Afiliasi berhasil ditambahkan.');
    }

    public function edit(Affiliation $affiliation)
    {
        $parentAffiliations = Affiliation::where('company_id', Auth::guard('employee')->user()->company_id)
            ->where('id', '!=', $affiliation->id)
            ->orderBy('display_order')
            ->get();

        return view('admin.affiliations.form', compact('affiliation', 'parentAffiliations'));
    }

    public function update(Request $request, Affiliation $affiliation): RedirectResponse
    {
        $companyId = Auth::guard('employee')->user()->company_id;

        $data = $request->validate([
            'affiliation_code' => 'required|max:20|unique:affiliations,affiliation_code,' . $affiliation->id . ',id,company_id,' . $companyId,
            'affiliation_name' => 'required|max:150',
            'parent_affiliation_code' => 'nullable|max:20|exists:affiliations,affiliation_code',
            'display_order' => 'nullable|integer|min:0',
            'organization_type' => 'nullable|integer|in:1,2',
        ]);

        $affiliation->update($data);

        return redirect('/admin/affiliations')->with('success', 'Afiliasi berhasil diperbarui.');
    }

    public function destroy(Affiliation $affiliation): RedirectResponse
    {
        $affiliation->delete();
        return redirect('/admin/affiliations')->with('success', 'Afiliasi berhasil dihapus.');
    }
}
