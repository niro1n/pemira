@extends('layouts.public')

@section('content')
    @include('partials.home.navbar', ['election' => $election])

    <main>
        @include('partials.home.hero', ['election' => $election])
        @include('partials.home.schedule', ['election' => $election])
        @include('partials.home.candidates', ['election' => $election])
        @include('partials.home.voting-guide', ['election' => $election])
        @include('partials.home.sponsors', ['sponsors' => $sponsors])
        @include('partials.home.faq', ['election' => $election])
    </main>

    @include('partials.home.footer', ['election' => $election])
@endsection
