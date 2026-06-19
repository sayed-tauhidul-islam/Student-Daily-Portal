<?php

namespace App\Http\Controllers\TeacherAdmin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Notice;
use App\Models\ParentPortalAccess;
use App\Models\PaymentConfirmation;
use App\Models\Student;
use App\Models\StudentExamResult;
use App\Models\StudentProgress;
use App\Models\StudentTask;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeacherAdminStudentController extends Controller
{
    public function index(Request $request): View
    {
        $school = $this->schoolName();
        $search = trim((string) $request->query('q', ''));

        $students = Student::query()->get()->filter(function (Student $student) use ($school, $search) {
            if (! $this->belongsToSchool((string) ($student->school ?? ''), $school)) {
                return false;
            }

            if ($search === '') {
                return true;
            }

            $user = User::find($student->user_id);
            $needle = strtolower($search);

            return str_contains(strtolower((string) ($user?->name ?? '')), $needle)
                || str_contains(strtolower((string) ($user?->email ?? '')), $needle)
                || str_contains(strtolower((string) ($student->class ?? '')), $needle)
                || str_contains(strtolower((string) ($student->group ?? '')), $needle)
                || str_contains(strtolower((string) ($student->area ?? '')), $needle)
                || str_contains(strtolower((string) ($student->subject ?? '')), $needle);
        })->sortByDesc('created_at')->values();

        $students = $this->paginateCollection($students, $request, 8);

        return view('teacher_admin.students.index', [
            'students' => $students,
            'search' => $search,
            'school' => $school,
            'panel' => $this->panel(),
        ]);
    }

    public function edit(Student $student): View
    {
        $this->ensureSchoolAccess($student->school ?? null);

        return view('teacher_admin.students.form', [
            'student' => $student,
            'user' => User::find($student->user_id),
            'school' => $this->schoolName(),
            'panel' => $this->panel(),
            'action' => route($this->routeName('students.update'), $student),
            'method' => 'PUT',
        ]);
    }

    public function show(Student $student): View
    {
        $this->ensureSchoolAccess($student->school ?? null);

        $attendance = Attendance::query()
            ->where('student_user_id', (string) $student->user_id)
            ->orderBy('date', 'desc')
            ->get()
            ->filter(fn ($record) => $this->belongsToSchool((string) ($record->institute ?? ''), $this->schoolName()))
            ->values();
        $progress = StudentProgress::query()->firstWhere('student_user_id', $this->progressKey($student));
        $examResults = StudentExamResult::query()
            ->where('student_user_id', $this->progressKey($student))
            ->orderBy('exam_date', 'desc')
            ->get();
        $payments = PaymentConfirmation::query()
            ->where('school', $this->schoolName())
            ->where('user_id', (string) $student->user_id)
            ->where('type', 'tuition_fee')
            ->orderBy('month', 'desc')
            ->get();
        $tasks = StudentTask::query()
            ->where('user_id', (string) $student->user_id)
            ->orderBy('due_date')
            ->get();
        $notices = Notice::query()
            ->where('target_user_id', (string) $student->user_id)
            ->orderBy('published_at', 'desc')
            ->get()
            ->filter(fn (Notice $notice) => $this->belongsToSchool((string) ($notice->institute ?? ''), $this->schoolName()))
            ->values();
        $guardian = ParentPortalAccess::query()->firstWhere('student_user_id', (string) $student->user_id);

        return view('teacher_admin.students.show', [
            'student' => $student,
            'user' => User::find($student->user_id),
            'attendance' => $attendance,
            'progress' => $progress,
            'examResults' => $examResults,
            'payments' => $payments,
            'tasks' => $tasks,
            'notices' => $notices,
            'guardian' => $guardian,
            'school' => $this->schoolName(),
            'panel' => $this->panel(),
        ]);
    }

    public function storeFee(Request $request, Student $student): RedirectResponse
    {
        $this->ensureSchoolAccess($student->school ?? null);

        $data = $request->validate([
            'month' => ['required', 'string', 'max:30'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        PaymentConfirmation::query()->updateOrCreate(
            ['user_id' => (string) $student->user_id, 'type' => 'tuition_fee', 'month' => $data['month']],
            [
                'school' => $this->schoolName(),
                'role' => 'student',
                'amount' => $data['amount'],
                'status' => $data['status'],
                'submitted_by' => (string) Auth::id(),
                'submitted_at' => now(),
                'confirmed_by' => (string) Auth::id(),
                'confirmed_at' => $data['status'] === 'approved' ? now() : null,
                'note' => $data['note'] ?? null,
            ]
        );

        return back()->with('success', 'Student monthly fee saved.');
    }

    public function updateFee(Request $request, Student $student, PaymentConfirmation $payment): RedirectResponse
    {
        $this->ensureSchoolAccess($student->school ?? null);
        abort_unless((string) $payment->user_id === (string) $student->user_id && $this->belongsToSchool((string) $payment->school, $this->schoolName()), 403);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->fill([
            'amount' => $data['amount'],
            'status' => $data['status'],
            'confirmed_by' => (string) Auth::id(),
            'confirmed_at' => $data['status'] === 'approved' ? now() : null,
            'note' => $data['note'] ?? null,
        ])->save();

        return back()->with('success', 'Student fee updated.');
    }

    public function storeNotice(Request $request, Student $student): RedirectResponse
    {
        $this->ensureSchoolAccess($student->school ?? null);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        Notice::query()->create([
            'institute' => $this->schoolName(),
            'teacher_user_id' => (string) Auth::id(),
            'target_user_id' => (string) $student->user_id,
            'target_type' => 'student',
            'title' => $data['title'],
            'body' => $data['body'],
            'published_at' => now(),
        ]);

        return back()->with('success', 'Special notice sent to this student.');
    }

    public function storeTask(Request $request, Student $student): RedirectResponse
    {
        $this->ensureSchoolAccess($student->school ?? null);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['nullable', 'in:low,medium,high'],
        ]);

        StudentTask::query()->create([
            'user_id' => (string) $student->user_id,
            'title' => $data['title'],
            'due_date' => $data['due_date'] ?? null,
            'priority' => $data['priority'] ?? 'medium',
            'is_completed' => false,
        ]);

        return back()->with('success', 'Special task assigned to this student.');
    }

    public function create(): View
    {
        return view('teacher_admin.students.form', [
            'student' => new Student(),
            'user' => null,
            'school' => $this->schoolName(),
            'panel' => $this->panel(),
            'action' => route($this->routeName('students.store')),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'class' => ['required', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:1000'],
            'preferred_teacher' => ['nullable', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        if (! empty($data['email']) && User::query()->where('email', $data['email'])->exists()) {
            return back()
                ->withErrors(['email' => 'This email is already registered.'])
                ->withInput();
        }

        $school = $this->schoolName();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'student',
            'school' => $school,
            'phone' => $data['phone'] ?? null,
            'area' => $data['area'],
        ]);

        Student::query()->create([
            'user_id' => $user->getKey(),
            'class' => $data['class'],
            'group' => $data['group'] ?? null,
            'school' => $school,
            'subject' => $data['subject'] ?? null,
            'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subject'] ?? ''))))),
            'preferred_teacher' => $data['preferred_teacher'] ?? null,
            'area' => $data['area'],
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        return redirect()->route($this->routeName('students.index'))->with('success', 'Student added successfully.');
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $this->ensureSchoolAccess($student->school ?? null);

        $user = User::find($student->user_id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'class' => ['required', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:1000'],
            'preferred_teacher' => ['nullable', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        $imagePath = $user?->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('student-images', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        if (! $user && ! empty($data['email'])) {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?: str()->random(16)),
                'role' => 'student',
                'school' => $student->school ?? $this->schoolName(),
                'image' => $imagePath,
                'phone' => $data['phone'] ?? null,
                'area' => $data['area'],
            ]);
            $student->user_id = $user->getKey();
        }

        $school = $this->schoolName();

        if ($user) {
            $user->name = $data['name'];
            if (! empty($data['email'])) {
                $user->email = $data['email'];
            }
            $user->school = $school;
            if (! empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            $user->image = $imagePath;
            $user->phone = $data['phone'] ?? null;
            $user->area = $data['area'];
            $user->save();
        }

        $student->update([
            'class' => $data['class'],
            'group' => $data['group'] ?? null,
            'school' => $school,
            'subject' => $data['subject'] ?? null,
            'subjects' => array_values(array_filter(array_map('trim', explode(',', (string) ($data['subject'] ?? ''))))),
            'preferred_teacher' => $data['preferred_teacher'] ?? null,
            'area' => $data['area'],
            'phone' => $data['phone'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        return redirect()->route($this->routeName('students.index'))->with('success', 'Student updated.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->ensureSchoolAccess($student->school ?? null);

        $user = User::find($student->user_id);
        if ($user?->image) {
            Storage::disk('public')->delete($user->image);
        }

        $student->delete();
        $user?->delete();

        return back()->with('success', 'Student deleted.');
    }

    private function schoolName(): string
    {
        if ((Auth::user()?->role ?? '') === 'teacher') {
            return trim((string) (Teacher::query()->firstWhere('user_id', Auth::id())?->institution ?? Auth::user()?->school ?? ''));
        }

        return trim((string) (Auth::user()?->school ?? ''));
    }

    private function panel(): string
    {
        return (Auth::user()?->role ?? '') === 'teacher_admin' ? 'teacher-admin' : 'teacher';
    }

    private function routeName(string $name): string
    {
        return $this->panel().'.'.$name;
    }

    private function progressKey(Student $student): string
    {
        $userId = trim((string) ($student->user_id ?? ''));

        return $userId !== '' ? $userId : 'student:'.$student->getKey();
    }

    private function ensureSchoolAccess(?string $school): void
    {
        if (! $this->belongsToSchool((string) $school, $this->schoolName())) {
            abort(403, 'This student does not belong to your school.');
        }
    }

    private function belongsToSchool(string $value, string $school): bool
    {
        $value = $this->normalize($value);
        $school = $this->normalize($school);

        if ($value === '' || $school === '') {
            return false;
        }

        return $value === $school || str_contains($value, $school) || str_contains($school, $value);
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(['&'], ' and ', $value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }

    private function paginateCollection($items, Request $request, int $perPage = 8): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $items->values();

        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return $paginator->appends($request->query());
    }
}
