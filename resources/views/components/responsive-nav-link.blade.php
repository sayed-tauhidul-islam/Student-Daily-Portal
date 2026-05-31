@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-xl px-4 py-3 text-start text-base font-semibold text-[color:var(--app-primary)] bg-[color:var(--app-soft)] border border-[color:var(--app-border)] transition'
            : 'block w-full rounded-xl px-4 py-3 text-start text-base font-semibold text-[color:var(--app-muted)] border border-transparent hover:bg-[color:var(--app-soft)] hover:text-[color:var(--app-text)] transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
