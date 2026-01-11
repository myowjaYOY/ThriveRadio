@extends('layouts.main')

@section('title')
    Dashboard
@endsection
@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h4>@yield('title')</h4>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
            </div>
        </div>
    </div>
@endsection
@section('content')
    <section class="section">
        <div class="row">

            <!-- Dashboard Card 1: City -->
            @if($city_mode == 1)
                <div class="col-md-3">
                    <a href="{{route('city.index')}}" class="menu-link">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start ">
                                        <div class="stats-icon purple mb-2">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-lg-12 col-xl-12 col-xxl-4">
                                        <h6 class="text-muted font-semibold">City</h6>
                                        <h6 class="font-extrabold mb-0">{{$city}}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            <!-- Dashboard Card 2: Category -->
            <div class="col-md-3">
                <a href="{{route('category.index')}}" class="menu-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start ">
                                    <div class="stats-icon blue mb-2">
                                        <i class="fas fa-align-justify"></i>
                                    </div>
                                </div>
                                <div class="col-md-5 col-lg-12 col-xl-12 col-xxl-4">
                                    <h6 class="text-muted font-semibold">Category</h6>
                                    <h6 class="font-extrabold mb-0">{{$category}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>


            <!-- Dashboard Card 3: Radio_station -->
            <div class="col-md-3">
                <a href="{{route('radio-station.index')}}" class="menu-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start ">
                                    <div class="stats-icon green mb-2">
                                        <i class="fas fa-microphone"></i>
                                    </div>
                                </div>
                                <div class="col-md-5 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Radio-Station</h6>
                                    <h6 class="font-extrabold mb-0">{{$radioStation}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Dashboard Card 4: Slider -->
            <div class="col-md-3">
                <a href="{{route('slider.index')}}" class="menu-link">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start ">
                                    <div class="stats-icon red mb-2">
                                        <i class="fas fa-sliders-h"></i>
                                    </div>
                                </div>
                                <div class="col-md-5 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Slider</h6>
                                    <h6 class="font-extrabold mb-0">{{$slider}}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </section>
@endsection
