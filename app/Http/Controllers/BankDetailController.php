<?php

namespace App\Http\Controllers;

use App\Models\BankDetail;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BankDetailController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('bankdetailmenu');
        $search = trim((string) $request->input('search'));
        $bankDetails = BankDetail::query()
            ->when($search, fn ($query) => $query->where(fn ($sub) => $sub
                ->where('label', 'like', "%{$search}%")
                ->orWhere('bank_name', 'like', "%{$search}%")
                ->orWhere('account_name', 'like', "%{$search}%")
                ->orWhere('account_number', 'like', "%{$search}%")))
            ->orderByDesc('active')->orderBy('label')->paginate(12)->withQueryString();

        return view('bank-details.index', compact('bankDetails', 'search'));
    }

    public function create()
    {
        $this->authorize('createbankdetail');

        return view('bank-details.create', ['bankDetail' => new BankDetail(['active' => true])]);
    }

    public function store(Request $request)
    {
        $this->authorize('createbankdetail');
        BankDetail::create($this->validated($request));

        return redirect()->route('bank-details.index')->with('success', 'Detail rekening berhasil ditambahkan.');
    }

    public function edit(BankDetail $bankDetail)
    {
        $this->authorize('editbankdetail');

        return view('bank-details.edit', compact('bankDetail'));
    }

    public function update(Request $request, BankDetail $bankDetail)
    {
        $this->authorize('editbankdetail');
        $bankDetail->update($this->validated($request));

        return redirect()->route('bank-details.index')->with('success', 'Detail rekening berhasil diperbarui.');
    }

    public function destroy(BankDetail $bankDetail)
    {
        $this->authorize('deletebankdetail');
        if ($bankDetail->invoices()->exists() || $bankDetail->quotations()->exists()) {
            $bankDetail->update(['active' => false]);

            return back()->with('warning', 'Rekening dipakai oleh dokumen lama, jadi dinonaktifkan agar riwayat tetap aman.');
        }
        $bankDetail->delete();

        return back()->with('success', 'Detail rekening berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'npwp' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'active' => ['nullable', 'boolean'],
        ]);
        $data['active'] = $request->boolean('active');

        return $data;
    }
}
