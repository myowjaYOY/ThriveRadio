@extends('layouts.main')

@section('title')
    Manage App Settings
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
                        <h4>App Settings</h4>
                    </div>
                </div>
            </div>
            <div class="card-content">
                {{-- start system setting form --}}
                <form method="POST" enctype="multipart/form-data" class="form-horizontal form-label-left create-form" id="create-form" action="{{route('app-setting.update')}}">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="pt-3 row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>{{ __('App Link') }}</label>
                                    <input name="app_link"
                                        value="{{ isset($settings['app_link']) ? $settings['app_link'] : '' }}"
                                        type="url" required placeholder="{{ __('App Link') }}" class="form-control" />
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>{{ __('Ios App Link') }}</label>
                                    <input name="ios_app_link"
                                        value="{{ isset($settings['ios_app_link']) ? $settings['ios_app_link'] : '' }}"
                                        type="url" required placeholder="{{ __('Ios App Link') }}"
                                        class="form-control" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-3 col-sm-12">
                                    <label>{{ __('App Version') }}</label>
                                    <input name="app_version"
                                        value="{{ isset($settings['app_version']) ? $settings['app_version'] : '' }}"
                                        type="text" required placeholder="{{ __('App Version') }}"
                                        class="form-control" />
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label>{{ __('Ios App Version') }}</label>
                                    <input type="text" name="ios_app_version" required
                                        placeholder="{{ __('Ios App Version') }}" class="form-control"
                                        value="{{ isset($settings['ios_app_version']) ? $settings['ios_app_version'] : '' }}">
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label>{{ __('Force App Update') }}</label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input"
                                                value="{{ isset($settings['force_app_update']) ? $settings['force_app_update'] : 0 }}"
                                                id="force_app_update">{{ __('Force App Update') }}
                                            <i class="input-helper"></i></label>
                                    </div>
                                    <input type="hidden" name="force_app_update" id="txt_force_app_update">
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label>{{ __('App Maintenance') }}</label>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input"
                                                value="{{ isset($settings['app_maintenance']) ? $settings['app_maintenance'] : 0 }}"
                                                id="app_maintenance">{{ __('App Maintenance') }}
                                            <i class="input-helper"></i></label>
                                    </div>
                                    <input type="hidden" name="app_maintenance" id="txt_app_maintenance">
                                </div>
                            </div>
                            <div class="row mt-5  btn-right">
                                <div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                    <input id="btnadd" name="btnadd" value="Submit" type="submit" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script>
        function app_setting() {
            force_app_update = $('#force_app_update').val();
            app_maintenance = $('#app_maintenance').val();
            if (force_app_update == 1) {
                $('#force_app_update').attr('checked', true);
                $('#force_app_update').val(1);
                $('#txt_force_app_update').val(1);
            } else {
                $('#force_app_update').val(0);
                $('#txt_force_app_update').val(0);
            }
            if (app_maintenance == 1) {
                $('#app_maintenance').attr('checked', true);
                $('#app_maintenance').val(1);
                $('#txt_app_maintenance').val(1);
            } else {
                $('#app_maintenance').val(0);
                $('#txt_app_maintenance').val(0);
            }

            teacher_force_app_update = $('#teacher_force_app_update').val();
            teacher_app_maintenance = $('#teacher_app_maintenance').val();

            if (teacher_force_app_update == 1) {
                $('#teacher_force_app_update').attr('checked', true);
                $('#teacher_force_app_update').val(1);
                $('#teacher_txt_force_app_update').val(1);
            } else {
                $('#teacher_force_app_update').val(0);
                $('#teacher_txt_force_app_update').val(0);
            }
            if (teacher_app_maintenance == 1) {
                $('#teacher_app_maintenance').attr('checked', true);
                $('#teacher_app_maintenance').val(1);
                $('#teacher_txt_app_maintenance').val(1);
            } else {
                $('#teacher_app_maintenance').val(0);
                $('#teacher_txt_app_maintenance').val(0);
            }

        }
        $(document).ready(function() {
            app_setting();
        });
        $(document).on('change', '#force_app_update', function(e) {
            if ($('#force_app_update').val() == 1) {
                $('#force_app_update').val(0);
                $('#txt_force_app_update').val(0);
            } else {
                $('#force_app_update').val(1);
                $('#txt_force_app_update').val(1);
            }
        });
        $(document).on('change', '#app_maintenance', function(e) {
            if ($('#app_maintenance').val() == 1) {
                $('#app_maintenance').val(0);
                $('#txt_app_maintenance').val(0);
            } else {
                $('#app_maintenance').val(1);
                $('#txt_app_maintenance').val(1);
            }
        });

        $(document).on('change', '#teacher_force_app_update', function(e) {
            if ($('#teacher_force_app_update').val() == 1) {
                $('#teacher_force_app_update').val(0);
                $('#teacher_txt_force_app_update').val(0);
            } else {
                $('#teacher_force_app_update').val(1);
                $('#teacher_txt_force_app_update').val(1);
            }
        });
        $(document).on('change', '#teacher_app_maintenance', function(e) {
            if ($('#teacher_app_maintenance').val() == 1) {
                $('#teacher_app_maintenance').val(0);
                $('#teacher_txt_app_maintenance').val(0);
            } else {
                $('#teacher_app_maintenance').val(1);
                $('#teacher_txt_app_maintenance').val(1);
            }
        });
    </script>
@endsection
