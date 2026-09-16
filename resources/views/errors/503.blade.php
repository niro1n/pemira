@extends('layouts.error', [
    'title' => 'PEMIRA Sedang Tidak Tersedia',
])

@section('content')
    @include('errors.partials.error-page', [
        'code' => '503',
        'badge' => '503 MAINTENANCE',
        'title' => 'PEMIRA sedang tidak tersedia',
        'description' => 'Layanan sedang dalam proses pemeliharaan. Silakan coba lagi beberapa saat.',
        'primaryCtaText' => 'COBA LAGI',
        'primaryCtaUrl' => url()->current(),
    ])
@endsection
