"use strict";

function errorPlacement(label, element) {
    label.addClass('mt-2 text-danger');
    if(label.text()){
        if (element.is(":radio") || element.is(":checkbox")) {
            label.insertAfter(element.parent().parent().parent());
        } else if (element.is(":file")) {
            label.insertAfter(element.parent('div'));
        } else if (element.hasClass('color-picker')) {
            label.insertAfter(element.parent());
        } else {
            label.insertAfter(element);
        }
    }
}

function highlight(element, errorClass) {
    if ($(element).hasClass('color-picker')) {
        $(element).parent().parent().addClass('has-danger')
    } else {
        $(element).parent().addClass('has-danger')
    }

    $(element).addClass('form-control-danger')
}

$(".create-form").validate({
    rules: {
        'name': "required",
        'image': "required"
    },
    success: function(label, element) {
        $(element).parent().removeClass('has-danger')
        $(element).removeClass('form-control-danger')
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".edit-form").validate({
    rules: {
        'name': "required",
    },
    success: function(label, element) {
        $(element).parent().removeClass('has-danger')
        $(element).removeClass('form-control-danger')
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});

$(".notification-form").validate({
    rules: {
        'name': "required",
    },
    success: function(label, element) {
        $(element).parent().removeClass('has-danger')
        $(element).removeClass('form-control-danger')
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});
$(".password-form").validate({
    rules: {
        'current_password': "required",
    },
    success: function(label, element) {
        $(element).parent().removeClass('has-danger')
        $(element).removeClass('form-control-danger')
    },
    errorPlacement: function (label, element) {
        errorPlacement(label, element);
    },
    highlight: function (element, errorClass) {
        highlight(element, errorClass);
    }
});
