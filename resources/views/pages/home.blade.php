@extends('layouts.public')

@section('content')
    @include('partials.home.navbar')

    <main>
        @include('partials.home.hero')
        @include('partials.home.schedule')
        @include('partials.home.candidates')
        @include('partials.home.voting-guide')
        @include('partials.home.faq')
    </main>

    @include('partials.home.footer')
@endsection
