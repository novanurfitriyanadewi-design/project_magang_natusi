<?php

namespace App\Providers;

use App\Models\PesertaMagang;
use App\Services\PenugasanTemplateService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Paksa HTTPS ketika aplikasi berjalan di production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Sinkronisasi template penugasan ketika data peserta magang disimpan
        PesertaMagang::saved(function (PesertaMagang $peserta): void {
            if (
                !$peserta->wasRecentlyCreated &&
                !$peserta->wasChanged([
                    'tgl_mulai',
                    'tingkat_pendidikan',
                    'kelas',
                    'status'
                ])
            ) {
                return;
            }

            if ($peserta->status !== 'aktif' || !$peserta->tgl_mulai) {
                return;
            }

            app(PenugasanTemplateService::class)
                ->syncForParticipant($peserta);
        });
    }
}