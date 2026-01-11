@extends('layouts.main')

@section('title')
    Manage Notification Settings
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
                        <h4>Notification Settings</h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- start system setting form --}}
                <form method="POST" enctype="multipart/form-data" class="form-horizontal form-label-left create-form" id="create-form" action="{{route('update_notification_setting')}}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label col-md-12 col-sm-12">Project Id</label>
                            <input class="form-control" type="text" name="project_id" value="{{ isset($settings['project_id']) ? $settings['project_id'] : '' }}" placeholder="Enter Project Id" autofocus required />
                        </div>
                        <div class="col-md-6">
                            <label class="col-sm-12 col-form-label">{{ __('Service File') }}</label>
                            <div class="col-sm-12">
                                <input type="file" name="service_file" class="form-control mb-2"/>
                                @if (isset($settings['service_file']) ? $settings['service_file'] : '')
                                    <a href="{{Storage::url(isset($settings['service_file']) ? $settings['service_file'] : '')}}"><strong>Firebase Service File</strong></a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5  btn-right">
                        <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                            <input id="btnadd" name="btnadd" value="Submit" type="submit" class="btn btn-primary">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </section>
@endsection

