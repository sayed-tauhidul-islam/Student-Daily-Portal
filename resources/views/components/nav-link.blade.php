@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-[color:var(--app-primary)] bg-[color:var(--app-soft)] border border-[color:var(--app-border)] transition'
            : 'inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-[color:var(--app-muted)] border border-transparent hover:bg-[color:var(--app-soft)] hover:text-[color:var(--app-text)] transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
