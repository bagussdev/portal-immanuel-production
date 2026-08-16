@props(['n'])

<tr data-id="{{ $n->id }}"
    class="border-t dark:border-gray-700 {{ $n->read_at ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-800' }}">
    <td class="px-4 py-2">#{{ $n->id }}</td>
    <td class="px-4 py-2">{{ $n->type }}</td>
    <td class="px-4 py-2 max-w-[260px] truncate">{{ $n->title ?? data_get($n->data, 'title') }}</td>
    <td class="px-4 py-2 max-w-[380px] truncate">{{ $n->message ?? data_get($n->data, 'message') }}</td>
    <td class="px-4 py-2">{{ $n->created_at->format('Y-m-d H:i') }}</td>
    <td class="px-4 py-2">
        @if (is_null($n->read_at))
            <span class="inline-flex items-center gap-1 text-xs text-blue-700 dark:text-blue-300">
                <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span> Unread
            </span>
        @else
            <span class="text-xs text-gray-500">Read</span>
        @endif
    </td>
    <td class="px-4 py-2">
        @if (is_null($n->read_at))
            <form method="POST" action="{{ route('notifications.read', $n) }}"
                onsubmit="return confirmAndLoad('Mark as read?')">
                @csrf
                <x-action-button text="Mark read" color="green" type="submit" dense="true" />
            </form>
        @else
            <span class="text-xs text-gray-400">—</span>
        @endif
    </td>
</tr>
