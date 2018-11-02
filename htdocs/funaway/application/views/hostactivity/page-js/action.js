$(document).data("changed", false);
$(document).ready(function () {
    step1('#first-tb');

});

getSubService = function (obj) {
    $.ajax({
        url: fcom.makeUrl('services', 'sub-service'),
        type: 'post',
        data: {'service_id': $(obj).val()},
        success: function (json) {
            json = $.parseJSON(json);
            $('#subcat-list').html(json.msg);
        }
    });
}

step1 = function (obj) {
    makeActive(0, function () {

        jsonNotifyMessage('Loading...');


        $.ajax({
            url: fcom.makeUrl('hostactivity', 'step1'),
            type: 'post',
            success: function (json) {
                jsonRemoveMessage();
                json = $.parseJSON(json);
                $('.form-section').html(json.html);
                $('.request-team').modaal();
                $('.js-status-info').modaal();
                $('.js-booking-status-info').modaal();
                $('.js-activity-display-price').modaal();
                $('.date-popup').modaal({
                    width: 20
                });
                $('.js-commission-popup').modaal({
                    width: 20
                });
                $('.modaal-ajax').modaal({
                    type: 'ajax'
                });
                jQuery.fn.formModfied('step1');
                changeBooking('#booking-day');
                changeDuration('#duration-day');
            }
        });
    });
}

actionStep1 = function (v) {

    $('#step1').ajaxSubmit({
        delegation: true,
        beforeSubmit: function () {
            v.validate();
            if (!v.isValid()) {
                return false;
            }
            jsonStrictNotifyMessage();
        },
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == "1") {
                $(document).data("changed", false);
                jsonSuccessMessage(json.msg);
                step1();

            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}

step2 = function (obj) {

    makeActive(1, function () {

        jsonNotifyMessage('Loading...');
        $.ajax({
            url: fcom.makeUrl('hostactivity', 'step2'),
            type: 'post',
            success: function (json) {

                json = $.parseJSON(json);
                if (json.status == 1) {
                    jsonRemoveMessage();
                    jQuery.fn.formModfied('frmPhoto');
                    $('.form-section').html(json.html + json.form);
                    /* $('.form-section').append(json.form); */
                    bindImageUploader();
                } else {
                    jsonErrorMessage(json.msg);
                    if (json.step != null && json.step == 1) {
                        step1();
                        scrollUp();
                    }
                }
            }
        });
    });
}
bindImageUploader = function () {
    $('.modaal-ajax').modaal({
        type: 'ajax',
        after_open: function () {
            var settings = {
                'aspectRatio': 16 / 9,
                'url': fcom.makeUrl('croper', 'activityImage'),
                'afterSaveCallback': function () {

                    $('.modaal-ajax').modaal('close');
                    step2();

                }
            }
            var croper = new CropAvatar(settings);
        }
    });

};
actionStep2 = function (v) {

    $('#frmPhoto').ajaxSubmit({
        delegation: true,
        beforeSubmit: function () {
            v.validate();
            if (!v.isValid()) {
                return false;
            }
            jsonStrictNotifyMessage();
        },
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == "1") {
                $(document).data("changed", false);
                jsonSuccessMessage(json.msg);
                step2();

            } else {
                jsonErrorMessage(json.msg);
                if (json.step != null && json.step == 1) {
                    step1();
                    scrollUp();
                }
            }
        }
    });
}

function removeImage(fileId) {
    if (confirm('Are You Sure')) {
        jsonStrictNotifyMessage();
        $.ajax({
            url: fcom.makeUrl('hostactivity', 'remove-image'),
            type: 'post',
            data: {'file_id': fileId},
            success: function (json) {

                json = $.parseJSON(json);
                if (json.status == 1) {
                    jsonSuccessMessage(json.msg);
                    step2();
                } else {
                    jsonErrorMessage(json.msg);
                    if (json.step != null && json.step == 1) {
                        step1();
                        scrollUp();
                    }
                }
            }
        });
    }
}



step3 = function (obj) {
    makeActive(2, function () {

        jsonNotifyMessage('Loading...');

        $.ajax({
            url: fcom.makeUrl('hostactivity', 'step3'),
            type: 'post',
            success: function (json) {

                json = $.parseJSON(json);
                if (json.status == 1) {
                    jsonRemoveMessage();

                    $('.form-section').html(json.html + json.form);
                    jQuery.fn.formModfied('frmVideo');
                    $('.video--1').modaal({
                        type: 'video'
                    });
                    /* $('.form-section').append(json.form); */
                } else {
                    jsonErrorMessage(json.msg);
                    if (json.step != null && json.step == 1) {
                        step1();
                        scrollUp();
                    }
                }
            }
        });
    });
}

actionStep3 = function (v) {

    $('#frmVideo').ajaxSubmit({
        delegation: true,
        beforeSubmit: function () {
            v.validate();
            if (!v.isValid()) {
                return false;
            }
            jsonStrictNotifyMessage();
        },
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == "1") {
                jsonSuccessMessage(json.msg);
                $(document).data("changed", false);
                step3();
            } else {
                jsonErrorMessage(json.msg);
                if (json.step != null && json.step == 1) {
                    step1();
                    scrollUp();
                }

            }
        }
    });
}

function removeVideo(fileId) {
    if (confirm('Are You Sure')) {
        jsonStrictNotifyMessage();
        $.ajax({
            url: fcom.makeUrl('hostactivity', 'remove-video'),
            type: 'post',
            data: {'file_id': fileId},
            success: function (json) {

                json = $.parseJSON(json);
                if (json.status == 1) {
                    jsonSuccessMessage(json.msg);
                    step3();
                } else {
                    jsonErrorMessage(json.msg);
                    if (json.step != null && json.step == 1) {
                        step1();
                        scrollUp();
                    }

                }
            }
        });
    }
}


step4 = function (obj) {
    makeActive(3, function () {
        jsonNotifyMessage('Loading...');

        $.ajax({
            url: fcom.makeUrl('hostactivity', 'step4'),
            type: 'post',
            success: function (json) {
                jsonRemoveMessage();
                json = $.parseJSON(json);
                $('.form-section').html(json.html);
                jQuery.fn.formModfied('step4');


                $('.meeting-points').modaal();
                $('.cancellation-popup').modaal({
                    width: 20
                });
                if (json.step != null && json.step == 1) {
                    step1();
                    scrollUp();
                }

            }
        });
    });
}

actionStep4 = function (v) {

    $('#step4').ajaxSubmit({
        delegation: true,
        beforeSubmit: function () {
            v.validate();
            if (!v.isValid()) {
                return false;
            }
            jsonStrictNotifyMessage();
        },
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == "1") {
                jsonSuccessMessage(json.msg);
                $(document).data("changed", false);
            } else {
                jsonErrorMessage(json.msg);
                if (json.step != null && json.step == 1) {
                    step1();
                    scrollUp();
                }

            }
        }
    });
}


step5 = function (obj) {
    makeActive(4, function () {
        jsonNotifyMessage('Loading...');

        $.ajax({
            url: fcom.makeUrl('hostactivity', 'step5'),
            type: 'post',
            success: function (json) {

                json = $.parseJSON(json);
                if (json.status == 1) {
                    jsonRemoveMessage();
                    $('.form-section').html(json.html);
                    jQuery.fn.formModfied('frmMap');
                } else {
                    jsonErrorMessage(json.msg);
                    if (json.step != null && json.step == 1) {
                        step1();
                        scrollUp();
                    }

                }
            }
        });
    });
}

actionStep5 = function () {
    jsonStrictNotifyMessage();
    $('#frmMap').ajaxSubmit({
        delegation: true,
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == "1") {
                jsonSuccessMessage(json.msg);
                $(document).data("changed", false);
                $('#verify_change').val("");
            } else {
                jsonErrorMessage(json.msg);
                if (json.step != null && json.step == 1) {
                    step1('#first-tb');
                    scrollUp();
                }

            }
        }
    });
}



setDefault = function (image_id) {
    jsonStrictNotifyMessage();
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'default-image'),
        type: 'post',
        data: {'image_id': image_id},
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == "1") {
                jsonSuccessMessage(json.msg);
                step2();
            } else {
                jsonErrorMessage(json.msg);
                if (json.step != null && json.step == 1) {
                    step1('#first-tb');
                    scrollUp();
                }

            }
        }
    });
}

changeBooking = function (obj) {
    if ($(obj).val() == 100) {
        $("#booking-day-field").parents('.field-set').show();

    } else {
        $("#booking-day-field").parents('.field-set').hide();
    }
    return;
}
changeDuration = function (obj) {
    if ($(obj).val() == 100) {
        $("#duration-day-field").parents('.field-set').show();
    } else {
        $("#duration-day-field").parents('.field-set').hide();
    }
    return;
}


bindAddonImageUploader = function (addon_id) {
    $('.addon-modaal-ajax').modaal({
        type: 'ajax',
        after_open: function () {
            var settings = {
                'aspectRatio': 16 / 9,
                'url': fcom.makeUrl('croper', 'activityAddonImage', [addon_id]),
                'afterSaveCallback': function () {

                    $('.addon-modaal-ajax').modaal('close');
                    addonImages(addon_id);

                }
            }
            var croper = new CropAvatar(settings);
        }
    });

};


addonImages = function (addon_id) {

    if (!makeActive(6)) {
        return false;
    }
    jsonNotifyMessage('Loading...');
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'addonImages'),
        data: {addon_id: addon_id},
        type: 'post',
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonRemoveMessage();
                $('.form-section').html(json.msg);
                bindAddonImageUploader(addon_id);
            } else {
                jsonErrorMessage(json.msg);
                if (json.step != null && json.step == 1) {
                    step1('#first-tb');
                    scrollUp();
                }

            }
        }
    });
}

uploadAddonImage = function (v) {

    $('#addonImageFrm').ajaxSubmit({
        delegation: true,
        beforeSubmit: function () {
            v.validate();
            if (!v.isValid()) {
                return false;
            }
            jsonStrictNotifyMessage();
        },
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == "1") {
                $(document).data("changed", false);
                jsonSuccessMessage(json.msg);
                addonImages(json.addon_id);
            } else {
                jsonErrorMessage(json.msg);
                step7();
                scrollUp();

            }
        }
    });
}
removeAddonImage = function (image_id) {
    if (confirm('Do You want to delete?')) {
        jsonStrictNotifyMessage();
        fcom.ajax(fcom.makeUrl('hostactivity', 'removeAddonImage'), {image_id: image_id}, function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonSuccessMessage(json.msg);
                addonImages(json.addon_id);
            } else {
                jsonErrorMessage(json.msg);
            }
        });
    }
}

step7 = function (obj) {
    makeActive(6, function () {
        jsonNotifyMessage('Loading...');

        $.ajax({
            url: fcom.makeUrl('hostactivity', 'step7'),
            type: 'post',
            success: function (json) {

                json = $.parseJSON(json);
                if (json.status == 1) {
                    jsonRemoveMessage();
                    $('.form-section').html(json.form + json.html);
                    jQuery.fn.formModfied('frmAddons');
                    readlessmore();
                } else {
                    jsonErrorMessage(json.msg);
                    if (json.step != null && json.step == 1) {
                        step1('#first-tb');
                        scrollUp();
                    }

                }
            }
        });
    });
}

actionStep7 = function (v) {

    $('#frmAddons').ajaxSubmit({
        delegation: true,
        beforeSubmit: function () {
            v.validate();
            if (!v.isValid()) {
                return false;
            }
            jsonStrictNotifyMessage();
        },
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == "1") {
                $(document).data("changed", false);
                jsonSuccessMessage(json.msg);
                step7();
            } else {
                jsonErrorMessage(json.msg);
                if (json.step != null && json.step == 1) {
                    step1('#first-tb');
                    scrollUp();
                }

            }
        }
    });
}

editAddon = function (addon_id) {
    if (typeof addon_id == 'undefined' || addon_id == 'null') {
        return false;
    }
    jsonStrictNotifyMessage();
    fcom.ajax(fcom.makeUrl('hostactivity', 'editAddon'), {addon_id: addon_id}, function (json) {
        json = $.parseJSON(json);
        if (json.status == 1) {
            $('.form-section').html(json.msg);
            jsonRemoveMessage();
        } else {
            jsonErrorMessage(json.msg);
        }
    });

}

function removeAddon(fileId) {
    if (confirm('Are You Sure')) {
        jsonStrictNotifyMessage();
        $.ajax({
            url: fcom.makeUrl('hostactivity', 'remove-addons'),
            type: 'post',
            data: {'addon_id': fileId},
            success: function (json) {

                json = $.parseJSON(json);
                if (json.status == 1) {
                    jsonSuccessMessage(json.msg);
                    step7();
                } else
                    jsonErrorMessage(json.msg);
            }
        });
    }
}


step6 = function (obj) {
    makeActive(5, function () {
        jsonNotifyMessage('Loading...');

        $.ajax({
            url: fcom.makeUrl('hostactivity', 'step6'),
            type: 'post',
            success: function (json) {

                json = $.parseJSON(json);
                if (json.status == 1) {
                    jsonRemoveMessage();
                    $('.form-section').html(json.html);
                    $('.modaal-ajax').modaal({
                        type: 'ajax'
                    });
                    $('.available-instruction-popup').modaal();
                    $('.prior-instruction-popup').modaal();
                    $('.bulk-entry-instruction-popup').modaal();

                    jQuery.fn.formModfied('step6');
                    /* if($(".c-type").is(':disabled')){
                     $(".c-type:not(:disabled):first").trigger('click');
                     }
                     checkValue = $(".c-type:checked").val(); */
                } else {
                    jsonErrorMessage(json.msg);
                    if (json.step != null && json.step == 1) {
                        makeActive(0);
                    }

                }
            }
        });
    });
}

actionStep6 = function () {

    jsonStrictNotifyMessage();
    serviceType = $(".service-type:checked").val();
    confrimType = $(".confirm-type:checked").val();
    $('#time-slot-form').ajaxSubmit({
        delegation: true,
        data: {'year': $('.current-yr').text(), 'month': $('.current-mon').text()},
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == "1") {
                $(document).data("changed", false);
                jsonSuccessMessage(json.msg);
                currentMonth($('.current-yr').text(), $('.current-mon').text());
            } else {
                jsonErrorMessage(json.msg);
                if (json.step != null && json.step == 1) {
                    makeActive(0);
                }

            }
        }
    });
}

prevMonth = function (year, month) {
    jsonNotifyMessage('Loading...');
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'step6'),
        type: 'post',
        data: {'year': year, 'month': month, 'type': 'prev'},
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonRemoveMessage();
                $('.form-section').html(json.html);
                $('.modaal-ajax').modaal({
                    type: 'ajax'
                });

            } else
                jsonErrorMessage(json.msg);
        }
    });
}

nextMonth = function (year, month) {
    jsonNotifyMessage('Loading...');
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'step6'),
        type: 'post',
        data: {'year': year, 'month': month, 'type': 'next'},
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonRemoveMessage();
                $('.form-section').html(json.html);
                $('.modaal-ajax').modaal({
                    type: 'ajax'
                });

            } else
                jsonErrorMessage(json.msg);
        }
    });
}

currentMonth = function (year, month) {
    jsonNotifyMessage('Loading...');
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'step6'),
        type: 'post',
        data: {'year': year, 'month': month, 'type': 'current'},
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonRemoveMessage();
                $('.form-section').html(json.html);
                $('.modaal-ajax').modaal({
                    type: 'ajax'
                });

            } else
                jsonErrorMessage(json.msg);
        }
    });
}

entryOption = function (obj) {
    val = $(obj).val();
    serviceType = $(".service-type:checked").val()
    confrimType = $(".confirm-type:checked").val()
    service_type =
            $('#time-slot > .slots').remove();
    if (val == 1) {
        $("#time-slot").show();
        $("#week-slot").hide();
        $("#notime-slot").hide();
    } else if (val == 2) {
        $("#week-slot").show();
        $("#time-slot").hide();
        $("#notime-slot").hide();
    } else {
        $("#time-slot").hide();
        $("#notime-slot").hide();
        $("#week-slot").hide();
    }

    if (serviceType == 1) {
        $("#time-slot").hide();
        $("#notime-slot").show();
    }
}

onChangeTimeOption = function () {

    onChangeTime($(".serviceopt-type:checked").val());

}
onChangeTime = function (cVal) {
    if (cVal == 1) {
        $(".time-opt").hide();
    } else {
        $(".time-opt").show();
    }
}

addMoreTimeSlot = function () {
    $('#time-slot .form-element__control > div:first').append($('.time-slot-section').html());
}


$(document).on('click', '.remove-slot', function () {
    $(this).parent('div').parent('div').remove();
});

$(document).on('click', '.weekdays', function () {
    time_slot = 0;
    $('.weekdays').each(function () {
        if ($(this).is(':checked')) {
            time_slot = 1;

        }
    });
    if (time_slot == 1) {
        $("#time-slot").show();
    } else {
        $("#time-slot").hide();
    }
    serviceType = $(".service-type:checked").val();
    if (serviceType == 1) {
        $("#time-slot").hide();
        $("#notime-slot").show();
    }
});


deleteEvent = function (obj, eventId) {
    if (!confirm('Are You Sure?')) {
        return;
    }
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'delete-event'),
        data: {'event_id': eventId},
        type: 'post',
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonSuccessMessage(json.msg);
                currentMonth($('.current-yr').text(), $('.current-mon').text());
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
};


addNewEvent = function () {
    serviceType = $(".service-type:checked").val();
    confrimType = $(".confirm-type:checked").val();
    $('#new-event').ajaxSubmit({
        delegation: true,
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == "1") {
                $('.modaal-ajax').modaal('close');
                jsonSuccessMessage(json.msg);
                currentMonth($('.current-yr').text(), $('.current-mon').text());
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}

cleareMonthRecord = function () {
    if (!confirm('Are You Sure?')) {
        return;
    }
    $.ajax({
        url: fcom.makeUrl('hostactivity', 'delete-all-event'),
        data: {'year': $('.current-yr').text(), 'month': $('.current-mon').text()},
        type: 'post',
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonSuccessMessage(json.msg);
                currentMonth($('.current-yr').text(), $('.current-mon').text());
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}

serviceChange = function () {
    $(".entry-type").val("").change();
    $("#time-slot").hide();
    $("#notime-slot").hide();
}


function showMap(lat, lang, dragable) {

    L.mapbox.accessToken = mapbox_access_token;
    map = L.mapbox.map('map', 'mapbox.streets')
            .setView([lat, lang], 12);

    layers = {
        Streets: L.mapbox.tileLayer('mapbox.streets'),
        Outdoors: L.mapbox.tileLayer('mapbox.outdoors'),
        Satellite: L.mapbox.tileLayer('mapbox.satellite')
    };

    layers.Streets.addTo(map);
    L.control.layers(layers).addTo(map);
    marker = L.marker(new L.LatLng(lat, lang), {
        icon: L.mapbox.marker.icon({
            'marker-color': 'ff8888'
        }),
        draggable: true
    });
    marker.bindPopup('The marker is dragable! Move It around. ');
    marker.addTo(map);
    if (dragable == 1) {
        marker.on('dragend', ondragend);

        ondragend();
    } else {
        //	$(".inline").modaal();
    }
}

function ondragend() {
    m = marker.getLatLng();
    $('#act_lat').val(m.lat);
    $('#act_long').val(m.lng);
}

jQuery.fn.formModfied = function (frm_id) {

    $(document).data("changed", false);

    $('#' + frm_id).on('change', function () {
        $(document).data("changed", true);
    });

}

var confirmBox = function (n, succescallBack, FailureCallBack) {

    $('.confirm').modaal({
        type: 'confirm',
        confirm_button_text: 'Continue',
        confirm_cancel_button_text: 'Cancel',
        confirm_title: 'Warning',
        confirm_content: '<p>Please Save Unsave Content</p>',
        confirm_callback: succescallBack,
    });
}



makeActive = function (n, callback) {

    var isDataChanged = $(document).data("changed");
    var counterTab = n;
    if (isDataChanged == true) {


        confirmBox(n, function () {
            loadCurrentTab(n, callback);

        });
        $('.confirm').trigger("click");
        $('.confirm').remove();
        $('body').append('<a href="javascript:void(0);" class="confirm" style="display:none;" rel="confirm-6">Show</a>');
    } else {

        loadCurrentTab(n, callback);
    }

    return true;
}

loadCurrentTab = function (n, callback) {

    $(".js-menu-tab a").removeClass('active');
    $(".js-menu-tab ul li:eq(" + n + ") a").addClass("active");
    if (callback)
        callback();

}


selectAttr = function (el) {
    var data_attr = $.parseJSON($(el).attr('data-attr'));

    if ($(el).is(':checked')) {
        $('#attr_file_wrapper_' + data_attr.attr_id).show();
    } else {
        $('#attr_file_wrapper_' + data_attr.attr_id).hide();
    }

}
