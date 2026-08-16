<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    use AuthorizesRequests;

    protected function buildIndexQuery(Request $request)
    {
        $search = (string) $request->input('search', '');

        return Client::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id');
    }

    public function index(Request $request)
    {
        $this->authorize('clientmenu');

        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        if ($search && ! $request->has('per_page')) {
            $perPage = 'all';
        }
        $perPage = $perPage === 'all' ? null : (int) $perPage;

        $query = $this->buildIndexQuery($request);

        $clients = $perPage
            ? $query->paginate($perPage)->appends($request->all())
            : $query->get();

        $rawLatest = (clone $query)
            ->select(DB::raw('GREATEST(MAX(updated_at), MAX(created_at)) as ts'))
            ->value('ts');
        $latestTs = $rawLatest ? Carbon::parse($rawLatest)->toIso8601String() : now()->toIso8601String();

        $baseOffset = ($clients instanceof LengthAwarePaginator)
            ? (($clients->currentPage() - 1) * $clients->perPage())
            : 0;

        return view('client.index', compact('clients', 'search', 'perPage', 'latestTs', 'baseOffset'));
    }

    public function changes(Request $request)
    {
        $this->authorize('clientmenu');

        $sinceIso = (string) $request->query('since', '');
        $since = $sinceIso ? Carbon::parse($sinceIso) : Carbon::now()->subYears(10);
        $since2 = (clone $since)->subSeconds(2);

        $base = $this->buildIndexQuery($request);

        $created = (clone $base)
            ->where('created_at', '>=', $since2)
            ->pluck('id')
            ->all();

        $updated = (clone $base)
            ->where('updated_at', '>=', $since2)
            ->where('created_at', '<', $since2)
            ->pluck('id')
            ->all();

        $visible = array_values(array_filter((array) $request->query('visible'), fn ($v) => is_numeric($v)));
        $deleted = [];
        if (! empty($visible)) {
            $existingVisible = (clone $base)->whereIn('id', $visible)->pluck('id')->all();
            $deleted = array_values(array_diff($visible, $existingVisible));
        }

        $rawLatest = (clone $base)
            ->select(DB::raw('GREATEST(MAX(updated_at), MAX(created_at)) as ts'))
            ->value('ts');
        $latest = $rawLatest ? Carbon::parse($rawLatest)->toIso8601String() : Carbon::now()->toIso8601String();

        return response()->json([
            'latest_ts' => $latest,
            'created' => array_values(array_unique($created)),
            'updated' => array_values(array_unique($updated)),
            'deleted' => $deleted,
        ]);
    }

    public function rows(Request $request)
    {
        $this->authorize('clientmenu');

        $ids = array_filter((array) $request->query('ids'), fn ($v) => is_numeric($v));
        if (! $ids) {
            return response('');
        }

        $clients = Client::whereIn('id', $ids)->orderByDesc('id')->get();

        return view('client._rows', compact('clients'));
    }

    public function create()
    {
        $this->authorize('clientmenu');

        return view('client.create');
    }

    public function store(Request $request)
    {
        $this->authorize('createclient');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => [
                'required',
                'regex:/^[0-9]{1,13}$/',
                Rule::unique('clients', 'phone')->where(function ($query) use ($request) {
                    return $query->where('name', $request->name)
                        ->where('company', $request->company)
                        ->where('email', $request->email);
                }),
            ],
        ], [
            'phone.regex' => 'Nomor telepon hanya boleh angka dan maksimal 13 digit.',
            'phone.unique' => 'Client dengan nama "'.$request->name.
                '", perusahaan "'.$request->company.
                '", email "'.$request->email.
                '", dan nomor telepon "'.$request->phone.
                '" sudah tersedia.',
        ]);

        Client::create($validated);

        return redirect()->route('client.index')->with('success', 'Client successfully created.');
    }

    public function show(Request $request, $id)
    {
        $this->authorize('clientmenu');

        $client = Client::findOrFail($id);

        $invPerPage = $request->input('invoice_per_page', 5);
        $quoPerPage = $request->input('quotation_per_page', 5);
        $invPerPage = $invPerPage === 'all' ? 100000 : (int) $invPerPage;
        $quoPerPage = $quoPerPage === 'all' ? 100000 : (int) $quoPerPage;

        $invoices = $client->invoices()
            ->orderByDesc('created_at')
            ->paginate($invPerPage, ['*'], 'invoice_page')
            ->appends($request->except('invoice_page'));

        $quotations = $client->quotations()
            ->orderByDesc('created_at')
            ->paginate($quoPerPage, ['*'], 'quotation_page')
            ->appends($request->except('quotation_page'));

        return view('client.show', compact('client', 'invoices', 'quotations', 'invPerPage', 'quoPerPage'));
    }

    public function edit(Client $client)
    {
        $this->authorize('editclient');

        return view('client.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->authorize('editclient');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $client->update($validated);

        return redirect()->route('client.index')->with('success', 'Client successfully updated.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('deleteclient');
        $client->delete();

        return redirect()->route('client.index')->with('success', 'Client successfully deleted.');
    }
}
