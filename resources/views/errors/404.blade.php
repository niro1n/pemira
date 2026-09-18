@extends('layouts.error', [
    'title' => 'Halaman Tidak Ditemukan',
])

@section('content')
    @include('errors.partials.error-page', [
        'code' => '404',
        'badge' => '404 NOT FOUND',
        'title' => 'Halaman tidak ditemukan',
        'description' => 'Halaman yang kamu cari mungkin sudah dipindahkan, dihapus, atau alamatnya tidak benar.',
        'primaryCtaText' => 'KEMBALI KE BERANDA',
        'primaryCtaUrl' => route('home'),
    ])
@endsection
