<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentRequest;
use App\Models\Teacher;
use App\Support\Lists\ClassList;
use App\Support\Lists\GroupList;
use App\Support\Lists\SchoolList;
use App\Support\Lists\SubjectList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\PostRequested;
use App\Models\User;
use Illuminate\View\View;

class StudentRequestController extends Controller
{
    public function index(): View
    {
        $requests = StudentRequest::where('user_id', Auth::id())->orderBy('created_at', -1)->get();
        $feed = collect();

        return view('student.requests', [
            'requests' => $requests,
            'feed' => $feed,
            'schools' => SchoolList::names(),
            'subjects' => SubjectList::allNames(),
            'classes' => ClassList::all(),
            'groups' => GroupList::all(),
            'areas' => SchoolList::areas(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'class_level' => ['required', 'string', 'max:50'],
            'group' => ['nullable', 'string', 'max:100'],
            'school' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'budget' => ['required', 'integer', 'min:0'],
            'gender' => ['nullable', 'string', 'max:50'],
            'online' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:30'],
            'deadline' => ['nullable', 'date'],
            'description' => ['required', 'string', 'max:4000'],
        ]);

        StudentRequest::create([
            'user_id' => Auth::id(),
            'student_name' => Auth::user()?->name,
            'title' => $request->title,
            'class_level' => $request->class_level,
            'group' => $request->group,
            'school' => $request->school,
            'subject' => $request->subject,
            'area' => $request->area,
            'budget' => (int) $request->budget,
            'gender' => $request->gender,
            'online' => $request->boolean('online'),
            'phone' => $request->phone,
            'description' => $request->description,
            'status' => 'pending',
            'applications' => [],
            'is_featured' => false,
            'deadline' => $request->deadline,
        ]);

        return redirect()->route('student.requests.index')->with('success', 'Tuition request posted successfully.');
    }

    public function apply(Request $request, StudentRequest $tuitionRequest): RedirectResponse
    {
        $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        abort_unless($tuitionRequest->status === 'pending', 403);
        $applications = collect($tuitionRequest->applications ?? []);
        abort_if($applications->contains(fn (array $application) => (string) ($application['teacher_id'] ?? '') === (string) Auth::id()), 422, 'You have already applied to this request.');
        $applications->push([
            'teacher_id' => Auth::id(),
            'teacher_name' => Auth::user()?->name,
            'message' => $request->message,
            'applied_at' => now()->toDateTimeString(),
        ]);

        $tuitionRequest->applications = $applications->values()->all();
        $tuitionRequest->status = $tuitionRequest->status ?: 'pending';
        $tuitionRequest->save();

        return back()->with('success', 'Application sent to the tuition request.');
    }

    public function applyToPost(Request $request, \App\Models\TeacherPost $post): RedirectResponse
    {
        $request->validate([
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $sr = StudentRequest::create([
            'user_id' => Auth::id(),
            'teacher_id' => $post->user_id,
            'post_id' => $post->getKey(),
            'student_name' => Auth::user()?->name,
            'title' => 'Request for: ' . ($post->title ?? 'Teaching post'),
            'class_level' => '',
            'group' => null,
            'school' => null,
            'subject' => $post->category ?? null,
            'area' => null,
            'budget' => 0,
            'gender' => null,
            'online' => false,
            'phone' => Auth::user()?->phone ?? null,
            'description' => $request->message ?? ('Interested in your post: ' . ($post->title ?? '')),
            'status' => 'pending',
            'applications' => [],
            'is_featured' => false,
        ]);

        // try to notify the teacher by email and in-app notification
        $teacherUser = User::find($post->user_id);
        if ($teacherUser) {
            if (! empty($teacherUser->email)) {
                try {
                    // Queue email (reliable in production when queue worker is running)
                    Mail::to($teacherUser->email)->queue(new PostRequested($post, Auth::user(), $sr));
                } catch (\Throwable $e) {
                    // fail silently — email config or queue might not be set in local dev
                }
            }

            try {
                // Queue notification (PostRequestedNotification implements ShouldQueue)
                $teacherUser->notify(new \App\Notifications\PostRequestedNotification($sr, $post));
            } catch (\Throwable $e) {
                // ignore notification errors in local dev
            }

        }

        return back()->with('success', 'Request sent to the teacher.');
    }

    public function approve(Request $request, StudentRequest $studentRequest): RedirectResponse
    {
        abort_unless($studentRequest->status === 'pending', 403);
        abort_unless(collect($studentRequest->applications ?? [])->contains(fn (array $application) => (string) ($application['teacher_id'] ?? '') === (string) Auth::id()), 403);
        $studentRequest->teacher_id = Auth::id();
        $studentRequest->status = 'approved';
        $studentRequest->save();

        return back()->with('success', 'Student request approved.');
    }
}
