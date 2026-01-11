 /// START :: ACTIVE MENU CODE
$(document).ready(function ()
{
    $( ".menu a" ).each( function ()
    {
        var pageUrl = window.location.href.split( /[?#]/ )[ 0 ];

        if ( this.href == pageUrl ) {
            $( this ).parent().parent().addClass( "active" );
            $( this ).parent().addClass( "active" ); // add active to li of the current link
            $( this ).parent().parent().prev().addClass( "active" ); // add active class to an anchor
            $( this ).parent().parent().parent().addClass( "active" ); // add active class to an anchor
            $( this ).parent().parent().parent().parent().addClass( "active" ); // add active class to an anchor
        }

        var subURL = $( "a#subURL" ).attr( "href" );
        if ( subURL != 'undefined' ) {
            if ( this.href == subURL ) {

                $( this ).parent().addClass( "active" ); // add active to li of the current link
                $( this ).parent().parent().addClass( "active" );
                $( this ).parent().parent().prev().addClass( "active" ); // add active class to an anchor

                $( this ).parent().parent().parent().addClass( "active" ); // add active class to an anchor

            }
        }
    } );
    /// END :: ACTIVE MENU CODE

});
// This code is executed when the document is ready
$( document ).ready( function ()
{
    $( '.select2-selection__clear' ).hide();
} );
/// START :: TinyMCE
document.addEventListener( "DOMContentLoaded", () =>
{
    tinymce.init( {
        selector: '#tinymce_editor',
        height: 400,
        menubar: true,
        plugins: [
            'advlist autolink lists link charmap print preview anchor textcolor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime table contextmenu paste code help wordcount'
        ],

        toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        setup: function ( editor )
        {
            editor.on( "change keyup", function ( e )
            {
                //tinyMCE.triggerSave(); // updates all instances
                editor.save(); // updates this instance's textarea
                $( editor.getElement() ).trigger( 'change' ); // for garlic to detect change
            } );
        }
    } );
} );

$( 'body' ).append( '<div id="loader-container"><div class="loader"></div></div>' );
$( window ).on( 'load', function ()
{
    $( '#loader-container' ).fadeOut( 'slow' );
} );

//magnific popup
$( document ).on( 'click', '.image-popup-no-margins', function ()
{

    $( this ).magnificPopup( {
        type: 'image',
        closeOnContentClick: true,
        closeBtnInside: false,
        fixedContentPos: true,

        image: {
            verticalFit: true
        },
        zoom: {
            enabled: true,
            duration: 300 // don't foget to change the duration also in CSS
        }

    } ).magnificPopup( 'open' );
    return false;
} );


setTimeout( function ()
{
    $( ".error-msg" ).fadeOut( 1500 )
}, 5000 );

$( document ).ready( function ()
{
    $( '.select2' ).select2( {
        theme: 'bootstrap-5',
        placeholder: {
            id: '-1', // the value of the option
            text: 'Select an option'
        },
        allowClear: false


    } );
} );
//  Define functions show_error and confirmationDelete
function show_error ()
{
    Swal.fire( {
        title: 'Modification not allowed',
        icon: 'error',
        showDenyButton: true,

        confirmButtonText: 'Yes',
        denyCanceButtonText: `No`,
    } ).then( ( result ) =>
    {
        /* Read more about isConfirmed, isDenied below */

    } )
}
function confirmationDelete ( e )
{
Swal.fire( {
        title: 'Modification not allowed',
        icon: 'error',
        showDenyButton: true,

        confirmButtonText: 'Yes',
        denyCanceButtonText: `No`,
    } ).then( ( result ) =>
    {
        /* Read more about isConfirmed, isDenied below */
    } )

    var url = e.currentTarget.getAttribute( 'href' ); //use currentTarget because the click may be on the nested i tag and not a tag causing the href to be empty

    $( '#form-del' ).attr( 'action', url );
    Swal.fire( {
        title: 'Are You Sure Want to Delete This Record??',
        icon: 'error',
        showDenyButton: true,

        confirmButtonText: 'Yes',
        denyCanceButtonText: `No`,
    } ).then( ( result ) =>
    {
        /* Read more about isConfirmed, isDenied below */
        if ( result.isConfirmed ) {
            $( "#form-del" ).submit();
        } else {
            $( '#form-del' ).attr( 'action', '' );
        }
    } )
    return false;
}
$( document ).ready( function ()
{
    $('.select2').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),
            theme: 'bootstrap-5',
        });
    });

    FilePond.registerPlugin( FilePondPluginImagePreview, FilePondPluginFileValidateSize,
        FilePondPluginFileValidateType );

    $( '.filepond' ).filepond( {
        credits: null,
        allowFileSizeValidation: "true",
        maxFileSize: '25MB',
        labelMaxFileSizeExceeded: 'File is too large',
        labelMaxFileSize: 'Maximum file size is {filesize}',
        allowFileTypeValidation: true,
        acceptedFileTypes: [ 'image/*' ],
        labelFileTypeNotAllowed: 'File of invalid type',
        fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}',
        storeAsFile: true,
        allowPdfPreview: true,
        pdfPreviewHeight: 320,
        pdfComponentExtraParams: 'toolbar=0&navpanes=0&scrollbar=0&view=fitH',
        allowVideoPreview: true, // default true
        allowAudioPreview: true // default true
    });

    function resetFilepond() {
        $('.filepond').filepond('removeFiles');
    }

    // Call resetFilepond() function to clear Filepond when document is ready
    resetFilepond();

} );

$('#create-form,.create-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        formElement[0].reset();
        setTimeout(function () {
            location.reload();
        }, 1000)
        $('#table_list').bootstrapTable('refresh');
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('#edit-form,.edit-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');

    let data = new FormData(this);
    data.append("_method", "PUT");
    function successCallback(response) {
        setTimeout(function () {
            location.reload();
            $('#editModal').modal('hide');
        }, 1000)
        formElement[0].reset();
        $('#table_list').bootstrapTable('refresh');
    }
    function errorCallback(response) {
        setTimeout(function () {
            $('#editModal').modal('hide');
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback, errorCallback);
})

$(document).on('click', '.delete-form', function (e) {
    e.preventDefault();
    Swal.fire({
        title: 'Are you sure you want to delete this?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#b0506a',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            let url = $(this).attr('href');
            let data = null;

            function successCallback(response) {
                $('#table_list').bootstrapTable('refresh');
                showSuccessToast(response.message);
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest('DELETE', url, data, null, successCallback, errorCallback);
        }
    })
})

$('#about-us-form,.about-us-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        formElement[0].reset();
        setTimeout(function () {
            location.reload();
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('#privacy-policy,.privacy-policy').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        formElement[0].reset();
        setTimeout(function () {
            location.reload();
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('#terms-condition,.terms-condition').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        formElement[0].reset();
        setTimeout(function () {
            location.reload();
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})

$('#change-sequence-slider').click(async function () {
    const ids = await $('#table_list').bootstrapTable('getData').map(function (row) {
        return row.id;
    });
    $.ajax({
        type: "post",
        url: baseUrl + "/slider-change-sequence",
        data: {
            ids: ids
        },
        dataType: "json",
        success: function (data) {
            $('#table_list').bootstrapTable('refresh');
            if (!data.error) {
                showSuccessToast(data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showErrorToast(data.message);
            }
        }
    });
})
$('#password-form, .password-form').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        formElement[0].reset();
        setTimeout(function () {
            location.reload();
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
$('#system-update,.system-update').on('submit', function (e) {
    e.preventDefault();
    let formElement = $(this);
    let submitButtonElement = $(this).find(':submit');
    let url = $(this).attr('action');
    let data = new FormData(this);

    function successCallback() {
        formElement[0].reset();
        setTimeout(function () {
            location.reload();
        }, 1000)
    }

    formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
})
