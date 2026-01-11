@extends('layouts.main')

@section('title')
    Manage About us
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
        <div class="card">
            <div class="card-header">
                <div class="divider">
                    <div class="divider-text">
                        <h4>About us</h4>
                    </div>
                </div>
            </div>
            <div class="card-content">


                <div class="card-body">
                    <div class="row">
                        <div class="form-group text-end">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <a href="{{url('about-us')}}"><input value="About US" type="submit" class="btn btn-primary"></a>
                            </div>
                        </div>
                    </div>
                    <!-- About Us form -->
                    <form method="POST" enctype="multipart/form-data" class="form-horizontal form-label-left about-us-form" id="about-us-form" action="{{ route('update_about_us')}}">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="about_us" value="about_us">
                            <!-- Textarea for editing the About Us content -->
                            <div class="mt-3 form-group col-md-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <textarea id="tinymce_editor" name="message" class="form-control col-md-12 col-xl-12 w-100">{{ isset($settings['about_us']) ? $settings['about_us'] : '' }}</textarea>
                                </div>
                            </div>
                            <!-- Submit button -->
                            <div class="mt-3 col-md-12  btn-right">
                                <div class="form-group text-end">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input  value="Submit" type="submit" class="btn btn-primary">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    {{-- end about us form --}}
                </div>
            </div>
        </div>
    </section>
@endsection
