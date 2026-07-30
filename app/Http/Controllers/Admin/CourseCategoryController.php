<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use App\Models\CourseCategoryDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseCategoryController extends Controller
{
    public function index()
    {
        $categories = CourseCategory::with('details')->orderBy('display_order')->paginate(20);
        return view('admin.course-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.course-categories.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_code' => 'required|max:20|unique:course_categories,category_code',
            'category_name' => 'required|max:100',
            'display_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('categories', 'public');
        }

        CourseCategory::create($data);

        return redirect('/admin/course-categories')->with('success', 'Kategori kursus berhasil ditambahkan.');
    }

    public function edit(CourseCategory $courseCategory)
    {
        return view('admin.course-categories.form', ['category' => $courseCategory]);
    }

    public function update(Request $request, CourseCategory $courseCategory): RedirectResponse
    {
        $data = $request->validate([
            'category_code' => 'required|max:20|unique:course_categories,category_code,' . $courseCategory->id,
            'category_name' => 'required|max:100',
            'display_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('categories', 'public');
        }

        $courseCategory->update($data);

        return redirect('/admin/course-categories')->with('success', 'Kategori kursus berhasil diperbarui.');
    }

    public function destroy(CourseCategory $courseCategory): RedirectResponse
    {
        $courseCategory->delete();
        return redirect('/admin/course-categories')->with('success', 'Kategori kursus berhasil dihapus.');
    }

    // ─── Detail Categories ────────────────────────────────────────────────────

    public function storeDetail(Request $request, CourseCategory $courseCategory): RedirectResponse
    {
        $data = $request->validate([
            'detail_code' => 'required|max:20|unique:course_category_details,detail_code',
            'detail_name' => 'required|max:100',
            'display_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('categories/details', 'public');
        }

        $courseCategory->details()->create($data);

        return back()->with('success', 'Detail kategori berhasil ditambahkan.');
    }

    public function destroyDetail(CourseCategoryDetail $detail): RedirectResponse
    {
        $detail->delete();
        return back()->with('success', 'Detail kategori berhasil dihapus.');
    }
}
