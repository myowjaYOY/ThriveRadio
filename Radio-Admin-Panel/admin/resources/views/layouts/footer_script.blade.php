<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/filepond/filepond.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/js/filepond/filepond.jquery.js') }}"></script>
{{-- <script src="{{ asset('assets/js/custom/firebase_config.js') }}"></script> --}}
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="{{ asset('assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/extensions/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/js/custom/custom.js') }}"></script>
<script src="{{ asset('assets/js/custom/validate.js') }}"></script>
<script src="{{ asset('assets/js/custom/function.js') }}"></script>
<script src="{{ asset('assets/js/jquery-jvectormap-2.0.5.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-jvectormap-asia-merc.js') }}"></script>
<script src="{{ asset('assets/js/query-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('assets/js/jquery-jvectormap-world-mill.js') }}"></script>
<script src="{{ asset('assets/extensions/toastify-js/src/toastify.js') }}"></script>
<script src="{{ asset('assets/extensions/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/parsley.js') }}"></script>
<script src="{{ asset('assets/extensions/bootstrap-table/bootstrap-table.min.js') }}"></script>
<script src="{{ asset('assets/extensions/bootstrap-table/fixed-columns/bootstrap-table-fixed-columns.min.js') }}">
</script>
<script src="{{ asset('assets/js/jquery.tablednd.min.js') }}"></script>
{{-- <script src="{{ asset('assets/js/bootstrap-table-reorder-rows.min.js') }}"></script> --}}
<script src="{{ asset('assets/bootstrap-table/jquery.tablednd.min.js') }}"></script>
<script src="{{ asset('assets/bootstrap-table/reorder-rows.min.js') }}"></script>
<script src="{{ asset('assets/extensions/bootstrap-table/mobile/bootstrap-table-mobile.min.js') }}"></script>
<script src="{{ asset('assets/extensions/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/extensions/select2/dist/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/extensions/jquery-ui/jquery-ui.js') }}"></script>
<script src="{{ asset('assets/extensions/clipboardjs/dist/clipboard.min.js') }}"></script>
<script src="{{ asset('assets/js/filepond/filepond.min.js') }}"></script>
<script src="{{ asset('assets/js/filepond/filepond-plugin-image-preview.min.js') }}"></script>
<script src="{{ asset('assets/js/filepond/filepond-plugin-pdf-preview.min.js') }}"></script>
<script src="{{ asset('assets/js/filepond/filepond-plugin-file-validate-size.js') }}"></script>
<script src="{{ asset('assets/js/filepond/filepond-plugin-file-validate-type.js') }}"></script>
<script src="{{ asset('assets/js/filepond/filepond-plugin-image-validate-size.js') }}"></script>
<script src="{{ asset('assets/js/filepond/filepond.jquery.js') }}"></script>
<script src="{{ asset('assets/js/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
{{-- timezone in system setting --}}
<script src="{{ asset('/assets/color-picker/jquery-asColor.min.js') }}"></script>
<script src="{{ asset('/assets/color-picker/color.min.js') }}"></script>
<script src="{{ asset('assets/js/timezones.full.js') }}"></script>
<script src="{{ asset('assets/js/timezones.full.min.js') }}"></script>
<script src="{{ asset('assets/js/timezones.js') }}"></script>
<script src="{{ asset('assets/js/timezones.min.js') }}"></script>

{{-- color picker in system setting --}}
<script src="{{ asset('assets/js/iris-basic.min.js') }}"></script>
<script src="{{ asset('assets/js/iris.min.js') }}"></script>




{{-- @if (Session::has('success'))
    <script type="text/javascript">
        Toastify({
            text: '{{ Session::get('success') }}',
            duration: 6000,
            close: !0,
            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)"
        }).showToast()
    </script>
@endif


@if (Session::has('error'))
    <script type="text/javascript">
        Toastify({
            text: '{{ Session::get('error') }}',
            duration: 6000,
            close: !0,
            backgroundColor: '#dc3545' //"linear-gradient(to right, #dc3545, #96c93d)"
        }).showToast()
    </script>
@endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <script type="text/javascript">
            Toastify({
                text: '{{ $error }}',
                duration: 6000,
                close: !0,
                backgroundColor: '#dc3545' //"linear-gradient(to right, #dc3545, #96c93d)"
            }).showToast()
        </script>
    @endforeach
@endif --}}
