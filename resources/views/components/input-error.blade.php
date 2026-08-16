@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-start gap-2 rounded-xl border border-red-100 bg-red-50/80 px-3 py-2 text-xs font-semibold leading-5 text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-300">
                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v5m0 3h.01"/></svg>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif
