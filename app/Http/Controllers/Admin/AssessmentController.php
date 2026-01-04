<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAssessmentRequest;
use App\Http\Requests\Admin\UpdateAssessmentRequest;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentOption;
use App\Models\UserAssessment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AssessmentController extends Controller
{
    private function normalizeTranslatableValue($value): array
    {
        if (is_array($value)) {
            return array_filter($value, fn ($v) => is_string($v) && trim($v) !== '');
        }

        if (is_string($value) && trim($value) !== '') {
            return ['vi' => $value];
        }

        return [];
    }

    private function redirectToIndex(Request $request)
    {
        $routeName = (string) optional($request->route())->getName();

        if (str_starts_with($routeName, 'consultant.')) {
            return redirect()->route('consultant.assessments.index', ['locale' => app()->getLocale()]);
        }

        return redirect()->route('admin.assessments.index');
    }

    private function redirectToShow(Request $request, Assessment $assessment)
    {
        $routeName = (string) optional($request->route())->getName();

        if (str_starts_with($routeName, 'consultant.')) {
            return redirect()->route('consultant.assessments.show', [
                'locale' => app()->getLocale(),
                'assessment' => $assessment->id,
            ]);
        }

        return redirect()->route('admin.assessments.show', $assessment);
    }

    private function resolveAssessment(): Assessment
    {
        $assessment = request()->route('assessment');

        if ($assessment instanceof Assessment) {
            return $assessment;
        }

        return Assessment::query()->findOrFail($assessment);
    }

    private function resolveQuestion(): AssessmentQuestion
    {
        $question = request()->route('question');

        if ($question instanceof AssessmentQuestion) {
            return $question;
        }

        return AssessmentQuestion::query()->findOrFail($question);
    }

    public function index()
    {
        $assessments = Assessment::with('creator')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();

        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.assessments.index', compact('assessments'));
        }

        return view('admin.assessments.index', compact('assessments'));
    }

    public function create()
    {
        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.assessments.create');
        }

        return view('admin.assessments.create');
    }

    public function store(StoreAssessmentRequest $request)
    {
        $validated = $request->validated();

        $assessment = Assessment::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'created',
            'created_by' => Auth::id(),
        ]);

        foreach ($validated['questions'] as $questionData) {
            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'content' => $questionData['content'],
                'type' => $questionData['type'],
                'order' => $questionData['order'],
            ]);

            foreach ($questionData['options'] as $optionData) {
                AssessmentOption::create([
                    'question_id' => $question->id,
                    'content' => $optionData['content'],
                    'score' => $optionData['score'],
                ]);
            }
        }

        return $this->redirectToShow($request, $assessment)
            ->with('success', 'Assessment created successfully!');
    }

    public function show()
    {
        $assessment = $this->resolveAssessment();
        $assessment->load(['questions.options']);

        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.assessments.show', compact('assessment'));
        }

        return view('admin.assessments.show', compact('assessment'));
    }

    public function exportJson(Request $request)
    {
        $assessment = $this->resolveAssessment();
        $assessment->load(['questions.options']);

        $payload = [
            'title' => $assessment->getRawOriginal('title'),
            'description' => $assessment->getRawOriginal('description'),
            'status' => $assessment->status,
            'questions' => $assessment->questions->map(function (AssessmentQuestion $q) {
                return [
                    'order' => $q->order,
                    'type' => $q->type,
                    'content' => $q->getRawOriginal('content'),
                    'options' => $q->options->map(function (AssessmentOption $o) {
                        return [
                            'content' => $o->getRawOriginal('content'),
                            'score' => $o->score,
                        ];
                    })->values()->all(),
                ];
            })->values()->all(),
        ];

        $filename = 'assessment-' . $assessment->id . '.json';

        return response()->streamDownload(function () use ($payload) {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function importJson(Request $request)
    {
        $data = $request->validate([
            'json_file' => ['required', 'file'],
        ]);

        $raw = file_get_contents($data['json_file']->getRealPath());
        $decoded = json_decode($raw, true);

        if (!is_array($decoded)) {
            return back()->with('error', 'Invalid JSON file.');
        }

        $title = $this->normalizeTranslatableValue($decoded['title'] ?? null);
        $description = $this->normalizeTranslatableValue($decoded['description'] ?? null);

        if ($title === [] || $description === []) {
            return back()->with('error', 'JSON must include title and description (VI or EN).');
        }

        $status = ($decoded['status'] ?? 'created');
        if (!in_array($status, ['created', 'active'], true)) {
            $status = 'created';
        }

        $questions = $decoded['questions'] ?? [];
        if (!is_array($questions) || $questions === []) {
            return back()->with('error', 'JSON must include at least 1 question.');
        }

        $assessment = Assessment::create([
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'created_by' => Auth::id(),
        ]);

        foreach ($questions as $q) {
            $qContent = $this->normalizeTranslatableValue($q['content'] ?? null);
            if ($qContent === []) {
                continue;
            }

            $qType = $q['type'] ?? 'single_choice';
            if (!in_array($qType, ['single_choice', 'multi_choice'], true)) {
                $qType = 'single_choice';
            }

            $qOrder = (int) ($q['order'] ?? 1);
            if ($qOrder < 1) {
                $qOrder = 1;
            }

            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'content' => $qContent,
                'type' => $qType,
                'order' => $qOrder,
            ]);

            $options = $q['options'] ?? [];
            if (!is_array($options)) {
                $options = [];
            }

            foreach ($options as $opt) {
                $oContent = $this->normalizeTranslatableValue($opt['content'] ?? null);
                if ($oContent === []) {
                    continue;
                }

                $score = (int) ($opt['score'] ?? 1);
                if ($score < 1) {
                    $score = 1;
                }
                if ($score > 5) {
                    $score = 5;
                }

                AssessmentOption::create([
                    'question_id' => $question->id,
                    'content' => $oContent,
                    'score' => $score,
                ]);
            }
        }

        return $this->redirectToShow($request, $assessment)
            ->with('success', 'Assessment imported successfully!');
    }

    public function edit()
    {
        $assessment = $this->resolveAssessment();
        $assessment->load(['questions.options']);

        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.assessments.edit', compact('assessment'));
        }

        return view('admin.assessments.edit', compact('assessment'));
    }

    public function update(UpdateAssessmentRequest $request)
    {
        $assessment = $this->resolveAssessment();
        $validated = $request->validated();

        $assessment->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'] ?? $assessment->status,
        ]);

        // Delete existing questions and options
        $assessment->questions()->each(function ($question) {
            $question->options()->delete();
            $question->delete();
        });

        // Create new questions and options
        foreach ($validated['questions'] as $questionData) {
            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'content' => $questionData['content'],
                'type' => $questionData['type'],
                'order' => $questionData['order'],
            ]);

            foreach ($questionData['options'] as $optionData) {
                AssessmentOption::create([
                    'question_id' => $question->id,
                    'content' => $optionData['content'],
                    'score' => $optionData['score'],
                ]);
            }
        }

        return $this->redirectToShow($request, $assessment)
            ->with('success', 'Assessment updated successfully!');
    }

    public function storeQuestion(Request $request)
    {
        $assessment = $this->resolveAssessment();

        $data = $request->validate([
            'content' => 'required|array|min:1',
            'content.vi' => 'nullable|string|required_without:content.en',
            'content.en' => 'nullable|string|required_without:content.vi',
            'type' => ['required', Rule::in(['single_choice', 'multi_choice'])],
            'order' => 'required|integer|min:1',
            'options' => 'required|array|min:2',
            'options.*.content' => 'required|array|min:1',
            'options.*.content.vi' => 'nullable|string|required_without:options.*.content.en',
            'options.*.content.en' => 'nullable|string|required_without:options.*.content.vi',
            'options.*.score' => 'required|integer|min:1|max:5',
        ]);

        $question = AssessmentQuestion::create([
            'assessment_id' => $assessment->id,
            'content' => $data['content'],
            'type' => $data['type'],
            'order' => $data['order'],
        ]);

        foreach ($data['options'] as $opt) {
            AssessmentOption::create([
                'question_id' => $question->id,
                'content' => $opt['content'],
                'score' => $opt['score'],
            ]);
        }

        return $this->redirectToShow($request, $assessment)
            ->with('success', 'Question added successfully!');
    }

    public function editQuestion()
    {
        $assessment = $this->resolveAssessment();
        $question = $this->resolveQuestion();

        $question->load('options');

        abort_unless((int) $question->assessment_id === (int) $assessment->id, 404);

        $routeName = (string) optional(request()->route())->getName();
        if (str_starts_with($routeName, 'consultant.')) {
            return view('consultant.assessments.question-edit', compact('assessment', 'question'));
        }

        return view('admin.assessments.question-edit', compact('assessment', 'question'));
    }

    public function updateQuestion(Request $request)
    {
        $assessment = $this->resolveAssessment();
        $question = $this->resolveQuestion();

        abort_unless((int) $question->assessment_id === (int) $assessment->id, 404);

        $data = $request->validate([
            'content' => 'required|array|min:1',
            'content.vi' => 'nullable|string|required_without:content.en',
            'content.en' => 'nullable|string|required_without:content.vi',
            'type' => ['required', Rule::in(['single_choice', 'multi_choice'])],
            'order' => 'required|integer|min:1',
            'options' => 'required|array|min:2',
            'options.*.content' => 'required|array|min:1',
            'options.*.content.vi' => 'nullable|string|required_without:options.*.content.en',
            'options.*.content.en' => 'nullable|string|required_without:options.*.content.vi',
            'options.*.score' => 'required|integer|min:1|max:5',
        ]);

        $question->update([
            'content' => $data['content'],
            'type' => $data['type'],
            'order' => $data['order'],
        ]);

        $question->options()->delete();
        foreach ($data['options'] as $opt) {
            AssessmentOption::create([
                'question_id' => $question->id,
                'content' => $opt['content'],
                'score' => $opt['score'],
            ]);
        }

        return $this->redirectToShow($request, $assessment)
            ->with('success', 'Question updated successfully!');
    }

    public function destroyQuestion(Request $request)
    {
        $assessment = $this->resolveAssessment();
        $question = $this->resolveQuestion();

        abort_unless((int) $question->assessment_id === (int) $assessment->id, 404);

        $question->options()->delete();
        $question->delete();

        return $this->redirectToShow($request, $assessment)
            ->with('success', 'Question deleted successfully!');
    }

    public function destroy()
    {
        $assessment = $this->resolveAssessment();
        $assessment->questions()->each(function ($question) {
            $question->options()->delete();
            $question->delete();
        });
        $assessment->delete();

        return $this->redirectToIndex(request());
            // ->with('success', 'Assessment deleted successfully!');
    }

    public function requestTranslation()
    {
        $assessment = $this->resolveAssessment();
        if ($assessment->status !== 'created') {
            return back()->with('error', 'Only assessments with "created" status can be sent for translation.');
        }

        $assessment->update(['status' => 'created']); // Status stays 'created' but notifies translator

        return back()->with('success', 'Translation request sent to translator!');
    }

    public function approveAndPublish()
    {
        $assessment = $this->resolveAssessment();
        if ($assessment->status !== 'reviewed') {
            return back()->with('error', 'Only reviewed assessments can be published.');
        }

        $assessment->update(['status' => 'active']);

        return back()->with('success', 'Assessment published successfully!');
    }

    public function markAsSpecial()
    {
        $assessment = $this->resolveAssessment();
        if ($assessment->status !== 'reviewed') {
            return back()->with('error', 'Only reviewed assessments can be marked as special.');
        }

        $assessment->update(['status' => 'special']);

        return back()->with('success', 'Assessment marked as special!');
    }

    public function markAsReviewed()
    {
        $assessment = $this->resolveAssessment();
        if ($assessment->status !== 'translated') {
            return back()->with('error', 'Only translated assessments can be reviewed.');
        }

        $assessment->update(['status' => 'reviewed']);

        return back()->with('success', 'Assessment marked as reviewed!');
    }

    public function userResults()
    {
        $assessment = $this->resolveAssessment();
        $userAssessments = UserAssessment::where('assessment_id', $assessment->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter results based on submission mode and user role
        $filteredResults = $userAssessments->map(function ($userAssessment) {
            $result = $userAssessment->toArray();
            
            // Hide private results from non-admin users
            if ($userAssessment->submission_mode === 'self_review' && Auth::user()->role !== 'admin') {
                $result['total_score'] = 'Private';
                $result['answers'] = null;
                $result['is_private'] = true;
            } else {
                $result['is_private'] = false;
            }
            
            return $result;
        });

        return view('admin.assessments.results', compact('assessment', 'filteredResults'));
    }
}
