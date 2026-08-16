@foreach ($equipment as $item)
    <tr data-id="{{ $item->id }}">
        {{-- No (dinomori ulang via JS) --}}
        <td class="px-4 py-3 text-left no"></td>

        <td class="px-4 py-3 text-left name">{{ $item->name }}</td>
        <td class="px-4 py-3 brand">{{ $item->brand ?? '-' }}</td>
        <td class="px-4 py-3 model">{{ $item->model ?? '-' }}</td>
        <td class="px-4 py-3 serial">{{ $item->serial_number ?? '-' }}</td>
        <td class="px-4 py-3 qty">{{ $item->qty }}</td>
        <td class="px-4 py-3 createdby">{{ $item->createdBy->name ?? '-' }}</td>

        <td class="px-4 py-3 status">
            @if ($item->status === 'baik')
                <span class="px-3 py-1 text-xs font-medium rounded-md bg-green-100 text-green-800">Baik</span>
            @else
                <span class="px-3 py-1 text-xs font-medium rounded-md bg-red-100 text-red-700">Rusak</span>
            @endif
        </td>

        <td class="px-4 py-3 location">{{ $item->gudang->name ?? '-' }}</td>

        <td class="px-4 py-3">
            <div class="flex flex-row items-center justify-center gap-1">
                @can('editequipment')
                    <a href="{{ route('equipment.edit', $item->id) }}" onclick="showFullScreenLoader();">
                        <x-action-button text="Edit" color="blue" />
                    </a>
                @endcan

                @can('deleteequipment')
                    <form action="{{ route('equipment.destroy', $item->id) }}" method="POST"
                        onsubmit="return confirmAndLoad('Are you sure to delete equipment?')">
                        @csrf
                        @method('DELETE')
                        <x-action-button text="Delete" color="red" />
                    </form>
                @endcan

                <a href="{{ route('equipment.show', $item->id) }}" onclick="showFullScreenLoader();">
                    <x-action-button text="Details" color="blue" />
                </a>
            </div>
        </td>
    </tr>
@endforeach
