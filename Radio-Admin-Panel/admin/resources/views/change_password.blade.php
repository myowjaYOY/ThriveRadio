@extends('layouts.main')

@section('title')
    {{ __('Change Password') }}
@endsection
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="divider">
                    <div class="divider-text">
                        <h4>{{ __('Change Password') }}</h4>
                    </div>
                </div>
            </div>
            <div class="card-content">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" class="general-form" action="" id="changepassword"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row mt-1">
                                <div class="card-body">

                                    @if (Auth::user()->type == 0)
                                        <div class="form-group row">
                                            <label
                                                class="col-sm-4 col-form-label text-alert text-center">{{ __('Name') }}</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="name"
                                                    class="form-control form-control-lg form-control-solid mb-2"
                                                    placeholder={{ __('Name') }} value="{{ Auth::user()->name }}"
                                                    required readonly />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label
                                                class="col-sm-4 col-form-label text-alert text-center">{{ __('Email') }}</label>
                                            <div class="col-sm-4">
                                                {{-- enter  email --}}
                                                <input type="email" name={{ __('email') }}
                                                    class="form-control form-control-lg form-control-solid mb-2"
                                                    placeholder="Email" value="{{ Auth::user()->email }}" required />
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label text-alert text-center">{{ __('Current Password') }}</label>
                                        <div class="col-sm-4">
                                            <input type="password" name="current_password" id="current_password"
                                                   class="form-control form-control-lg form-control-solid mb-2" value=""
                                                   placeholder="Current password" required />
                                        </div>
                                    </div>
                                    <div class="form-group row" id="btncheckrow">
                                        <label class="col-sm-4 col-form-label text-alert">&nbsp;</label>
                                        <div class="col-sm-4">
                                            <button type="button" id="btncheck" class="btn btn-primary">{{ __('Check') }}</button>
                                        </div>
                                    </div>

                                    <div id="new_password_fields" style="display: none;">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label text-alert text-center">{{ __('New Password') }}</label>
                                            <div class="col-sm-4">
                                                <input type="password" name="new_password" id="new_password"
                                                       class="form-control form-control-lg form-control-solid mb-2" value=""
                                                       placeholder="New password" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label text-alert text-center">{{ __('Confirm New Password') }}</label>
                                            <div class="col-sm-4">
                                                <input type="password" name="confirm_new_password" id="confirm_new_password"
                                                       class="form-control form-control-lg form-control-solid mb-2" value=""
                                                       placeholder="Confirm new password" required />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label text-alert">&nbsp;</label>
                                            <div class="col-sm-4">
                                                <button type="button" id="btnchange" class="btn btn-primary">{{ __('Change Password') }}</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row" style="display: none">
                                        <label class="col-sm-4 col-form-label text-alert">&nbsp;</label>
                                        <div class="col-sm-4 text-end">
                                            <button type="submit" name="btnadd" value="btnadd"
                                                class="btn btn-primary float-right">{{ __('Change') }}</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>


        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {

            $('#current_password').on('focus', function() {
                $(this).attr('type', 'text');
            });

            $('#current_password').on('blur', function() {
                $(this).attr('type', 'password');
            });

            $('#new_password').on('focus', function() {
                $(this).attr('type', 'text');
            });

            $('#new_password').on('blur', function() {
                $(this).attr('type', 'password');
            });
            $('#confirm_new_password').on('focus', function() {
                $(this).attr('type', 'text');
            });

            $('#confirm_new_password').on('blur', function() {
                $(this).attr('type', 'password');
            });

            $('#btncheck').on('click', function() {
                var currentPassword = $('#current_password').val();
                var url = '{{ route("checkpassword") }}';
                var data = {
                    current_password: currentPassword
                };

                $.post(url, data, function(response) {
                    if (!response.error) {
                        $('#new_password_fields').show();
                        $('#btncheckrow').hide();
                        showSuccessToast(response.message); // Display success message
                    } else {
                        showErrorToast(response.message); // Display error message
                    }
                });
            });

            $('#btnchange').on('click', function() {
                var newPassword = $('#new_password').val();
                var confirmNewPassword = $('#confirm_new_password').val();
                var url = '{{ route("changepassword") }}';
                var data = {
                    new_password: newPassword,
                    confirm_new_password: confirmNewPassword
                };

                $.post(url, data, function(response) {
                    if (!response.error) {
                        showSuccessToast(response.message); // Display success message
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showErrorToast(response.message); // Display error message
                    }
                });
            });
        });

    </script>
@endsection
