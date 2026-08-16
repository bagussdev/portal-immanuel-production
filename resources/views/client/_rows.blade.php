@foreach ($clients as $client)
    <tr data-id="{{ $client->id }}">
        <td class="px-4 py-3 text-left no"></td>
        <td class="px-4 py-3 text-left name">{{ $client->name }}</td>
        <td class="px-4 py-3 company">{{ $client->company ?? '-' }}</td>
        <td class="px-4 py-3 email">{{ $client->email ?? '-' }}</td>
        <td class="px-4 py-3 phone">{{ $client->phone ?? '-' }}</td>
        <td class="px-4 py-3">
            <div class="flex flex-row items-center justify-center gap-1">
                @can('editclient')
                    <a href="{{ route('client.edit', $client->id) }}" onclick="showFullScreenLoader();">
                        <x-action-button text="Edit" color="green" />
                    </a>
                @endcan
                @can('deleteclient')
                    <form action="{{ route('client.destroy', $client->id) }}" method="POST"
                        onsubmit="return confirm('Delete this client?')">
                        @csrf
                        @method('DELETE')
                        <x-action-button text="Delete" color="red" />
                    </form>
                @endcan
                <a href="{{ route('client.show', $client->id) }}" onclick="showFullScreenLoader();">
                    <x-action-button text="Details" color="blue" />
                </a>
            </div>
        </td>
    </tr>
@endforeach
