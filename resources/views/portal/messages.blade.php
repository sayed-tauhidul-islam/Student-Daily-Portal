<x-app-layout x-data="{ menuOpen:false, menuX:0, menuY:0, menuDeleteUrlMe:null, menuDeleteUrlEveryone:null, menuMessageId:null, menuCanEdit:false, menuCanDelete:false, openMenu(event){ const el = event.currentTarget || event.target; const d = el.dataset || {}; this.menuX = (event.clientX || (event.touches && event.touches[0].clientX)) + 8; this.menuY = (event.clientY || (event.touches && event.touches[0].clientY)) + 8; this.menuMessageId = d.id || null; this.menuDeleteUrlMe = d.deleteUrlMe || null; this.menuDeleteUrlEveryone = d.deleteUrlEveryone || null; this.menuCanEdit = (d.canEdit === 'true'); this.menuCanDelete = (d.canDeleteForMe === 'true'); this.menuOpen = true; }, closeMenu(){ this.menuOpen = false; } }">
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] app-accent">Messenger</p>
            <h2 class="mt-2 text-3xl font-black text-[color:var(--app-text)]">Private Conversation</h2>
            <p class="mt-2 text-sm app-muted">Only sender, receiver, and head authority can review these chat records.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[0.35fr_0.65fr]">
        <aside class="app-surface rounded-2xl p-4">
            <h3 class="text-lg font-black">Contacts</h3>
            <form method="GET" action="{{ request()->url() }}" class="mt-3 flex gap-2">
                @if($receiver)
                    <input type="hidden" name="with" value="{{ $receiver->getKey() }}">
                @endif
                <input name="q" value="{{ $search ?? '' }}" placeholder="Search people..." class="min-w-0 flex-1 rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] px-3 py-2 text-sm text-[color:var(--app-text)]">
                <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[color:var(--app-primary)] text-white" aria-label="Search contacts">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                    </svg>
                </button>
            </form>
            <div class="mt-3 space-y-2 max-h-[34rem] overflow-auto pr-1">
                @forelse($contacts as $contact)
                    <a href="{{ request()->url() . '?with=' . $contact->getKey() . (!empty($search) ? '&q=' . urlencode($search) : '') }}" class="block rounded-xl border px-3 py-3 {{ $receiver && (string) $receiver->getKey() === (string) $contact->getKey() ? 'border-[color:var(--app-primary)] bg-[color:var(--app-soft)]' : 'border-[color:var(--app-border)]' }}">
                        <p class="font-semibold">{{ $contact->name }}</p>
                        <p class="text-xs app-muted">{{ ucfirst(str_replace('_', ' ', $contact->role ?? 'user')) }}</p>
                    </a>
                @empty
                    @php
                        $directoryRoute = match (auth()->user()?->role) {
                            'teacher' => route('teacher.finder'),
                            'teacher_admin' => route('teacher-admin.search'),
                            default => route('student.school-members'),
                        };
                    @endphp
                    <div class="rounded-xl border border-dashed border-[color:var(--app-border)] bg-[color:var(--app-soft)] p-4">
                        <p class="text-sm font-semibold text-[color:var(--app-text)]">No contacts available.</p>
                        <p class="mt-1 text-xs app-muted">Open your institute directory first, or complete your profile if it is still missing school information.</p>
                        <a href="{{ $directoryRoute }}" class="mt-3 inline-flex rounded-full bg-[color:var(--app-primary)] px-4 py-2 text-xs font-semibold text-white">
                            Open directory
                        </a>
                    </div>

                    <!-- (global floating menu removed from here; rendered once at the end of the document) -->
                @endforelse
            </div>
        </aside>

        <section class="app-surface rounded-2xl p-4 sm:p-5">
            @if($receiver)
                <div class="border-b border-[color:var(--app-border)] pb-3">
                    <p class="text-sm app-muted">Chatting with</p>
                    <p class="text-xl font-black">{{ $receiver->name }}</p>
                </div>

                @if(auth()->user()?->role === 'teacher_admin')
                    <form method="GET" action="{{ route('teacher-admin.messages') }}" class="mt-4 grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
                        <input type="hidden" name="with" value="{{ $receiver->getKey() }}">
                        <select name="peer" class="rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]">
                            <option value="">View direct chat with me</option>
                            @foreach($contacts as $contact)
                                @if((string) $contact->getKey() !== (string) $receiver->getKey())
                                    <option value="{{ $contact->getKey() }}" @selected($peer && (string) $peer->getKey() === (string) $contact->getKey())>{{ $contact->name }} ({{ ucfirst(str_replace('_', ' ', (string) $contact->role)) }})</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-full border border-[color:var(--app-border)] px-4 py-2 text-sm font-semibold">Inspect</button>
                    </form>
                @endif

                <div class="mt-4 space-y-3 max-h-[28rem] overflow-auto pr-1">
                    @forelse($messages as $message)
                        @php
                            $viewerId = (string) auth()->id();
                            $viewerRole = (string) (auth()->user()?->role ?? '');
                            $mine = (string) $message->sender_id === $viewerId;
                            $canEdit = $mine && !empty($message->deleted_for_everyone_at) === false;
                            $canDeleteForMe = $mine || (string) $message->receiver_id === $viewerId;
                            $sentAt = $message->created_at?->copy()->timezone(config('app.timezone'));
                        @endphp
                        <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}" x-data="{ editing: false, draft: @js($message->body) }" x-on:open-edit.window="if($event.detail === '{{ $message->getKey() }}') editing = true">
                            <div class="max-w-[85%] rounded-2xl px-4 py-3 {{ $mine ? 'bg-[color:var(--app-primary)] text-white' : 'bg-[color:var(--app-soft)] text-[color:var(--app-text)]' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <template x-if="!editing">
                                            <div>
                                                <p class="text-sm leading-6 whitespace-pre-wrap">{{ $message->body }}</p>
                                                @if(!empty($message->edited_at))
                                                    <p class="mt-1 text-[10px] {{ $mine ? 'text-white/80' : 'app-muted' }}">Edited</p>
                                                @endif
                                            </div>
                                        </template>

                                        <template x-if="editing">
                                            <form method="POST" action="{{ request()->routeIs('student.messages*') ? route('student.messages.update', $message) : (request()->routeIs('teacher.messages*') ? route('teacher.messages.update', $message) : route('teacher-admin.messages.update', $message)) }}" class="space-y-3">
                                                @csrf
                                                @method('PATCH')
                                                <textarea name="body" x-model="draft" rows="3" maxlength="4000" class="w-full rounded-xl border border-white/20 bg-white/10 text-sm text-inherit placeholder:text-white/60"></textarea>
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" @click="editing = false" class="rounded-full border border-white/20 px-3 py-1.5 text-xs font-semibold text-inherit">Cancel</button>
                                                    <button type="submit" class="rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold text-inherit">Save</button>
                                                </div>
                                            </form>
                                        </template>
                                    </div>

                                        @if($canEdit || $canDeleteForMe)
                                        <div class="relative">
                                            <button type="button"
                                                data-id="{{ $message->getKey() }}"
                                                data-can-edit="{{ $canEdit ? 'true' : 'false' }}"
                                                data-can-delete-for-me="{{ $canDeleteForMe ? 'true' : 'false' }}"
                                                data-delete-url-me="{{ $canDeleteForMe ? (request()->routeIs('student.messages*') ? route('student.messages.delete', $message) : (request()->routeIs('teacher.messages*') ? route('teacher.messages.delete', $message) : route('teacher-admin.messages.delete', $message))) : '' }}"
                                                data-delete-url-everyone="{{ $mine ? (request()->routeIs('student.messages*') ? route('student.messages.delete', $message) : (request()->routeIs('teacher.messages*') ? route('teacher.messages.delete', $message) : route('teacher-admin.messages.delete', $message))) : '' }}"
                                                @click.prevent="openMenu($event)"
                                                class="rounded-full border border-white/20 px-3 py-1.5 text-xs font-semibold text-inherit">
                                                <span aria-hidden>⋯</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <p class="mt-2 text-[10px] {{ $mine ? 'text-white/80' : 'app-muted' }}">{{ optional($sentAt)->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm app-muted">No messages yet. Start the conversation.</p>
                    @endforelse
                </div>

                @if(!($peer && auth()->user()?->role === 'teacher_admin'))
                    <form method="POST" action="{{ request()->routeIs('student.messages*') ? route('student.messages.send') : (request()->routeIs('teacher.messages*') ? route('teacher.messages.send') : route('teacher-admin.messages.send')) }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $receiver->getKey() }}">
                        <label for="body" class="sr-only">Message</label>
                        <textarea id="body" name="body" rows="3" required maxlength="4000" class="w-full rounded-xl border-[color:var(--app-border)] bg-[color:var(--app-surface-solid)] text-[color:var(--app-text)]" placeholder="Write your message..."></textarea>
                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="rounded-full bg-[color:var(--app-primary)] px-5 py-2.5 text-sm font-semibold text-white">Send Message</button>
                        </div>
                    </form>
                @else
                    <p class="mt-4 text-xs app-muted">Inspection mode: You are viewing messages between {{ $receiver->name }} and {{ $peer->name }}.</p>
                @endif
            @else
                <p class="text-sm app-muted">Select a contact to start messaging.</p>
            @endif
        </section>
    </div>

    <!-- Global floating menu (single instance) -->
    <div x-show="menuOpen" x-transition x-cloak @click.away="closeMenu()" x-bind:style="`position: fixed; left: ${menuX}px; top: ${menuY}px;`" class="z-50">
        <div class="w-44 rounded-md bg-white shadow-lg ring-1 ring-black/5">
            <div class="py-1">
                <button type="button" x-show="menuCanEdit" x-on:click="$dispatch('open-edit', menuMessageId); closeMenu();" class="w-full text-left px-4 py-2 text-sm text-[color:var(--app-text)] hover:bg-[color:var(--app-soft)]">Edit</button>

                <form x-show="menuCanDelete" :action="menuDeleteUrlMe" method="POST" class="m-0">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="delete_scope" value="me">
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-[color:var(--app-text)] hover:bg-[color:var(--app-soft)]">Delete for me</button>
                </form>

                <form x-show="menuDeleteUrlEveryone" :action="menuDeleteUrlEveryone" method="POST" class="m-0" onsubmit="return confirm('Are you sure you want to remove this message for everyone? This action cannot be undone.');">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="delete_scope" value="everyone">
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-[color:var(--app-soft)]">Delete for everyone</button>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
