@extends('layouts.portal')
@section('title', 'Notifikasi')
@section('content')<h1 class="text-2xl font-bold">Notifikasi</h1><a class="mt-4 inline-block" href="{{ route('admin-peserta.notifikasi.create') }}">Buat notifikasi</a><div class="mt-4 space-y-2">@foreach($notifikasis as $notifikasi)<a class="block rounded bg-white p-4" href="{{ route('admin-peserta.notifikasi.show', $notifikasi) }}">{{ $notifikasi->judul }}</a>@endforeach</div>{{ $notifikasis->links() }}@endsection
