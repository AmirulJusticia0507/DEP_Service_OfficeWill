@extends('layouts.app')
@section('title', (isset($question) ? 'Edit' : 'Add') . ' Question')
@section('header-icon')<i class="ti ti-question-mark"></i>@endsection
@section('breadcrumbs')
    <a href="{{ route('courses.index') }}" class="text-slate-400 hover:text-slate-600">{{ __('Courses') }}</a>
    <span class="mx-1">/</span>
    <a href="{{ route('admin.questions.index', $course) }}" class="text-slate-400 hover:text-slate-600">{{ __('Questions') }}</a>
    <span class="mx-1">/</span>
    <span class="text-slate-800 font-medium dark:text-slate-100">{{ isset($question) ? __('Edit') : __('Add New') }}</span>
@endsection
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded shadow p-6 dark:bg-navy-800">
        <form method="POST" action="{{ isset($question) ? route('admin.questions.update', [$course, $question]) : route('admin.questions.store', $course) }}">
            @csrf
            @if(isset($question)) @method('PUT') @endif

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">{{ __('Type') }} <span class="required-mark">*</span></label>
                <select name="question_type" id="questionType" class="form-select" required>
                    <option value="MCQ" @if(isset($question) && $question->question_type === 'MCQ') selected @endif>Multiple Choice (MCQ)</option>
                    <option value="TRUE_FALSE" @if(isset($question) && $question->question_type === 'TRUE_FALSE') selected @endif>True / False</option>
                    <option value="ESSAY" @if(isset($question) && $question->question_type === 'ESSAY') selected @endif>Essay</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">{{ __('Question') }} <span class="required-mark">*</span></label>
                <textarea name="question_text" rows="3" class="form-input" required>{{ old('question_text', $question->question_text ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Points') }} <span class="required-mark">*</span></label>
                    <input type="number" name="points" value="{{ old('points', $question->points ?? 1) }}" min="1" class="form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('Display Order') }}</label>
                    <input type="number" name="display_order" value="{{ old('display_order', $question->display_order ?? 0) }}" class="form-input">
                </div>
            </div>

            <div id="optionsSection">
                <label class="block text-sm font-medium mb-2">{{ __('Options') }}</label>
                <div id="optionsList" class="space-y-2">
                    @if(isset($question) && $question->options->isNotEmpty())
                        @foreach($question->options as $i => $opt)
                        <div class="flex items-center gap-2 option-row">
                            <input type="hidden" name="options[{{ $i }}][id]" value="{{ $opt->id }}">
                            <input type="text" name="options[{{ $i }}][option_text]" value="{{ $opt->option_text }}" class="form-input flex-1" placeholder="Option text" required>
                            <label class="flex items-center gap-1 text-xs whitespace-nowrap cursor-pointer">
                                <input type="radio" name="correct_option" value="{{ $i }}" {{ $opt->is_correct ? 'checked' : '' }} class="correct-radio">
                                {{ __('Correct Answer') }}
                            </label>
                            <input type="hidden" name="options[{{ $i }}][is_correct]" value="{{ $opt->is_correct ? 1 : 0 }}" class="is-correct-input">
                            <button type="button" class="icon-btn text-rose-400 remove-option" title="Remove"><i class="ti ti-x text-xs"></i></button>
                        </div>
                        @endforeach
                    @else
                        <div class="flex items-center gap-2 option-row">
                            <input type="text" name="options[0][option_text]" class="form-input flex-1" placeholder="Option text" required>
                            <label class="flex items-center gap-1 text-xs whitespace-nowrap cursor-pointer">
                                <input type="radio" name="correct_option" value="0" class="correct-radio">
                                {{ __('Correct Answer') }}
                            </label>
                            <input type="hidden" name="options[0][is_correct]" value="0" class="is-correct-input">
                            <button type="button" class="icon-btn text-rose-400 remove-option" title="Remove"><i class="ti ti-x text-xs"></i></button>
                        </div>
                    @endif
                </div>
                <button type="button" id="addOption" class="btn-secondary text-xs mt-2"><i class="ti ti-plus"></i> Add Option</button>
            </div>

            <div class="flex items-center gap-2 mt-6">
                <button type="submit" class="btn-primary">{{ isset($question) ? __('Update') : __('Save') }}</button>
                <a href="{{ route('admin.questions.index', $course) }}" class="btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('questionType');
    const optionsSection = document.getElementById('optionsSection');
    const optionsList = document.getElementById('optionsList');
    const addBtn = document.getElementById('addOption');
    let optIndex = {{ isset($question) ? $question->options->count() : 1 }};

    function toggleOptions() {
        const val = typeSelect.value;
        if (val === 'ESSAY') {
            optionsSection.classList.add('hidden');
        } else if (val === 'TRUE_FALSE') {
            optionsSection.classList.remove('hidden');
            optionsList.innerHTML = '';
            ['True', 'False'].forEach((text, i) => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2 option-row';
                div.innerHTML = `
                    <input type="text" name="options[${i}][option_text]" value="${text}" class="form-input flex-1" required readonly>
                    <label class="flex items-center gap-1 text-xs whitespace-nowrap cursor-pointer">
                        <input type="radio" name="correct_option" value="${i}" ${i === 0 ? 'checked' : ''} class="correct-radio">
                        {{ __('Correct Answer') }}
                    </label>
                    <input type="hidden" name="options[${i}][is_correct]" value="${i === 0 ? 1 : 0}" class="is-correct-input">
                `;
                optionsList.appendChild(div);
            });
            optIndex = 2;
        } else {
            optionsSection.classList.remove('hidden');
            if (optionsList.children.length === 0) {
                addOptionRow();
                addOptionRow();
            }
        }
        updateRadios();
    }

    function addOptionRow(val = '') {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 option-row';
        div.innerHTML = `
            <input type="text" name="options[${optIndex}][option_text]" value="${val}" class="form-input flex-1" placeholder="Option text" required>
            <label class="flex items-center gap-1 text-xs whitespace-nowrap cursor-pointer">
                <input type="radio" name="correct_option" value="${optIndex}" class="correct-radio">
                {{ __('Correct Answer') }}
            </label>
            <input type="hidden" name="options[${optIndex}][is_correct]" value="0" class="is-correct-input">
            <button type="button" class="icon-btn text-rose-400 remove-option" title="Remove"><i class="ti ti-x text-xs"></i></button>
        `;
        optionsList.appendChild(div);
        optIndex++;
        updateRadios();
    }

    function updateRadios() {
        document.querySelectorAll('.correct-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.is-correct-input').forEach(h => h.value = '0');
                const idx = this.value;
                const hidden = this.closest('.option-row').querySelector('.is-correct-input');
                if (hidden) hidden.value = '1';
            });
        });
        document.querySelectorAll('.remove-option').forEach(btn => {
            btn.addEventListener('click', function() {
                if (optionsList.children.length > 2) {
                    this.closest('.option-row').remove();
                    updateRadios();
                }
            });
        });
    }

    typeSelect.addEventListener('change', toggleOptions);
    addBtn.addEventListener('click', () => addOptionRow());
    toggleOptions();

    // Init radio listeners
    document.querySelectorAll('.correct-radio').forEach(r => {
        if (r.checked) {
            const hidden = r.closest('.option-row').querySelector('.is-correct-input');
            if (hidden) hidden.value = '1';
        }
    });
});
</script>
@endsection
