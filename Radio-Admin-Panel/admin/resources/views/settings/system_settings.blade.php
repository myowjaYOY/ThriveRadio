@extends('layouts.main')

@section('title')
    Manage Settings
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
                        <h4>System Settings</h4>
                    </div>
                </div>
            </div>
            <div class="card-content">
                {{-- start system setting form --}}
                <form method="POST" enctype="multipart/form-data" class="form-horizontal form-label-left create-form" id="create-form" action="{{route('update_settings')}}">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <h4>General Settings</h4>
                            <hr>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label col-md-12 col-sm-12">App Name</label>
                                    <input class="form-control" type="text" id="app_name" name="app_name" value="{{ isset($settings['app_name']) ? $settings['app_name'] : '' }}" placeholder="Enter App Name" autofocus required />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label col-md-12 col-sm-12">Email</label>
                                    <input class="form-control" type="email" id="email" name="email" value="{{ isset($user->email) ? $user->email : '' }}" placeholder="Enter Email" autofocus required />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label col-md-12 col-sm-12">Mobile No.</label>
                                    <input class="form-control" type="text" id="mobile" name="mobile" value="{{ isset($user->mobile) ? $user->mobile : '' }}" placeholder="Enter Mobile" autofocus required />
                                </div>
                                <div class="col-md-4">
                                    <label class="col-sm-12 col-form-label">{{ __('Timezone') }}</label>
                                    <div class="col-sm-12">
                                        <select class="form-control" name="timezone">
                                            @foreach ($timezones as $timezone)
                                            <option value="@php  echo $timezone[1]; @endphp"
                                                {{ isset($settings['timezone']) ? ($settings['timezone'] == $timezone[1] ? 'selected' : '') : '' }}>
                                                @php  echo '(GMT '. $timezone[0] . ') '. $timezone[1] @endphp</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="col-sm-12 col-form-label">{{ __('Primary Color') }}</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="primarycolor" value="{{ isset($settings['primarycolor']) ? $settings['primarycolor'] : '' }}" name="primarycolor" class="color-picker" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="col-sm-12 col-form-label">{{ __('Background Color') }}</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="backgroundcolor" value="{{ isset($settings['backgroundcolor']) ? $settings['backgroundcolor'] : '' }}" name="backgroundcolor" class="color-picker" />
                                    </div>
                                </div>
                                {{-- <div class="col-md-4">
                                    <label class="col-sm-12 col-form-label">{{ __('Dark Theme Primary Color') }}</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="darkprimarycolor" value="{{ isset($settings['darkprimarycolor']) ? $settings['darkprimarycolor'] : '' }}" name="darkprimarycolor" class="color-picker" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="col-sm-12 col-form-label">{{ __('Dark Theme Background Color') }}</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="darkbackgroundcolor" value="{{ isset($settings['darkbackgroundcolor']) ? $settings['darkbackgroundcolor'] : '' }}" name="darkbackgroundcolor" class="color-picker" />
                                    </div>
                                </div> --}}
                                <div class="col-md-4">
                                    <label class="col-sm-12 col-form-label ">{{ __('Admin Image') }}</label>
                                    <div class="col-sm-12">
                                        <input class="filepond" type="file" name="admin_image" id="admin_image">
                                        <img src="{{ isset($user->image) ? $user->image : '' }}" class="mt-2" alt="image" style="height: 100px;width: 100px;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="col-sm-12 col-form-label ">{{ __('Favicon Icon') }}</label>
                                    <div class="col-sm-12">
                                        <input class="filepond" type="file" name="favicon" id="favicon" accept="image/*">
                                        <img src="{{ isset($settings['favicon']) ? url(Storage::url($settings['favicon'])) : url('assets/images/logo/favicon.png') }}" class="mt-2 favicon_icon" alt="image" style="height: 100px;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="col-sm-12 col-form-label ">{{ __('Company Logo') }}</label>
                                    <div class="col-sm-12">
                                        <input class="filepond" type="file" name="logo" id="logo" accept="image/*">
                                        <img src="{{ isset($settings['logo']) ? url(Storage::url($settings['logo'])) : url('assets/images/logo/logo.png') }}" class="mt-2 company_logo" alt="image"
                                            style="height: 100px;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="col-sm-12 col-form-label ">{{ __('Background Image') }}</label>
                                    <div class="col-sm-12">
                                        <input class="filepond" type="file" name="bgimage" id="bgimage" accept="image/*">
                                        <img src="{{ isset($settings['bgimage']) ? url(Storage::url($settings['bgimage'])) : url('/assets/images/bg/bgimage.jpg') }}" class="mt-2 bgimage" alt="image"
                                            style="height: 100px;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch mt-5">
                                        <input type="hidden" name="city_mode" id="city_mode" value="{{isset($settings['city_mode']) ? $settings['city_mode'] : '' }}">
                                        <input class="form-check-input" type="checkbox" role="switch" id="switch_city_mode" {{ isset($settings['city_mode']) && $settings['city_mode'] == '1' ? 'checked' : '' }}/>
                                        <label class="form-check-label" for="switch_city_mode">{{ __('City Mode') }}</label>
                                    </div>
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
@section('script')
    <script type="text/javascript">
        $(document).ready(function($) {

            // city mode

            $("#switch_city_mode").on('change', function() {
                $("#switch_city_mode").is(':checked') ? $("#city_mode").val(1) : $("#city_mode")
                    .val(0);
            });

        });
    </script>
    <script type='text/javascript'>
        if ($(".color-picker").length) {
            $('.color-picker').asColorPicker();
        }
    </script>
@endsection
