<link rel="stylesheet" href="{{ asset('assets/css/main/app.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/pages/otherpages.css') }}">
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css"
    rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/extensions/toastify-js/src/toastify.css') }}">
<link href="{{ asset('/assets/extensions/bootstrap-table/bootstrap-table.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/extensions/bootstrap-table/fixed-columns/bootstrap-table-fixed-columns.min.css') }}"
    rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/fontawesome/css/all.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/fontawesome/css/all.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('assets/css/bootstrap-table-reorder-rows.css') }}" rel="stylesheet" /> --}}
<link href="{{ asset('assets/bootstrap-table/reorder-rows.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/extensions/magnific-popup/magnific-popup.css') }}">
<link href="{{ asset('assets/extensions/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/extensions/select2/dist/css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/extensions/sweetalert2/sweetalert2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/extensions/chosen.css') }}" />
<link href="{{ asset('assets/css/filepond/filepond.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/filepond/filepond-plugin-image-preview.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/filepond/filepond-plugin-pdf-preview.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/pages/jquery-jvectormap-2.0.5.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/pages/owl.carousel.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/js/jquery-toast-plugin/jquery.toast.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/assets/color-picker/color.min.css') }}" async>
<link href="{{ asset('assets/css/iris.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/iris.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="{{ url(Storage::url(env('FAVICON'))) }}"/>
{{-- CHANGE PRIMARY COLOR AND BACKGROUND COLOR --}}
@php
    $theme_primary_color = getSettings('primarycolor');
    $theme_Background_color = getSettings('backgroundcolor');

    $theme_primary_color = $theme_primary_color['primarycolor'];
    $theme_Background_color =  $theme_Background_color['backgroundcolor'];

@endphp
@php
    $login_image = getSettings('bgimage');

    if($login_image!= null){
        $path = $login_image['bgimage'];
        $login_image = url(Storage::url($path));
    }
    else {
        $login_image = url(Storage::url('bgimage.jpg'));
    }

@endphp
<style>
    :root {
        --bs-primary-color: <?=$theme_primary_color ?>;
        --primary-color: <?=$theme_primary_color ?>;
        --image-url: url(<?=$login_image ?>);
    }

    body {
        background-color: <?=$theme_Background_color ?>;
    }
</style>

<script>
    var baseUrl = "{{ URL::to('/') }}";
</script>


<style>
    .fontawesome-icons {
        text-align: center;
    }

    article dl {
        background-color: rgba(0, 0, 0, 0.02);
        padding: 20px;
    }

    .fontawesome-icons .the-icon {
        font-size: 24px;
        line-height: 1.2;
    }
</style>
