@foreach($armadas as $armada)
<tr data-id="{{ $armada->id }}">
    <td><p class="font-extrabold text-slate-900 dark:text-white">{{ $armada->name }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $armada->nomor_polisi }} &middot; {{ $armada->brand }} {{ $armada->model }}</p></td>
    <td><p>{{ $armada->location?->name ?: '-' }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $armada->user?->name ?: 'Tanpa PIC' }}</p></td>
    <td><x-status-badge :status="$armada->status" /></td>
    <td><x-status-badge :status="$armada->document_status" /></td>
    <td class="whitespace-nowrap">{{ optional($armada->stnk_expired)->translatedFormat('d M Y') ?: '-' }}</td>
    <td class="whitespace-nowrap text-right"><a href="{{ route('armada.show',$armada) }}" class="mr-3 font-bold text-sky-700 dark:text-red-400">Detail</a>@can('samsatarmada')<button type="button" onclick="openSamsatModal({{ $armada->id }})" class="font-bold text-emerald-600 dark:text-emerald-400">Perpanjang</button>@endcan</td>
</tr>
@endforeach
