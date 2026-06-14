<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BlockedIdentity;
use App\Models\Complaint;
use App\Models\LeaveApplication;
use App\Models\LoginReview;
use App\Models\Message;
use App\Models\Notice;
use App\Models\PaymentConfirmation;
use App\Models\ReadingLog;
use App\Models\Student;
use App\Models\StudentProgress;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolPortalController extends Controller
{
    public function studentSchool(): View
    {
        $school = $this->currentSchool();
        [$students, $teachers, $usersById] = $this->schoolMembers($school);

        return view('portal.school-members', [
            'roleView' => 'student',
            'school' => $school,
            'students' => $students,
            'teachers' => $teachers,
            'usersById' => $usersById,
            'classmates' => $students->where('class', optional(Student::firstWhere('user_id', Auth::id()))->class)->values(),
        ]);
    }

    public function messages(Request $request): View
    {
        $school = $this->currentSchool();
        [$students, $teachers, $usersById] = $this->schoolMembers($school);
        $role = Auth::user()?->role;
        $search = trim((string) $request->query('q', ''));

        $contacts = $usersById
            ->values()
            ->filter(fn ($user) => (string) $user->getKey() !== (string) Auth::id())
            ->filter(function ($user) use ($search) {
                if ($search === '') {
                    return true;
                }

                $haystack = strtolower((string) ($user->name ?? '').' '.(string) ($user->email ?? '').' '.str_replace('_', ' ', (string) ($user->role ?? '')));

                return str_contains($haystack, strtolower($search));
            })
            ->values();

        if ($contacts->isEmpty() && $school === '' && $search === '') {
            $contacts = User::query()
                ->whereIn('role', ['student', 'teacher', 'teacher_admin'])
                ->where('_id', '!=', Auth::id())
                ->get()
                ->values();
        }

        $receiver = $request->query('with') ? User::find($request->query('with')) : $contacts->first();
        $peer = null;
        $messages = collect();

        if ($receiver && $role === 'teacher_admin' && filled($request->query('peer'))) {
            $peer = User::find($request->query('peer'));
            if ($peer) {
                $pairIds = [(string) $receiver->getKey(), (string) $peer->getKey()];
                $messages = Message::query()
                    ->whereIn('sender_id', $pairIds)
                    ->whereIn('receiver_id', $pairIds)
                    ->orderBy('created_at')
                    ->get()
                    ->filter(fn ($message) => in_array((string) $message->sender_id, $pairIds, true) && in_array((string) $message->receiver_id, $pairIds, true))
                    ->filter(fn ($message) => $this->messageVisibleForViewer($message, (string) Auth::id(), (string) ($role ?? '')))
                    ->values();
            }
        } elseif ($receiver) {
            $ids = [(string) Auth::id(), (string) $receiver->getKey()];
            $messages = Message::query()
                ->whereIn('sender_id', $ids)
                ->whereIn('receiver_id', $ids)
                ->orderBy('created_at')
                ->get()
                ->filter(fn ($message) => in_array((string) $message->sender_id, $ids, true) && in_array((string) $message->receiver_id, $ids, true))
                ->filter(fn ($message) => $this->messageVisibleForViewer($message, (string) Auth::id(), (string) ($role ?? '')))
                ->values();

            Message::query()
                ->where('sender_id', (string) $receiver->getKey())
                ->where('receiver_id', (string) Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        return view('portal.messages', compact('contacts', 'receiver', 'peer', 'messages', 'school', 'search'));
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'receiver_id' => ['required', 'string'],
            'body' => ['required', 'string', 'max:4000'],
        ]);

        $receiver = User::findOrFail($data['receiver_id']);
        $this->authorizeConversation($receiver);
        $ids = [(string) Auth::id(), (string) $receiver->getKey()];
        sort($ids);

        Message::create([
            'conversation_id' => implode(':', $ids),
            'sender_id' => (string) Auth::id(),
            'receiver_id' => (string) $receiver->getKey(),
            'body' => $data['body'],
        ]);

        return back()->with('success', 'Message sent.');
    }

    public function updateMessage(Request $request, Message $message): RedirectResponse
    {
        $viewerId = (string) Auth::id();
        $viewerRole = (string) (Auth::user()?->role ?? '');
        abort_unless($this->canViewConversationMessage($message, $viewerId, $viewerRole), 403);
        abort_unless((string) $message->sender_id === $viewerId, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:4000'],
        ]);

        if (! empty($message->deleted_for_everyone_at)) {
            return back()->with('error', 'This message was removed for everyone and can no longer be edited.');
        }

        $message->body = $data['body'];
        $message->edited_at = now();
        $message->edited_by = $viewerId;
        $message->save();

        return back()->with('success', 'Message updated.');
    }

    public function deleteMessage(Request $request, Message $message): RedirectResponse
    {
        $viewerId = (string) Auth::id();
        $viewerRole = (string) (Auth::user()?->role ?? '');
        abort_unless($this->canViewConversationMessage($message, $viewerId, $viewerRole), 403);

        $deleteScope = $request->string('delete_scope')->toString();
        $isSender = (string) $message->sender_id === $viewerId;
        $isReceiver = (string) $message->receiver_id === $viewerId;

        abort_unless($isSender || $isReceiver, 403);

        if ($deleteScope === 'everyone') {
            abort_unless($isSender, 403);
            $message->deleted_for_everyone_at = now();
            $message->deleted_for_everyone_by = $viewerId;
            $message->save();

            return back()->with('success', 'Message removed for everyone.');
        }

        if ($isSender) {
            $message->sender_deleted_at = now();
        } elseif ($isReceiver) {
            $message->receiver_deleted_at = now();
        }

        $message->save();

        return back()->with('success', 'Message removed from your view.');
    }

    public function complaints(): View
    {
        $school = $this->currentSchool();
        [$students, $teachers, $usersById] = $this->schoolMembers($school);
        $role = Auth::user()?->role;
        $complaints = Complaint::query()->where('school', $school)->orderBy('created_at', 'desc')->get();

        if ($role !== 'teacher_admin') {
            $complaints = $complaints->filter(fn ($item) => (string) $item->created_by === (string) Auth::id())->values();
        }

        return view('portal.complaints', compact('school', 'students', 'teachers', 'usersById', 'complaints', 'role'));
    }

    public function storeComplaint(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'against_user_id' => ['nullable', 'string'],
            'against_name' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $against = $data['against_user_id'] ? User::find($data['against_user_id']) : null;
        $creatorRole = Auth::user()?->role;

        if ($creatorRole === 'student') {
            if ($against) {
                abort_unless(in_array($against->role, ['student', 'teacher'], true), 403, 'Students can complain against students or teachers only.');
            }

            abort_if($against && $against->role === 'teacher_admin', 403, 'Students cannot complain against Head Sir.');
        }

        Complaint::create([
            'school' => $this->currentSchool(),
            'created_by' => (string) Auth::id(),
            'creator_role' => $creatorRole,
            'against_user_id' => $against ? (string) $against->getKey() : null,
            'against_name' => $against?->name ?? $data['against_name'] ?? 'Not specified',
            'against_role' => $against?->role,
            'title' => $data['title'],
            'body' => $data['body'],
            'status' => 'open',
        ]);

        return back()->with('success', 'Complaint submitted.');
    }

    public function updateComplaint(Request $request, Complaint $complaint): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'teacher_admin' && $this->sameSchool($complaint->school), 403);
        $data = $request->validate([
            'status' => ['required', 'in:open,reviewing,resolved,rejected'],
            'action_note' => ['nullable', 'string', 'max:3000'],
        ]);

        $complaint->fill($data + ['action_by' => (string) Auth::id()]);
        $complaint->save();

        if (! empty($data['action_note'])) {
            Notice::create([
                'institute' => $complaint->school,
                'teacher_user_id' => (string) Auth::id(),
                'title' => 'Complaint action: '.$complaint->title,
                'body' => $data['action_note'],
                'published_at' => now(),
            ]);
        }

        return back()->with('success', 'Complaint action saved and notice published.');
    }

    public function leaves(): View
    {
        $school = $this->currentSchool();
        $query = LeaveApplication::query()->where('school', $school)->orderBy('created_at', 'desc');
        $leaves = Auth::user()?->role === 'teacher_admin'
            ? $query->get()
            : $query->where('user_id', (string) Auth::id())->get();
        $users = User::query()->whereIn('_id', $leaves->pluck('user_id')->filter()->values()->all())->get()
            ->mapWithKeys(fn ($user) => [(string) $user->getKey() => $user]);

        return view('portal.leaves', compact('leaves', 'users', 'school'));
    }

    public function storeLeave(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'leave_type' => ['required', 'in:advance,absence'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'reason' => ['required', 'string', 'max:3000'],
            'document' => ['required', 'file', 'mimes:pdf,docx', 'max:5120'],
        ]);

        $path = $request->file('document')->store('leave-documents', 'public');

        LeaveApplication::create([
            'school' => $this->currentSchool(),
            'user_id' => (string) Auth::id(),
            'role' => Auth::user()?->role,
            'leave_type' => $data['leave_type'],
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
            'reason' => $data['reason'],
            'document_path' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Leave application uploaded.');
    }

    public function updateLeave(Request $request, LeaveApplication $leave): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'teacher_admin' && $this->sameSchool($leave->school), 403);
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $leave->fill($data + ['reviewed_by' => (string) Auth::id()]);
        $leave->save();

        return back()->with('success', 'Leave status updated.');
    }

    public function payments(): View
    {
        $school = $this->currentSchool();
        [$students, $teachers, $usersById] = $this->schoolMembers($school);
        $payments = PaymentConfirmation::query()->where('school', $school)->orderBy('created_at', 'desc')->get();

        if (Auth::user()?->role !== 'teacher_admin') {
            $payments = $payments->where('user_id', (string) Auth::id())->values();
        }

        return view('portal.payments', compact('school', 'students', 'teachers', 'usersById', 'payments'));
    }

    public function submitPayment(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'student', 403);

        $data = $request->validate([
            'month' => ['required', 'string', 'max:30'],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        PaymentConfirmation::updateOrCreate(
            ['user_id' => (string) Auth::id(), 'type' => 'tuition_fee', 'month' => $data['month']],
            [
                'school' => $this->currentSchool(),
                'role' => 'student',
                'amount' => $data['amount'],
                'status' => 'pending',
                'submitted_by' => (string) Auth::id(),
                'submitted_at' => now(),
                'confirmed_by' => null,
                'confirmed_at' => null,
                'receiver_confirmed_at' => null,
                'note' => $data['note'] ?? null,
            ]
        );

        return back()->with('success', 'Tuition fee submitted for Head Sir approval.');
    }

    public function storePayment(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'teacher_admin', 403);
        $data = $request->validate([
            'user_id' => ['required', 'string'],
            'type' => ['required', 'in:tuition_fee,salary'],
            'month' => ['required', 'string', 'max:30'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $user = User::findOrFail($data['user_id']);

        PaymentConfirmation::updateOrCreate(
            ['user_id' => (string) $user->getKey(), 'type' => $data['type'], 'month' => $data['month']],
            [
                'school' => $this->currentSchool(),
                'role' => $user->role,
                'amount' => $data['amount'] ?? null,
                'status' => 'approved',
                'confirmed_by' => (string) Auth::id(),
                'confirmed_at' => now(),
                'note' => $data['note'] ?? null,
            ]
        );

        return back()->with('success', 'Payment confirmation saved.');
    }

    public function approvePayment(Request $request, PaymentConfirmation $payment): RedirectResponse
    {
        abort_unless(Auth::user()?->role === 'teacher_admin' && $this->sameSchool((string) $payment->school), 403);

        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->status = $data['status'];
        $payment->confirmed_by = (string) Auth::id();
        $payment->confirmed_at = $data['status'] === 'approved' ? now() : null;
        if (array_key_exists('note', $data)) {
            $payment->note = $data['note'];
        }
        $payment->save();

        return back()->with('success', $data['status'] === 'approved' ? 'Tuition fee approved.' : 'Tuition fee rejected.');
    }

    public function confirmReceived(PaymentConfirmation $payment): RedirectResponse
    {
        abort_unless((string) $payment->user_id === (string) Auth::id(), 403);
        $payment->receiver_confirmed_at = now();
        $payment->save();

        return back()->with('success', 'Received confirmation saved.');
    }

    public function readingLogs(): View
    {
        $school = $this->currentSchool();
        $logs = ReadingLog::query()
            ->when(Auth::user()?->role === 'student', fn ($query) => $query->where('student_user_id', (string) Auth::id()))
            ->when(Auth::user()?->role !== 'student', fn ($query) => $query->where('school', $school))
            ->orderBy('read_date', 'desc')
            ->get();
        $usersById = User::query()->whereIn('_id', $logs->pluck('student_user_id')->filter()->unique()->values()->all())->get()
            ->mapWithKeys(fn ($user) => [(string) $user->getKey() => $user]);

        return view('portal.reading-logs', compact('logs', 'usersById'));
    }

    public function storeReadingLog(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'book_name' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'read_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $start = Carbon::parse($data['read_date'].' '.$data['start_time']);
        $end = Carbon::parse($data['read_date'].' '.$data['end_time']);
        if ($end->lessThan($start)) {
            $end->addDay();
        }

        ReadingLog::create($data + [
            'student_user_id' => (string) Auth::id(),
            'school' => $this->currentSchool(),
            'duration_minutes' => $start->diffInMinutes($end),
        ]);

        return back()->with('success', 'Reading time saved.');
    }

    public function headSearch(Request $request): View
    {
        abort_unless(Auth::user()?->role === 'teacher_admin', 403);
        $school = $this->currentSchool();
        $term = trim((string) $request->query('q'));
        [$students, $teachers, $usersById] = $this->schoolMembers($school);
        $results = collect();

        if ($term !== '') {
            $results = $usersById->values()->filter(fn ($user) => str_contains(strtolower($user->name.' '.$user->email), strtolower($term)))->values();
        }

        $progress = StudentProgress::query()->whereIn('student_user_id', $results->pluck('_id')->map(fn ($id) => (string) $id)->all())->get()
            ->mapWithKeys(fn ($item) => [(string) $item->student_user_id => $item]);
        $attendance = Attendance::query()->where('institute', $school)->orderBy('date', 'desc')->get();

        return view('portal.head-search', compact('school', 'term', 'results', 'students', 'teachers', 'progress', 'attendance'));
    }

    public function loginReviews(): View
    {
        abort_unless(in_array(Auth::user()?->role, ['admin', 'teacher_admin'], true), 403);
        $query = LoginReview::query()->orderBy('created_at', 'desc');
        if (Auth::user()?->role === 'teacher_admin') {
            $query->where('school', $this->currentSchool());
        }

        return view('portal.login-reviews', ['reviews' => $query->get()]);
    }

    public function blockLogin(LoginReview $review): RedirectResponse
    {
        abort_unless(in_array(Auth::user()?->role, ['admin', 'teacher_admin'], true), 403);
        if (Auth::user()?->role === 'teacher_admin') {
            abort_unless($this->sameSchool($review->school), 403);
        }

        $review->fill(['status' => 'blocked', 'blocked_at' => now(), 'blocked_by' => (string) Auth::id()]);
        $review->save();
        $user = User::find($review->user_id);
        if ($user) {
            $user->status = 'blocked';
            $user->blocked_at = now();
            $user->save();
        }
        BlockedIdentity::updateOrCreate(
            ['email' => strtolower((string) $review->email), 'school' => (string) $review->school],
            ['role' => $review->role, 'user_id' => $review->user_id, 'reason' => 'Blocked from login review', 'blocked_by' => (string) Auth::id(), 'blocked_at' => now()]
        );

        return back()->with('success', 'User blocked for this school.');
    }

    public function unblockLogin(LoginReview $review): RedirectResponse
    {
        abort_unless(in_array(Auth::user()?->role, ['admin', 'teacher_admin'], true), 403);
        $review->fill(['status' => 'allowed', 'blocked_at' => null, 'blocked_by' => null]);
        $review->save();
        $user = User::find($review->user_id);
        if ($user) {
            $user->status = 'active';
            $user->blocked_at = null;
            $user->save();
        }
        BlockedIdentity::query()->where('email', strtolower((string) $review->email))->where('school', (string) $review->school)->delete();

        return back()->with('success', 'Block removed.');
    }

    public function deleteLogin(Request $request, LoginReview $review): RedirectResponse
    {
        abort_unless(in_array(Auth::user()?->role, ['admin', 'teacher_admin'], true), 403);
        if (Auth::user()?->role === 'teacher_admin') {
            abort_unless($this->sameSchool($review->school), 403);
        }

        $deleteLinkedUser = $request->boolean('delete_user');
        if ($deleteLinkedUser) {
            $user = User::find($review->user_id);
            if ($user) {
                $user->delete();
            }
        }

        $review->delete();

        return back()->with('success', $deleteLinkedUser ? 'Login record and linked user deleted.' : 'Login record deleted.');
    }

    private function authorizeConversation(User $receiver): void
    {
        $senderRole = Auth::user()?->role;
        $receiverRole = $receiver->role;

        abort_unless(in_array($senderRole, ['student', 'teacher', 'teacher_admin'], true), 403);
        abort_unless(in_array($receiverRole, ['student', 'teacher', 'teacher_admin'], true), 403);
        abort_unless((string) Auth::id() !== (string) $receiver->getKey(), 403);
        abort_unless($this->schoolMatches($this->receiverSchool($receiver), $this->currentSchool()), 403);
    }

    private function canViewConversationMessage(Message $message, string $viewerId, string $viewerRole): bool
    {
        $inConversation = in_array($viewerRole, ['teacher_admin', 'admin'], true)
            || (string) $message->sender_id === $viewerId
            || (string) $message->receiver_id === $viewerId;

        if (! $inConversation) {
            return false;
        }

        if (! empty($message->deleted_for_everyone_at)) {
            return false;
        }

        return true;
    }

    private function messageVisibleForViewer(Message $message, string $viewerId, string $viewerRole): bool
    {
        if (! $this->canViewConversationMessage($message, $viewerId, $viewerRole)) {
            return false;
        }

        if (in_array($viewerRole, ['teacher_admin', 'admin'], true)) {
            return true;
        }

        if ((string) $message->sender_id === $viewerId && ! empty($message->sender_deleted_at)) {
            return false;
        }

        if ((string) $message->receiver_id === $viewerId && ! empty($message->receiver_deleted_at)) {
            return false;
        }

        return true;
    }

    private function currentSchool(): string
    {
        $user = Auth::user();
        if (($user?->role ?? '') === 'student') {
            return (string) (Student::firstWhere('user_id', Auth::id())?->school ?? $user?->school ?? '');
        }
        if (($user?->role ?? '') === 'teacher') {
            return (string) (Teacher::firstWhere('user_id', Auth::id())?->institution ?? $user?->school ?? '');
        }

        return (string) ($user?->school ?? '');
    }

    private function schoolMembers(string $school): array
    {
        $students = Student::query()
            ->when($school !== '', fn ($query) => $query->where('school', $school))
            ->get()
            ->filter(fn ($student) => $this->schoolMatches((string) $student->school, $school))
            ->values();

        $teachers = Teacher::query()
            ->when($school !== '', fn ($query) => $query->where('institution', $school))
            ->get()
            ->filter(fn ($teacher) => $this->schoolMatches((string) $teacher->institution, $school))
            ->values();

        $headTeachers = User::query()
            ->where('role', 'teacher_admin')
            ->when($school !== '', fn ($query) => $query->where('school', $school))
            ->get()
            ->filter(fn ($user) => $school !== '' && $this->sameSchool((string) $user->school))
            ->values();

        $ids = collect($students->pluck('user_id'))
            ->merge($teachers->pluck('user_id'))
            ->merge($headTeachers->pluck('_id'))
            ->filter()
            ->unique()
            ->values();
        $usersById = User::query()->whereIn('_id', $ids->all())->get()->mapWithKeys(fn ($user) => [(string) $user->getKey() => $user]);

        return [$students, $teachers, $usersById];
    }

    private function receiverSchool(User $receiver): string
    {
        return match ($receiver->role) {
            'student' => (string) (Student::query()->firstWhere('user_id', $receiver->getKey())?->school ?? $receiver->school ?? ''),
            'teacher' => (string) (Teacher::query()->firstWhere('user_id', $receiver->getKey())?->institution ?? $receiver->school ?? ''),
            default => (string) ($receiver->school ?? ''),
        };
    }

    private function sameSchool(string $school): bool
    {
        return $this->schoolMatches($school, $this->currentSchool());
    }

    private function schoolMatches(string $left, string $right): bool
    {
        $left = $this->normalize($left);
        $right = $this->normalize($right);

        return $left !== '' && $right !== '' && ($left === $right || str_contains($left, $right) || str_contains($right, $left));
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }
}
