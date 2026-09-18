@extends('layouts.error', [
    'title' => 'Terjadi Kesalahan',
])

@section('content')
    @include('errors.partials.error-page', [
        'code' => '500',
        'badge' => '500 SERVER ERROR',
        'title' => 'Terjadi kesalahan',
        'description' => 'Terjadi masalah pada sistem. Silakan coba lagi beberapa saat.',
        'primaryCtaText' => 'KEMBALI KE BERANDA',
        'primaryCtaUrl' => route('home'),
    ])
@endsection
