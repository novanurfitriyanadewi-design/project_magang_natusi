<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\PesertaMagang;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PembayaranPeriodService
{
    public function startFor(Pembayaran $pembayaran): Carbon
    {
        if ($pembayaran->periode_mulai) {
            return Carbon::parse($pembayaran->periode_mulai)->startOfMonth();
        }

        $fallback = $pembayaran->tgl_bayar ?? $pembayaran->created_at ?? now();

        return Carbon::parse($fallback)->startOfMonth();
    }

    public function monthCountFor(Pembayaran $pembayaran): int
    {
        return max(1, (int) ($pembayaran->jumlah_bulan ?: 1));
    }

    public function endFor(Pembayaran $pembayaran): Carbon
    {
        if ($pembayaran->periode_selesai) {
            return Carbon::parse($pembayaran->periode_selesai)->endOfMonth();
        }

        return $this->startFor($pembayaran)
            ->copy()
            ->addMonthsNoOverflow($this->monthCountFor($pembayaran) - 1)
            ->endOfMonth();
    }

    /** @return array<int, string> */
    public function monthKeysFor(Pembayaran $pembayaran): array
    {
        return $this->monthKeys($this->startFor($pembayaran), $this->monthCountFor($pembayaran));
    }

    /** @return array<int, string> */
    public function monthKeys(Carbon $start, int $count): array
    {
        $keys = [];
        $cursor = $start->copy()->startOfMonth();

        for ($i = 0; $i < max(1, $count); $i++) {
            $keys[] = $cursor->format('Y-m');
            $cursor->addMonthNoOverflow();
        }

        return $keys;
    }

    /** @return array<string, bool> */
    public function paidMonthMap(Collection $payments): array
    {
        $paid = [];

        foreach ($payments->where('status', 'lunas') as $payment) {
            foreach ($this->monthKeysFor($payment) as $key) {
                $paid[$key] = true;
            }
        }

        return $paid;
    }

    /** @return array<string, bool> */
    public function pendingMonthMap(Collection $payments): array
    {
        $pending = [];

        foreach ($payments->where('status', 'menunggu') as $payment) {
            foreach ($this->monthKeysFor($payment) as $key) {
                $pending[$key] = true;
            }
        }

        return $pending;
    }

    public function firstUnpaidMonth(PesertaMagang $peserta, Collection $payments): Carbon
    {
        $paid = $this->paidMonthMap($payments);

        $start = $peserta->tgl_mulai
            ? Carbon::parse($peserta->tgl_mulai)->startOfMonth()
            : now()->startOfMonth();

        $end = $peserta->tgl_selesai
            ? Carbon::parse($peserta->tgl_selesai)->endOfMonth()
            : $start->copy()->addMonthsNoOverflow(23)->endOfMonth();

        $cursor = $start->copy();
        while ($cursor->lte($end) && isset($paid[$cursor->format('Y-m')])) {
            $cursor->addMonthNoOverflow();
        }

        return $cursor;
    }

    public function maxConsecutiveSelectableMonths(PesertaMagang $peserta, Collection $payments, Carbon $start, int $hardLimit = 12): int
    {
        $paid = $this->paidMonthMap($payments);
        $pending = $this->pendingMonthMap($payments);
        $end = $peserta->tgl_selesai
            ? Carbon::parse($peserta->tgl_selesai)->endOfMonth()
            : $start->copy()->addMonthsNoOverflow($hardLimit - 1)->endOfMonth();

        $cursor = $start->copy()->startOfMonth();
        $count = 0;

        while ($cursor->lte($end) && $count < $hardLimit) {
            $key = $cursor->format('Y-m');
            if (isset($paid[$key]) || isset($pending[$key])) {
                break;
            }

            $count++;
            $cursor->addMonthNoOverflow();
        }

        return $count;
    }

    public function isCurrentMonthPaid(Collection $payments): bool
    {
        $paid = $this->paidMonthMap($payments);

        return isset($paid[now()->format('Y-m')]);
    }

    public function paidThroughLabel(PesertaMagang $peserta, Collection $payments): ?string
    {
        $paid = $this->paidMonthMap($payments);
        if ($paid === []) {
            return null;
        }

        $start = $peserta->tgl_mulai
            ? Carbon::parse($peserta->tgl_mulai)->startOfMonth()
            : now()->startOfMonth();

        $cursor = $start->copy();
        $lastPaid = null;
        for ($i = 0; $i < 36; $i++) {
            $key = $cursor->format('Y-m');
            if (! isset($paid[$key])) {
                break;
            }
            $lastPaid = $cursor->copy();
            $cursor->addMonthNoOverflow();
        }

        return $lastPaid?->translatedFormat('F Y');
    }

    public function labelFor(Pembayaran $pembayaran): string
    {
        $start = $this->startFor($pembayaran);
        $end = $this->endFor($pembayaran);

        if ($start->format('Y-m') === $end->format('Y-m')) {
            return $start->translatedFormat('F Y');
        }

        return $start->translatedFormat('F Y') . ' – ' . $end->translatedFormat('F Y');
    }

    public function isRejectedResolved(Pembayaran $rejected, Collection $payments): bool
    {
        if ($rejected->status !== 'ditolak') {
            return false;
        }

        $paid = $this->paidMonthMap($payments);
        foreach ($this->monthKeysFor($rejected) as $key) {
            if (! isset($paid[$key])) {
                return false;
            }
        }

        return true;
    }
}
