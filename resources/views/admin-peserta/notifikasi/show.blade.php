@extends('layouts.portal')
@section('content')<h1 class="text-2xl font-bold">{{ $notifikasi->judul }}</h1><p class="mt-4">{{ $notifikasi->pesan }}</p><a class="mt-4 inline-block" href="{{ route('admin-peserta.notifikasi.edit', $notifikasi) }}">Ubah</a>@endsection
