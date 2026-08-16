<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Gudang;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    use AuthorizesRequests;

    protected function buildIndexQuery(Request $request)
    {
        $search = $request->input('search');

        return Equipment::with('gudang', 'createdBy')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhereHas('gudang', function ($loc) use ($search) {
                            $loc->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('id'); // <<< penting: yang baru di atas
    }

    public function index(Request $request)
    {
        $this->authorize('equipmentmenu');

        $search = $request->input('search');
        $perPage = $request->input('per_page', 5);

        if ($search && ! $request->has('per_page')) {
            $perPage = 'all';
        }
        $perPage = $perPage === 'all' ? null : (int) $perPage;

        $query = $this->buildIndexQuery($request);

        $equipment = $perPage
            ? $query->paginate($perPage)->appends($request->all())
            : $query->get();

        // latestTs untuk polling awal
        $rawLatest = (clone $query)
            ->select(DB::raw('GREATEST(MAX(updated_at), MAX(created_at)) as ts'))
            ->value('ts');
        $latestTs = $rawLatest ? Carbon::parse($rawLatest)->toIso8601String() : now()->toIso8601String();

        // base offset untuk kolom "No"
        $baseOffset = ($equipment instanceof LengthAwarePaginator)
            ? (($equipment->currentPage() - 1) * $equipment->perPage())
            : 0;

        return view('equipment.index', compact('equipment', 'search', 'perPage', 'latestTs', 'baseOffset'));
    }

    /** === POLLING: heartbeat changes (JSON) === */
    public function changes(Request $request)
    {
        $this->authorize('equipmentmenu');

        $sinceIso = $request->query('since');
        $since = $sinceIso ? Carbon::parse($sinceIso) : Carbon::now()->subYears(10);
        $since2 = (clone $since)->subSeconds(2); // anti-miss

        $base = $this->buildIndexQuery($request);

        // CREATED & UPDATED (seperti sebelumnya, pakai >= dan since2)
        $created = (clone $base)
            ->where('created_at', '>=', $since2)
            ->pluck('id')
            ->all();

        $updated = (clone $base)
            ->where('updated_at', '>=', $since2)
            ->where('created_at', '<', $since2)
            ->pluck('id')
            ->all();

        // DELETED: dari "visible[]" yang dikirim client tapi sudah tidak ada di DB
        $visible = array_values(array_filter((array) $request->query('visible'), fn ($v) => is_numeric($v)));
        $deleted = [];
        if (! empty($visible)) {
            $existingVisible = (clone $base)
                ->whereIn('id', $visible)
                ->pluck('id')
                ->all();

            $deleted = array_values(array_diff($visible, $existingVisible));
        }

        // latest_ts utk seed berikutnya
        $rawLatest = (clone $base)
            ->select(DB::raw('GREATEST(MAX(updated_at), MAX(created_at)) as ts'))
            ->value('ts');

        $latest = $rawLatest
            ? Carbon::parse($rawLatest)->toIso8601String()
            : Carbon::now()->toIso8601String();

        return response()->json([
            'latest_ts' => $latest,
            'created' => array_values(array_unique($created)),
            'updated' => array_values(array_unique($updated)),
            'deleted' => $deleted,
        ]);
    }

    /** === POLLING: render rows (HTML <tr>…) === */
    public function rows(Request $request)
    {
        $this->authorize('equipmentmenu');

        $ids = array_filter((array) $request->query('ids'), fn ($v) => is_numeric($v));
        if (! $ids) {
            return response('');
        }

        $equipment = Equipment::with('gudang', 'createdBy')->whereIn('id', $ids)->get();

        return view('equipment._rows', compact('equipment'));
    }

    public function create()
    {
        $this->authorize('createequipment');

        $gudangs = Gudang::orderBy('name')->get();

        return view('equipment.create', compact('gudangs'));
    }

    public function store(Request $request)
    {
        $this->authorize('createequipment');

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => [
                'nullable',
                'string',
                'max:100',
                // unik per kombinasi model + serial_number
                Rule::unique('equipment', 'serial_number')->where(function ($q) use ($request) {
                    return $q->where('model', $request->model);
                }),
            ],
            'qty' => 'required|integer|min:1',
            'status' => 'required|in:baik,rusak',
            'location' => 'required|exists:gudang,id',
            'notes' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'serial_number.unique' => 'Barang dengan model "'.($request->model ?? '-').'" dan S/N "'.($request->serial_number ?? '-').'" sudah tersedia.',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('equipment', 'public');
        }

        $validated['created_by'] = Auth::id();
        Equipment::create($validated);

        return redirect()->route('equipment.index')->with('success', 'Equipment berhasil ditambahkan.');
    }

    public function show($id)
    {

        $equipment = Equipment::with('gudang')
            ->findOrFail($id);

        return view('equipment.show', compact('equipment'));
    }

    public function edit($id)
    {
        $this->authorize('editequipment');

        $equipment = Equipment::findOrFail($id);
        $gudangs = Gudang::all(); // diasumsikan 'gudangs' diambil dari tabel 'store'

        return view('equipment.edit', compact('equipment', 'gudangs'));
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editequipment');

        $equipment = Equipment::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100|unique:equipment,serial_number,'.$id,
            'qty' => 'required|integer|min:1',
            'status' => 'required|in:baik,rusak',
            'location' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($equipment->photo) {
                Storage::disk('public')->delete($equipment->photo);
            }
            $validated['photo'] = $request->file('photo')->store('equipment', 'public');
        }

        $equipment->update($validated);

        return redirect()->route('equipment.index')->with('success', 'Equipment berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->authorize('deleteequipment');

        $equipment = Equipment::findOrFail($id);
        if ($equipment->photo) {
            Storage::disk('public')->delete($equipment->photo);
        }

        $equipment->delete();

        return redirect()->route('equipment.index')->with('success', 'Equipment berhasil dihapus.');
    }
}
