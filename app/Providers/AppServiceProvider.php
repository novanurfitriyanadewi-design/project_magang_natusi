<?php

namespace App\Providers;

use App\Models\PesertaMagang;
use App\Services\PenugasanTemplateService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        PesertaMagang::saved(function (PesertaMagang $peserta): void {
            if (!$peserta->wasRecentlyCreated
                && !$peserta->wasChanged(['tgl_mulai', 'tingkat_pendidikan', 'kelas', 'status'])) {
                return;
            }

            if ($peserta->status !== 'aktif' || !$peserta->tgl_mulai) {
                return;
            }

            app(PenugasanTemplateService::class)->syncForParticipant($peserta);
        });
    }
}
