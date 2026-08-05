<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AturanPerusahaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AturanPerusahaanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $rules = AturanPerusahaan::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('nama', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->latest('updated_at')
            ->paginate(5)
            ->withQueryString();

        $totalRules = AturanPerusahaan::query()->count();

        $rulesThisMonth = AturanPerusahaan::query()
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->count();

        $latestRule = AturanPerusahaan::query()
            ->latest('updated_at')
            ->first();

        return view('superadmin.aturan', compact(
            'rules',
            'search',
            'totalRules',
            'rulesThisMonth',
            'latestRule',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->rules(),
            $this->messages(),
        );

        $validated['status'] = 'aktif';

        AturanPerusahaan::query()->create($validated);

        return redirect()
            ->route('superadmin.aturan.index')
            ->with('success', 'Aturan perusahaan berhasil ditambahkan.');
    }

    public function update(
        Request $request,
        AturanPerusahaan $aturan
    ): RedirectResponse {
        $validated = $request->validate(
            $this->rules($aturan),
            $this->messages(),
        );

        $validated['status'] = 'aktif';

        $aturan->update($validated);

        return redirect()
            ->route('superadmin.aturan.index')
            ->with('success', 'Aturan perusahaan berhasil diperbarui.');
    }

    public function destroy(
        AturanPerusahaan $aturan
    ): RedirectResponse {
        $name = $aturan->nama;

        $aturan->delete();

        return redirect()
            ->route('superadmin.aturan.index')
            ->with('success', "Aturan {$name} berhasil dihapus.");
    }

    private function rules(
        ?AturanPerusahaan $aturan = null
    ): array {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('aturan_perusahaan', 'nama')
                    ->ignore($aturan?->id_aturan, 'id_aturan'),
            ],
            'deskripsi' => [
                'required',
                'string',
                'min:20',
                'max:10000',
            ],
        ];
    }

    private function messages(): array
    {
        return [
            'nama.required' => 'Nama aturan wajib diisi.',
            'nama.max' => 'Nama aturan maksimal 255 karakter.',
            'nama.unique' => 'Nama aturan tersebut sudah digunakan.',
            'deskripsi.required' => 'Isi atau penjelasan aturan wajib diisi.',
            'deskripsi.min' => 'Penjelasan aturan minimal 20 karakter.',
            'deskripsi.max' => 'Penjelasan aturan maksimal 10.000 karakter.',
        ];
    }
}