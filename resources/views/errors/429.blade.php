@extends('layouts.error', [
    'title' => 'Terlalu Banyak Percobaan',
])

@section('content')
    @include('errors.partials.error-page', [
        'code' => '429',
        'badge' => '429 TOO MANY REQUESTS',
        'title' => 'Terlalu banyak percobaan',
        'description' => 'Kamu melakukan terlalu banyak permintaan dalam waktu singkat. Silakan tunggu beberapa saat sebelum mencoba lagi.',
        'primaryCtaText' => 'KEMBALI KE BERANDA',
        'primaryCtaUrl' => route('home'),
    ])
@endsection
