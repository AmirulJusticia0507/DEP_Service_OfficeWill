<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseMaterial;
use App\Models\CourseTodo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Course::class);
        $query = Course::with('categoryDetail.category')->withCount('questions');

        if ($search = $request->get('search')) {
            $query->where('course_name', 'LIKE', "%{$search}%");
        }

        $courses = $query->paginate(20)->withQueryString();

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $this->authorize('create', Course::class);
        $categories = CourseCategory::with('details')->orderBy('display_order')->get();

        return view('courses.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Course::class);
        $data = $request->validate([
            'category_detail_id' => 'required|exists:course_category_details,id',
            'course_name' => 'required|max:200',
            'description' => 'nullable',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'has_retest' => 'boolean',
            'passing_score' => 'required|integer|min:0|max:100',
        ]);

        $data['has_retest'] = $request->boolean('has_retest');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('courses', 'public');
        }

        $course = Course::create($data);

        return redirect("/courses/{$course->id}/edit")->with('success', 'Kursus berhasil dibuat.');
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        $course->loadCount('questions')->load('materials', 'todos', 'categoryDetail.category');
        $categories = CourseCategory::with('details')->orderBy('display_order')->get();

        return view('courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);
        $data = $request->validate([
            'category_detail_id' => 'required|exists:course_category_details,id',
            'course_name' => 'required|max:200',
            'description' => 'nullable',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'has_retest' => 'boolean',
            'passing_score' => 'required|integer|min:0|max:100',
        ]);

        $data['has_retest'] = $request->boolean('has_retest');

        if ($request->hasFile('photo')) {
            if ($course->photo) {
                Storage::disk('public')->delete($course->photo);
            }
            $data['photo'] = $request->file('photo')->store('courses', 'public');
        }

        $course->update($data);

        return back()->with('success', 'Kursus berhasil diperbarui.');
    }

    public function storeMaterial(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);
        $data = $request->validate([
            'material_type' => 'required|in:YOUTUBE,PDF',
            'title' => 'required|max:200',
            'content_url_or_path' => 'required|max:255',
            'display_order' => 'integer|min:0',
        ]);

        $course->materials()->create($data);

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    public function destroyMaterial(CourseMaterial $material): RedirectResponse
    {
        $this->authorize('update', $material->course);
        $material->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }

    public function storeTodo(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);
        $data = $request->validate([
            'todo_type' => 'required|in:QUESTIONNAIRE,REPORT,TEST',
            'title' => 'required|max:200',
            'description' => 'nullable',
            'display_order' => 'integer|min:0',
            'passing_score' => 'nullable|integer|min:0|max:100',
        ]);

        $course->todos()->create($data);

        return back()->with('success', 'Todo berhasil ditambahkan.');
    }

    public function destroyTodo(CourseTodo $todo): RedirectResponse
    {
        $this->authorize('update', $todo->course);
        $todo->delete();

        return back()->with('success', 'Todo berhasil dihapus.');
    }
}
