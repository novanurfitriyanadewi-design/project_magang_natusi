<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\NominalPembayaran;
use App\Models\RiwayatMetodePembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DataMetodePembayaranController extends Controller
{
    private const BANK_OPTIONS = [
        'BCA' => 'Bank Central Asia',
        'MANDIRI' => 'Bank Mandiri',
        'BRI' => 'Bank Rakyat Indonesia',
        'BNI' => 'Bank Negara Indonesia',
        'BSI' => 'Bank Syariah Indonesia',
        'BTN' => 'Bank Tabungan Negara',
        'CIMB' => 'CIMB Niaga',
        'DANAMON' => 'Bank Danamon',
        'PERMATA' => 'PermataBank',
        'OCBC' => 'OCBC Indonesia',
        'MAYBANK' => 'Maybank Indonesia',
        'MEGA' => 'Bank Mega',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $banks = Bank::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('nama_bank', 'like', "%{$search}%")
                        ->orWhere('no_rekening', 'like', "%{$search}%")
                        ->orWhere('nama_pemilik', 'like', "%{$search}%");
                });
            })
            ->latest('id_bank')
            ->get();

        $nominal = NominalPembayaran::query()
            ->latest('id_nominal')
            ->first();

        $histories = RiwayatMetodePembayaran::query()
            ->with('user:id_user,nama')
            ->latest('id_riwayat')
            ->limit(30)
            ->get();

        return view('admin.metode-pembayaran.index', [
            'banks' => $banks,
            'nominal' => $nominal,
            'histories' => $histories,
            'bankOptions' => self::BANK_OPTIONS,
            'search' => $search,
            'totalBanks' => Bank::query()->count(),
        ]);
    }

    public function updateNominal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah_nominal' => [
                'required',
                'integer',
                'min:1000',
                'max:999999999',
            ],
        ], [
            'jumlah_nominal.required' => 'Jumlah pembayaran wajib diisi.',
            'jumlah_nominal.integer' => 'Jumlah pembayaran harus berupa angka bulat.',
            'jumlah_nominal.min' => 'Jumlah pembayaran minimal Rp1.000.',
            'jumlah_nominal.max' => 'Jumlah pembayaran terlalu besar.',
        ]);

        DB::transaction(function () use ($validated): void {
            $nominal = NominalPembayaran::query()
                ->latest('id_nominal')
                ->lockForUpdate()
                ->first();

            $oldData = $nominal?->only([
                'id_nominal',
                'jumlah_nominal',
            ]);

            if ($nominal) {
                $nominal->update([
                    'jumlah_nominal' => $validated['jumlah_nominal'],
                ]);
            } else {
                $nominal = NominalPembayaran::query()->create([
                    'jumlah_nominal' => $validated['jumlah_nominal'],
                ]);
            }

            $this->recordHistory(
                action: $oldData ? 'ubah' : 'tambah',
                entity: 'nominal',
                description: 'Jumlah pembayaran pendaftaran/administrasi diperbarui.',
                oldData: $oldData,
                newData: $nominal->fresh()->only([
                    'id_nominal',
                    'jumlah_nominal',
                ]),
            );
        });

        return redirect()
            ->route('admin.metode-pembayaran.index')
            ->with('success', 'Jumlah pembayaran berhasil disimpan.');
    }

    public function storeBank(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            $this->bankRules($request),
            $this->bankMessages(),
        );

        $bank = DB::transaction(function () use ($validated): Bank {
            $bank = Bank::query()->create($this->normalizeBankData($validated));

            $this->recordHistory(
                action: 'tambah',
                entity: 'rekening_bank',
                description: "Rekening {$bank->nama_bank} atas nama {$bank->nama_pemilik} ditambahkan.",
                oldData: null,
                newData: $bank->only([
                    'id_bank',
                    'nama_bank',
                    'no_rekening',
                    'nama_pemilik',
                ]),
            );

            return $bank;
        });

        return redirect()
            ->route('admin.metode-pembayaran.index')
            ->with('success', "Rekening {$bank->nama_bank} berhasil ditambahkan.");
    }

    public function updateBank(Request $request, Bank $bank): RedirectResponse
    {
        $validated = $request->validate(
            $this->bankRules($request, $bank),
            $this->bankMessages(),
        );

        DB::transaction(function () use ($bank, $validated): void {
            $oldData = $bank->only([
                'id_bank',
                'nama_bank',
                'no_rekening',
                'nama_pemilik',
            ]);

            $bank->update($this->normalizeBankData($validated));

            $this->recordHistory(
                action: 'ubah',
                entity: 'rekening_bank',
                description: "Rekening {$bank->nama_bank} atas nama {$bank->nama_pemilik} diperbarui.",
                oldData: $oldData,
                newData: $bank->fresh()->only([
                    'id_bank',
                    'nama_bank',
                    'no_rekening',
                    'nama_pemilik',
                ]),
            );
        });

        return redirect()
            ->route('admin.metode-pembayaran.index')
            ->with('success', 'Data rekening bank berhasil diperbarui.');
    }

    public function destroyBank(Bank $bank): RedirectResponse
    {
        if ($bank->pembayaran()->exists()) {
            return redirect()
                ->route('admin.metode-pembayaran.index')
                ->with('error', 'Rekening tidak dapat dihapus karena sudah digunakan pada transaksi pembayaran.');
        }

        $bankName = $bank->nama_bank;

        DB::transaction(function () use ($bank): void {
            $oldData = $bank->only([
                'id_bank',
                'nama_bank',
                'no_rekening',
                'nama_pemilik',
            ]);

            $bank->delete();

            $this->recordHistory(
                action: 'hapus',
                entity: 'rekening_bank',
                description: "Rekening {$oldData['nama_bank']} atas nama {$oldData['nama_pemilik']} dihapus.",
                oldData: $oldData,
                newData: null,
            );
        });

        return redirect()
            ->route('admin.metode-pembayaran.index')
            ->with('success', "Rekening {$bankName} berhasil dihapus.");
    }

    private function bankRules(Request $request, ?Bank $bank = null): array
    {
        $normalizedAccount = preg_replace(
            '/\D+/',
            '',
            (string) $request->input('no_rekening', '')
        );

        $request->merge([
            'no_rekening' => $normalizedAccount,
        ]);

        $uniqueAccount = Rule::unique('bank', 'no_rekening')
            ->where(fn ($query) => $query->where(
                'nama_bank',
                (string) $request->input('nama_bank')
            ));

        if ($bank) {
            $uniqueAccount->ignore($bank->id_bank, 'id_bank');
        }

        return [
            'nama_bank' => [
                'required',
                'string',
                Rule::in(array_values(self::BANK_OPTIONS)),
            ],
            'no_rekening' => [
                'required',
                'digits_between:6,30',
                $uniqueAccount,
            ],
            'nama_pemilik' => [
                'required',
                'string',
                'max:100',
            ],
        ];
    }

    private function bankMessages(): array
    {
        return [
            'nama_bank.required' => 'Nama bank wajib dipilih.',
            'nama_bank.in' => 'Nama bank tidak tersedia pada daftar pilihan.',
            'no_rekening.required' => 'Nomor rekening wajib diisi.',
            'no_rekening.digits_between' => 'Nomor rekening harus terdiri dari 6 sampai 30 digit.',
            'no_rekening.unique' => 'Nomor rekening tersebut sudah terdaftar pada bank yang sama.',
            'nama_pemilik.required' => 'Nama pemilik rekening wajib diisi.',
            'nama_pemilik.max' => 'Nama pemilik rekening maksimal 100 karakter.',
        ];
    }

    private function normalizeBankData(array $validated): array
    {
        return [
            'nama_bank' => trim($validated['nama_bank']),
            'no_rekening' => preg_replace('/\D+/', '', $validated['no_rekening']),
            'nama_pemilik' => mb_strtoupper(trim($validated['nama_pemilik'])),
        ];
    }

    private function recordHistory(
        string $action,
        string $entity,
        string $description,
        ?array $oldData,
        ?array $newData,
    ): void {
        RiwayatMetodePembayaran::query()->create([
            'user_id' => auth()->id(),
            'aksi' => $action,
            'entitas' => $entity,
            'deskripsi' => $description,
            'data_lama' => $oldData,
            'data_baru' => $newData,
        ]);
    }
}
