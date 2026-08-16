@props(['n'])

<li class="flex items-start gap-3 py-2 px-3 hover:bg-gray-50 dark:hover:bg-gray-800" data-id="{{ $n->id }}">
    <div
        class="mt-0.5 shrink-0 inline-flex items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 w-8 h-8">
        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="1.7">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9a6 6 0 10-12 0v.75a8.967 8.967 0 01-2.311 6.022c1.766.68 3.55 1.1 5.454 1.31m5.714 0a24.24 24.24 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
    </div>

    <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2">
            <a href="{{ $n->link ?? '#' }}" class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                {{ $n->title ?? ucfirst(str_replace('_', ' ', $n->type)) }}
            </a>
            @if (is_null($n->read_at))
                <span class="inline-block w-2 h-2 rounded-full bg-blue-500" title="Belum dibaca"></span>
            @endif
        </div>
        @if (!empty($n->message))
            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $n->message }}</div>
        @endif
        <div class="mt-0.5 text-[11px] text-gray-400">{{ $n->created_at->diffForHumans() }}</div>
        <div class="mt-1">
            <button type="button"
                class="text-[11px] text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 underline"
                data-action="mark-read" data-id="{{ $n->id }}">Tandai terbaca</button>
        </div>
    </div>
</li>
