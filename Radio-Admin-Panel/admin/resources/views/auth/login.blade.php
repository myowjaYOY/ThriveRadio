<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/main/app.css') }}">
    <link rel="stylesheet" href=" {{ asset('assets/css/pages/auth.css') }}">
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.validate.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/extensions/toastify-js/src/toastify.css') }}">
    <link href="{{ asset('assets/js/jquery-toast-plugin/jquery.toast.min.css') }}" rel="stylesheet" type="text/css" />

    @include('layouts.include')
</head>

<body>
    <div id="auth" class="login_bg">
        <div class="row justify-content-end login-box">
            <div class="col-lg-4 col-12 card">
                <div id="auth-center">
                    @if(env('DEMO_MODE'))
                        <div class="alert alert-theme text-center" role="alert">
                            NOTE : <a target="_blank" href="{{ route('login') }}">-- Click Here --</a> If you Can't Login.
                        </div>
                    @endif
                    <div class="auth-logo mt-5" style="display: flex; justify-content: center; align-items: center;">
                        <img src="{{ env('LOGO') ? url(Storage::url(env('LOGO'))) : url('assets/images/logo/logo.png') }}" alt="Logo" height="50%"
                            width="50%"></a>
                    </div>
                    <div class="center mtop-70">
                        <div class="pt-4">
                            <form method="POST" id="loginform" class="loginform" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group position-relative form-floating mb-2">
                                    <input type="email" placeholder="Email" id="email" class="form-control form-input" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    <label for="floatingInput">Email</label>
                                </div>
                                <div class="form-group position-relative form-floating has-icon-right mb-2">
                                    <input type="password" id="password" placeholder="Password" class="form-control form-input" name="password" required autocomplete="current-password">
                                    <label for="floatingInput">Password</label>
                                    <div class="form-control-icon icon-right">
                                        <i class="bi bi-eye" id='togglePassword'></i>
                                    </div>
                                </div>

                                <button class="btn btn-primary btn-block btn-sm shadow-lg mt-4 p-2 login_btn">Login</button>

                                @if(env('DEMO_MODE'))
                                    <button class="btn btn-primary btn-block btn-sm shadow-lg mt-4 p-2 login_btn" style="background-color: #e24c6533; color: #b0506A" id="login_btn">Admin Login</button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script src="{{ asset('assets/js/custom/function.js') }}"></script>
    <script src="{{ asset('assets/extensions/toastify-js/src/toastify.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
    <script type="text/javascript">

    </script>
    <script type='text/javascript'>
        $(".loginform").validate({
            rules: {
                email: "required",
                password: "required",
            },
            success: function(label, element) {
                $(element).parent().removeClass('has-danger')
                $(element).removeClass('form-control-danger')
            },
            errorPlacement: function (label, element) {
                if(label.text()){
                    label.insertAfter(element.parent()).addClass('text-danger');
                }

            },
            highlight: function (element, errorClass) {
                $(element).parent().addClass('has-danger')
                $(element).addClass('form-control-danger')
            }
        });

        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function () {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            // this.classList.toggle("fa-eye");
            if (password.getAttribute("type") === 'password') {
                $('#togglePassword').addClass('bi bi-eye-slash');
                $('#togglePassword').removeClass('bi bi-eye');
            } else {
                $('#togglePassword').removeClass('bi bi-eye-slash');
                $('#togglePassword').addClass('bi bi-eye');
            }
        });

        @if(env('DEMO_MODE'))
            $('#login_btn').on('click', function (e) {
            $('#email').val('admin@gmail.com');
            $('#password').val('admin@123');
            $('#login_btn').attr('disabled', true);
            $(this).attr('disabled', true);
            $('#loginform').submit();
            })
        @endif
    </script>

    <script type="text/javascript">
        function showErrorToast(message) {
            $.toast({
                text: message,
                showHideTransition: 'slide',
                icon: 'error',
                loaderBg: '#f2a654',
                position: 'top-right'
            });
        }

        function showSuccessToast(message) {
            $.toast({
                text: message,
                showHideTransition: 'slide',
                icon: 'success',
                loaderBg: '#f96868',
                position: 'top-right'
            });
        }
        $('#loginform').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function () {
                    window.location.href = '/home';
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        // Show error toast for invalid credentials
                        showErrorToast(xhr.responseJSON['message']);
                    } else {
                        // Show generic error toast
                        showErrorToast('An error occurred. Please try again.');
                    }
                }
            });
        });
    </script>
    @php
    $theme_primary_color = getSettings('primarycolor');
    $theme_Background_color = getSettings('backgroundcolor');

    $theme_primary_color = $theme_primary_color['primarycolor'];
    $theme_Background_color =  $theme_Background_color['backgroundcolor'];

    @endphp
    <style>
        :root {
            --bs-primary-color: <?=$theme_primary_color ?>;
            --primary-color: <?=$theme_primary_color ?>;
        }

        body {
            background-color: <?=$theme_Background_color ?>;
        }
    </style>

</body>

</html>

