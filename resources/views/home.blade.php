@extends('layouts.main')

@section('content')
    <div class="bg-white min-h-screen pb-20">
        <div class="relative flex flex-col w-full max-w-[640px] min-h-screen mx-auto bg-white overflow-x-hidden">

            <!-- HEADER SECTION -->
            <x-home.header />

            <!-- MOST POPULAR SECTION -->
            <x-home.most-popular :events="$mostPopularEvents" />

            <!-- BEST CATEGORIES SECTION -->
            <x-home.best-categories :categories="$bestCategories" />

            <!-- FRESH FOR YOU SECTION -->
            <x-home.fresh-for-you :events="$freshEvents" />

            <!-- IMPROVE SKILLS SECTION -->
            <x-home.improve-skills />

            <!-- BOTTOM NAVIGATION BAR -->
            <x-home.bottom-navigation activeRoute="home" />

        </div>
    </div>
@endsection
