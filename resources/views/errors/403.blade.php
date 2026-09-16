@extends('layouts.error', [
    'title' => 'Akses Ditolak',
])

@section('content')
    @include('errors.partials.error-page', [
        'code' => '403',
        'badge' => '403 FORBIDDEN',
        'title' => 'Akses ditolak',
        'description' => 'Kamu tidak memiliki izin untuk mengakses halaman ini.',
        'primaryCtaText' => 'KEMBALI KE BERANDA',
        'primaryCtaUrl' => route('home'),
    ])
@endsection
