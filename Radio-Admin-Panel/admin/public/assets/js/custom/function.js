"use strict";

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
//Setup CSRF Token default in AJAX Request
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
function ajaxRequest(type, url, data, beforeSendCallback, successCallback, errorCallback, finalCallback, processData = false) {
    /*
    * @param
    * beforeSendCallback : This function will be executed before Ajax sends its request
    * successCallback : This function will be executed if no Error will occur
    * errorCallback : This function will be executed if some error will occur
    * finalCallback : This function will be executed after all the functions are executed
    */
    $.ajax({
        type: type,
        url: url,
        data: data,
        cache: false,
        processData: processData,
        contentType: false,
        dataType: 'json',
        beforeSend: function () {
            if (beforeSendCallback != null) {
                beforeSendCallback();
            }
        },
        success: function (data) {
            if (!data.error) {
                if (successCallback != null) {
                    successCallback(data);
                }
            } else {
                if (errorCallback != null) {
                    errorCallback(data);
                }
            }

            if (finalCallback != null) {
                finalCallback(data);
            }
        }, error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseJSON) {
                showErrorToast(jqXHR.responseJSON.message);
            }
            if (finalCallback != null) {
                finalCallback();
            }
        }
    })
}

function formAjaxRequest(type, url, data, formElement, submitButtonElement, successCallback, errorCallback) {
    // To Remove Red Border from the Validation tag.
    formElement.find('.has-danger').removeClass("has-danger");
    formElement.validate();
    if (formElement.valid()) {
        let submitButtonText = submitButtonElement.val();

        function beforeSendCallback() {
            submitButtonElement.val('Please Wait...').attr('disabled', true);
        }

        function mainSuccessCallback(response) {
            showSuccessToast(response.message);
            if (successCallback != null) {
                successCallback(response);
            }
        }

        function mainErrorCallback(response) {
            showErrorToast(response.message);
            if (errorCallback != null) {
                errorCallback(response);
            }
        }

        function finalCallback(response) {
            submitButtonElement.val(submitButtonText).attr('disabled', false);
        }

        ajaxRequest(type, url, data, beforeSendCallback, mainSuccessCallback, mainErrorCallback, finalCallback)
    }
}
function imageFormatter(value,row)
{
    if (row.image) {
        return "<a class='image-popup-no-margins' href='" + row.image + "'><img class='rounded avatar-md shadow img-fluid' alt='image' src='"+  row.image +"' width='60'></a>";
    } else {
        return '-'
    }
}

$('#city_id').on('change', function(){

    let city_id =  $(this).val();

    let url = baseUrl + '/get-category/' + city_id;
    $('#category_id option').hide();

    function successCallback(response) {
        let html = ''
        if (response.data) {
            html = '<option value="">Select Category</option>';
            $.each(response.data, function (key, data) {
                html += '<option value=' + data.id + '>' + data.name + '</option>';
            });
        } else {
            html = '<option>No Category Found</option>';
        }
        $('#category_id').html(html);
    }

    ajaxRequest('GET', url, null, null, successCallback, null);

});

$('#category_id').on('change', function () {

    let city_id = $('#city_id').val();
    let category_id = $(this).val();
    if(city_id)
    {
        let url = baseUrl + '/get-radio-stations/' + city_id + '/' + category_id;
        $('#radio_station_id option').hide();

        function successCallback(response) {
            let html = ''
            html = '<option>No Radio Station</option>';
            if (response.data) {
                html = '<option value="">Select Radio Station</option>';
                $.each(response.data, function (key, data) {
                    html += '<option value=' + data.id + ' data-city-id= '+ data.city_id + ' data-category-id= '+ data.category_id +'>' + data.name + '</option>';
                });
            } else {
                html = '<option>No Radio Station Found</option>';
            }
            $('#radio_station_id').html(html);
        }

        ajaxRequest('GET', url, null, null, successCallback, null);

    }else{
            let url = baseUrl + '/get-radio-station-by-category/'+ category_id;
        $('#radio_station_id option').hide();

        function successCallback(response) {
            let html = ''
            html = '<option>No Radio Station</option>';
            if (response.data) {
                html = '<option value="">Select Radio Station</option>';
                $.each(response.data, function (key, data) {
                    html += '<option value=' + data.id + ' data-category-id= '+ data.category_id +'>' + data.name + '</option>';
                });
            } else {
                html = '<option>No Radio Station Found</option>';
            }
            $('#radio_station_id').html(html);
        }

        ajaxRequest('GET', url, null, null, successCallback, null);
    }

});

$('#edit_city_id').on('change', function () {
    let city_id = $('#edit_city_id').val();
    let category_id = $('#edit_category_id').val();
    $('#edit_category_id').empty();
    $.ajax({
        type: "GET",
        url: "get-category/" + city_id,
        dataType: 'json',
        success: function(response) {
            $('#edit_category_id').empty();
            if (response.error == false) {
                $('#edit_category_id').append($('<option>', {
                    value: '',
                    text: 'Select Category'
                }));
                $.each(response.data, function(i, item) {
                    var text_name = item.name;
                    $('#edit_category_id').append($('<option>', {
                        value: item.id,
                        text: text_name
                    }));
                });
                $('#edit_category_id').val(category_id).trigger('change.select2');
            } else {
                $('.dropdown_my_category').empty();
            }
        }
    });

    $('#edit_radio_station_id').empty();
});

$('#edit_category_id').on('change', function () {
    let category_id = $('#edit_category_id').val();
    let city_id = $('#edit_city_id').val();
    let radio_station_id = $('#edit_radio_station_id').val();
    if(city_id)
        {
            $.ajax({
                type: "GET",
                url: "get-radio-stations/" + city_id + "/" + category_id,
                dataType: 'json',
                success: function(response) {
                    $('#edit_radio_station_id').empty();
                    if (response.error == false) {
                        $('#edit_radio_station_id').append($('<option>', {
                            value: '',
                            text: 'Select Radio Station'
                        }));
                        $.each(response.data, function(i, item) {
                            var text_name = item.name;
                            $('#edit_radio_station_id').append($('<option>', {
                                value: item.id,
                                text: text_name
                            }));
                        });
                        $('#edit_radio_station_id').val(radio_station_id).trigger('change.select2');
                    } else {
                        $('.dropdown_my_radio').empty();
                    }
                }
            });
        }
        else{
            $.ajax({
                type: "GET",
                url: "/get-radio-station-by-category/" + row.category_id,
                dataType: 'json',
                success: function(response) {
                    $('#edit_radio_station_id').empty();
                    if (response.error == false) {
                        $('#edit_radio_station_id').append($('<option>', {
                            value: '',
                            text: 'Select Radio Station'
                        }));
                        $.each(response.data, function(i, item) {
                            var text_name = item.name;
                            $('#edit_radio_station_id').append($('<option>', {
                                value: item.id,
                                text: text_name
                            }));
                        });
                        $('#edit_radio_station_id').val(radio_station_id).trigger('change.select2');
                    } else {
                        $('.dropdown_my_radio').empty();
                    }
                }
            });
        }

});
