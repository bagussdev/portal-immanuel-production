@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs font-semibold text-slate-500">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} dari {{ $paginator->total() }}</p>
        <div class="flex items-center justify-between gap-2 sm:hidden">
            @if ($paginator->onFirstPage())<span class="ip-btn-secondary pointer-events-none opacity-45">Sebelumnya</span>@else<a href="{{ $paginator->previousPageUrl() }}" class="ip-btn-secondary">Sebelumnya</a>@endif
            <span class="text-xs font-bold text-slate-500">{{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}</span>
            @if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" class="ip-btn-secondary">Berikutnya</a>@else<span class="ip-btn-secondary pointer-events-none opacity-45">Berikutnya</span>@endif
        </div>
        <div class="hidden items-center gap-1 sm:flex">
            @foreach ($elements as $element)
                @if (is_string($element))<span class="px-2 text-slate-400">{{ $element }}</span>@endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())<span aria-current="page" class="flex h-9 min-w-9 items-center justify-center rounded-xl bg-sky-700 px-2 text-xs font-black text-white dark:bg-red-600">{{ $page }}</span>
                        @else<a href="{{ $url }}" class="flex h-9 min-w-9 items-center justify-center rounded-xl border border-sky-100 bg-white px-2 text-xs font-bold text-slate-600 hover:bg-sky-50 dark:border-white/10 dark:bg-white/[.04] dark:text-slate-300">{{ $page }}</a>@endif
                    @endforeach
                @endif
            @endforeach
        </div>
    </nav>
@endif
