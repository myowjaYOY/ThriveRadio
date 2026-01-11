@extends('layouts.main')

@section('title')
    Manage Terms and Conditions
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
                        <h4>Terms and Conditions</h4>
                    </div>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group text-end">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <a href="{{url('terms-conditions')}}"><input  value="Terms & Conditions" type="submit" class="btn btn-primary"></a>
                            </div>
                        </div>
                    </div>
                    <!-- Terms Conditions form -->
                    <form method="POST" enctype="multipart/form-data" class="form-horizontal form-label-left terms-condition" id="terms-condition" action="{{route('update_terms_condition')}}">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="terms_condition" value="terms_condition">
                            <!-- Textarea for editing the Terms Conditions content -->
                            <div class="form-group col-md-12 mt-3">

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <textarea id="tinymce_editor" name="message" class="form-control col-md-12 col-xl-12 w-100">{{ isset($settings['terms_condition']) ? $settings['terms_condition'] : '' }}</textarea>
                                </div>
                            </div>
                            <!-- Submit button -->
                            <div class="mt-3 col-md-12  btn-right">
                                <div class="form-group text-end">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <input name="btnadd" value="Submit" type="submit" class="btn btn-primary">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    {{-- end term_condition form --}}
                </div>
            </div>
        </div>
    </section>
@endsection
