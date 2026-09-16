@extends('layouts.error', [
    'title' => 'Halaman Sudah Kedaluwarsa',
])

@section('content')
    @include('errors.partials.error-page', [
        'code' => '419',
        'badge' => '419 SESSION EXPIRED',
        'title' => 'Halaman sudah kedaluwarsa',
        'description' => 'Sesi kamu sudah tidak berlaku. Silakan kembali dan coba lagi.',
        'primaryCtaText' => 'KEMBALI KE LOGIN',
        'primaryCtaUrl' => route('login'),
    ])
@endsection
