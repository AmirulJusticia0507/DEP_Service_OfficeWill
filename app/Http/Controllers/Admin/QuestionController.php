<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Course $course)
    {
        $course->load('questions.options');
        return view('admin.questions.index', compact('course'));
    }

    public function create(Course $course)
    {
        return view('admin.questions.form', compact('course'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'question_type' => 'required|in:MCQ,TRUE_FALSE,ESSAY',
            'question_text' => 'required',
            'points' => 'required|integer|min:1',
            'display_order' => 'nullable|integer',
            'options' => 'required_if:question_type,MCQ|required_if:question_type,TRUE_FALSE|array',
            'options.*.option_text' => 'required',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        $question = $course->questions()->create([
            'question_type' => $data['question_type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
            'display_order' => $data['display_order'] ?? 0,
        ]);

        if (in_array($data['question_type'], ['MCQ', 'TRUE_FALSE']) && isset($data['options'])) {
            foreach ($data['options'] as $i => $opt) {
                $question->options()->create([
                    'option_text' => $opt['option_text'],
                    'is_correct' => !empty($opt['is_correct']),
                    'display_order' => $i,
                ]);
            }
        }

        return redirect()->route('admin.questions.index', $course)
            ->with('success', __('Saved successfully'));
    }

    public function edit(Course $course, Question $question)
    {
        $question->load('options');
        return view('admin.questions.form', compact('course', 'question'));
    }

    public function update(Request $request, Course $course, Question $question): RedirectResponse
    {
        $data = $request->validate([
            'question_type' => 'required|in:MCQ,TRUE_FALSE,ESSAY',
            'question_text' => 'required',
            'points' => 'required|integer|min:1',
            'display_order' => 'nullable|integer',
            'options' => 'nullable|array',
            'options.*.id' => 'nullable|exists:question_options,id',
            'options.*.option_text' => 'required',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        $question->update([
            'question_type' => $data['question_type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
            'display_order' => $data['display_order'] ?? 0,
        ]);

        if (in_array($data['question_type'], ['MCQ', 'TRUE_FALSE']) && isset($data['options'])) {
            $existingIds = $question->options()->pluck('id')->toArray();
            $keepIds = [];

            foreach ($data['options'] as $i => $opt) {
                if (!empty($opt['id'])) {
                    QuestionOption::where('id', $opt['id'])->update([
                        'option_text' => $opt['option_text'],
                        'is_correct' => !empty($opt['is_correct']),
                        'display_order' => $i,
                    ]);
                    $keepIds[] = $opt['id'];
                } else {
                    $new = $question->options()->create([
                        'option_text' => $opt['option_text'],
                        'is_correct' => !empty($opt['is_correct']),
                        'display_order' => $i,
                    ]);
                    $keepIds[] = $new->id;
                }
            }

            $toDelete = array_diff($existingIds, $keepIds);
            QuestionOption::whereIn('id', $toDelete)->delete();
        }

        return redirect()->route('admin.questions.index', $course)
            ->with('success', __('Updated successfully'));
    }

    public function destroy(Course $course, Question $question): RedirectResponse
    {
        $question->options()->delete();
        $question->delete();
        return redirect()->route('admin.questions.index', $course)
            ->with('success', __('Deleted successfully'));
    }
}
