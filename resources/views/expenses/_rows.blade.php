@php
    // Koleksi bisa datang sebagai $expenses (polling) atau $rows (render awal)
    $collection = isset($rows) ? $rows : $expenses ?? collect();
    $canAct = $allowActions ?? ($locks['can_add'] ?? false); // fallback aman
@endphp

@foreach ($collection as $row)
    <tr data-id="{{ $row->id }}">
        <td class="px-4 py-3">
            {{ ($baseOffset ?? 0) + $loop->iteration }}
        </td>
        <td class="px-4 py-3 text-left font-medium">
            {{ $row->expense_number }}
        </td>
        <td class="px-4 py-3">
            {{ optional($row->expense_date)->format('d/m/Y') }}
        </td>
        <td class="px-4 py-3 text-left">
            {{ $row->name }}
        </td>
        <td class="px-4 py-3">
            {{ number_format((int) $row->qty, 0, ',', '.') }}
        </td>
        <td class="px-4 py-3 font-semibold">
            {{ 'Rp ' . number_format((int) $row->total, 0, ',', '.') }}
        </td>
        <td class="px-4 py-3 text-left">
            {{ $row->creator->name ?? '-' }}
        </td>
        <td class="px-4 py-3">
            <div class="flex justify-center items-center gap-1">
                @can('editexpenses')
                    <x-action-button :href="route('expenses.edit', $row)" onclick="showFullScreenLoader();" class="{{ $canAct ? '' : 'pointer-events-none opacity-50' }}" color="green" text="Edit" :dense="true" />
                @endcan
                @can('deleteexpenses')
                    <form action="{{ route('expenses.destroy', $row) }}" method="POST"
                        onsubmit="return confirmAndLoad('Hapus expense ini?');"
                        class="{{ $canAct ? '' : 'pointer-events-none opacity-50' }}">
                        @csrf @method('DELETE')
                        <x-action-button color="red" text="Hapus" :dense="true" />
                    </form>
                @endcan
            </div>
        </td>
    </tr>
@endforeach
