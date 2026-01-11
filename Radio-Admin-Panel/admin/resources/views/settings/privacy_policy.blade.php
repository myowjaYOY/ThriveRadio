@extends('layouts.main')

@section('title')
    Manage Privacy Policy
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
                        <h4>Privacy Policy</h4>
                    </div>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group text-end">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <a href="{{url('privacy-policy')}}"><input  value="Privacy Policy" type="submit" class="btn btn-primary"></a>
                            </div>
                        </div>
                    </div>
                    <!-- Privacy Policy form -->
                    <form method="POST" enctype="multipart/form-data" class="form-horizontal form-label-left privacy-policy" id="privacy-policy" action="{{route('update_privacy_policy')}}">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="privacy_policy" value="privacy_policy">
                            <!-- Textarea for editing the Privacy Policy content -->
                            <div class="mt-3 col-md-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <textarea id="tinymce_editor" name="message" class="form-control col-md-12 col-xl-12 w-100">{{ isset($settings['privacy_policy']) ? $settings['privacy_policy'] : '' }}</textarea>
                                </div>
                            </div>
                            <!-- Submit button -->
                            <div class="mt-3 col-md-12 btn-right">
                                <div class="form-group text-end">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input value="Submit" type="submit" class="btn btn-primary">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    {{-- privacy policy end form --}}
                </div>
            </div>
        </div>
    </section>
@endsection
