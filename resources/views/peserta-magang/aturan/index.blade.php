@extends('layouts.portal')
@section('title', 'Aturan Perusahaan')
@section('content')
    <section class="mb-6">
        <h1 class="headline text-2xl md:text-3xl font-bold text-slate-900 mb-1">
            Aturan Perusahaan
        </h1>
        <p class="text-sm text-slate-500">
            Berikut daftar aturan yang berlaku selama masa magang.
        </p>
    </section>

    <div class="space-y-4">
        @if ($aturan->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-400">
                Belum ada aturan perusahaan yang ditambahkan.
            </div>
        @else
            @foreach ($aturan as $item)
                <div class="rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
                    <h3 class="font-semibold text-[#05658f] text-base mb-2">{{ $item->nama }}</h3>
                    <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $item->deskripsi }}</div>
                </div>
            @endforeach
        @endif
    </div>
@endsection