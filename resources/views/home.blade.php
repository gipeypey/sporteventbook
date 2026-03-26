@extends('layouts.main')

@section('navbar')
    <x-navbar />
@endsection

@section('content')
    <!-- Hero Section -->
    <x-hero />

    <!-- Events Carousel Section -->
    <section id="events">
        <x-events-carousel :events="$mostPopularEvents" />
    </section>

    <!-- Ranking Section -->
    <x-ranking :topMen="$topMen" :topWomen="$topWomen" />

    <!-- News Section -->
    <x-news :news="$latestNews" />

    <!-- Registration CTA Section -->
    <x-registration-cta />

    <!-- Sponsors Section -->
    <x-sponsors :sponsors="$sponsors" />
@endsection

@section('footer')
    <x-footer />
@endsection