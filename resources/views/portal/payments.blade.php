<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] app-accent">Payment Board</p>
            <h2 class="mt-2 text-3xl font-black text-[color:var(--app-text)]">Tuition Fee & Salary Confirmation</h2>
            <p class="mt-2 text-sm app-muted">Head authority can confirm monthly fee/salary, users can see status and teachers can confirm received salary.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[0.44fr_0.56fr]">
        @if(auth()->user()?->role === 'student')
            <section class="app-surface rounded-2xl p-5">
                <h3 class="text-xl font-black">Submit Tuition Fee</h3>
                <form method="POST" action="{{ route('student.payments.submit') }}" class="mt-4 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-semibold">Month</label>
                            <input type="text" name="month" required maxlength="30" placeholder="2026-06" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Amount</label>
                            <input type="number" step="0.01" min="0" name="amount" required class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Note</label>
                        <textarea name="note" rows="3" maxlength="1000" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]"></textarea>
                    </div>
                    <button type="submit" class="rounded-full bg-[color:var(--app-primary)] px-5 py-2.5 text-sm font-semibold text-white">Submit Fee</button>
                </form>
            </section>
        @endif

        @if(auth()->user()?->role === 'teacher_admin')
            <section class="app-surface rounded-2xl p-5">
                <h3 class="text-xl font-black">Confirm Payment</h3>
                <form method="POST" action="{{ route('teacher-admin.payments.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold">User</label>
                        <select name="user_id" required class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                            <optgroup label="Students (Tuition)">
                                @foreach($students as $student)
                                    @php $u = $usersById[(string) ($student->user_id ?? '')] ?? null; @endphp
                                    @if($u)
                                        <option value="{{ $u->getKey() }}">{{ $u->name }} (Student)</option>
                                    @endif
                                @endforeach
                            </optgroup>
                            <optgroup label="Teachers (Salary)">
                                @foreach($teachers as $teacher)
                                    @php $u = $usersById[(string) ($teacher->user_id ?? '')] ?? null; @endphp
                                    @if($u)
                                        <option value="{{ $u->getKey() }}">{{ $u->name }} (Teacher)</option>
                                    @endif
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Payment Type</label>
                        <select name="type" required class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                            <option value="tuition_fee">Tuition Fee</option>
                            <option value="salary">Salary</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-semibold">Month</label>
                            <input type="text" name="month" required maxlength="30" placeholder="2026-05" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Amount</label>
                            <input type="number" step="0.01" min="0" name="amount" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Note</label>
                        <textarea name="note" rows="3" maxlength="1000" class="mt-1 w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]"></textarea>
                    </div>
                    <button type="submit" class="rounded-full bg-[color:var(--app-primary)] px-5 py-2.5 text-sm font-semibold text-white">Confirm Payment</button>
                </form>
            </section>
        @endif

        <section class="app-surface rounded-2xl p-5 {{ auth()->user()?->role === 'teacher_admin' || auth()->user()?->role === 'student' ? '' : 'xl:col-span-2' }}">
            <h3 class="text-xl font-black">Payment Records</h3>
            <div class="mt-4 space-y-4">
                @forelse($payments as $payment)
                    @php $u = $usersById[(string) ($payment->user_id ?? '')] ?? null; @endphp
                    @php $status = (string) ($payment->status ?? (!empty($payment->confirmed_at) ? 'approved' : 'pending')); @endphp
                    <div class="rounded-2xl border border-[color:var(--app-border)] p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="font-bold">{{ $u?->name ?? 'User' }} ({{ ucfirst((string) ($payment->role ?? 'unknown')) }})</p>
                                <p class="text-xs app-muted">Type: {{ $payment->type === 'tuition_fee' ? 'Tuition Fee' : 'Salary' }}</p>
                                <p class="text-xs app-muted">Month: {{ $payment->month }}</p>
                                <p class="text-xs app-muted">Amount: {{ $payment->amount !== null ? number_format((float) $payment->amount, 2) : 'N/A' }}</p>
                                <p class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($status === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $status === 'approved' && $payment->type === 'tuition_fee' ? 'Tuition Fee Paid' : ucfirst($status) }}
                                </p>
                            </div>
                            <div class="text-right text-xs app-muted">
                                <p>Submitted: {{ optional($payment->submitted_at)->format('d M Y') ?? 'N/A' }}</p>
                                <p>Confirmed: {{ optional($payment->confirmed_at)->format('d M Y') ?? 'N/A' }}</p>
                                <p>Received: {{ optional($payment->receiver_confirmed_at)->format('d M Y') ?? 'Not yet' }}</p>
                            </div>
                        </div>
                        @if(!empty($payment->note))
                            <p class="mt-3 text-sm">{{ $payment->note }}</p>
                        @endif

                        @if(auth()->user()?->role === 'teacher' && (string) $payment->user_id === (string) auth()->id() && empty($payment->receiver_confirmed_at))
                            <form method="POST" action="{{ route('teacher.payments.received', $payment) }}" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">I Received Salary</button>
                            </form>
                        @endif

                        @if(auth()->user()?->role === 'teacher_admin' && $payment->type === 'tuition_fee' && $status === 'pending')
                            <div class="mt-3 flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('teacher-admin.payments.approve', $payment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Approve Tuition Fee</button>
                                </form>
                                <form method="POST" action="{{ route('teacher-admin.payments.approve', $payment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="rounded-full border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-700">Reject</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-sm app-muted">No payment records available.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
