<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('gudangmenu');

        $perPage = $request->input('per_page', 5);
        $perPage = $perPage === 'all' ? null : (int) $perPage;

        $search = $request->input('search');

        $gudangsQuery = Gudang::query();

        // Apply search filter
        if ($search) {
            $gudangsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('site_code', 'like', '%'.$search.'%')
                    ->orWhere('location', 'like', '%'.$search.'%');
            });
        }

        // Get result
        $gudangs = $perPage ? $gudangsQuery->latest()->paginate($perPage) : $gudangsQuery->latest()->get();

        return view('gudang.index', [
            'gudangs' => $gudangs,
            'perPage' => $perPage ?? 'all',
            'search' => $search,
        ]);
    }

    public function create()
    {
        $this->authorize('creategudang');

        return view('gudang.create');
    }

    public function store(Request $request)
    {
        $this->authorize('creategudang');

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'site_code' => 'required|string|max:50|unique:gudang,site_code',
            'location' => 'nullable|string|max:255',
            'since' => 'nullable|date',
        ]);

        Gudang::create($validated);

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function edit(Gudang $gudang)
    {
        $this->authorize('editgudang');

        return view('gudang.edit', compact('gudang'));
    }

    public function update(Request $request, Gudang $gudang)
    {
        $this->authorize('editgudang');

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'site_code' => 'required|string|max:50|unique:gudang,site_code,'.$gudang->id,
            'location' => 'nullable|string|max:255',
            'since' => 'nullable|date',
        ]);

        $gudang->update($validated);

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Gudang $gudang)
    {
        $this->authorize('deletegudang');
        $gudang->delete();

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil dihapus.');
    }
}
