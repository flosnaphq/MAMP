//////////////////////////////////////////////// calendar \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
///////////////////////////////////////////////////////////////////////
$(document).ready(function () {
    /////////////////////////////////////////////////////////////////////
    $('.js-click').click(function () {
        $('.js-click').removeClass("current");
        $(this).addClass('current');
        val = $(this).attr("rel");
        $(".tab__content").hide();
        $("#" + val).show();
        $('.js-carousel').slick("unslick");
        $('.js-carousel').slick();
    });

    ///////////////////////////////////////////////////////////////////
    $('.share-activity').modaal({type: 'ajax'});
    $(".send-msg").modaal();
    $(".write-review").modaal();
    $(".activity-abuse").modaal();
    $("body").on("click", ".crating", function (e) {
        var offset = $(this).offset();
        points = e.clientX - offset.left;
        widths = $(this).width();
        setWidth = parseInt(points * 100 / widths);
        if (setWidth % 10 >= 5 && setWidth % 10 != 0)
            setWidth = setWidth - setWidth % 10 + 10;
        if (setWidth % 10 < 5 && setWidth % 10 != 0)
            setWidth = setWidth - setWidth % 10;
        setWidth = setWidth | 0;
        if (setWidth == 0)
            setWidth = 10;
        $(this).children(".rating__score").css("width", setWidth + "%");
        $(".ratesfld").val(setWidth / 20);
    });



    $(".view-review").modaal();
    calendar();
    review(1);
    listing();
    $('#book-now-section').on('click', '.calc-dt', function () {

        $('.calc-dt').removeClass('selectedDate');

        $(this).addClass('selectedDate');

        date = $(this).html();
        year = $('#cal-year').attr('rel');
        month = $('#cal-month').attr('rel');

        activity_id = $('#activityFld').html();
        $.ajax({
            url: fcom.makeUrl('activity', 'event'),
            type: 'post',
            data: {'activity_id': activity_id, 'month': month, 'year': year, 'date': date},
            beforeSend: function () {
                fillWithLoader('#book-card-event');
            },
            success: function (json) {
                json = $.parseJSON(json);
                if (json.status == 1) {
                    $('#book-card-event').html(json.html);
                } else {
                    jsonErrorMessage(json.msg);
                }
            }
        });
    });

});

calendar = function () {
    activity_id = $('#activityFld').html();
    jsonNotifyMessage('Calender Loading...');
    $.ajax({
        url: fcom.makeUrl('activity', 'calendar'),
        type: 'post',
        data: {'activity_id': activity_id},
        success: function (json) {

            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonRemoveMessage();
                $('#book-now-section').html(json.html);

            } else
                jsonErrorMessage(json.msg);
        }
    });
}
listing = function (page) {
    if (typeof page === 'undefined') {
        page = 1;
    }
    jsonNotifyMessage('Loading...');

    currentPage = page;
    activity_id = $('#activityFld').html();
    fcom.ajax(fcom.makeUrl("city", "activities"), {'city_id': city_id, activity_id: activity_id}, function (json) {
        json = $.parseJSON(json);
        if (json.status == 1) {
            jsonRemoveMessage('');
            if (json.noResult == 1) {
                return false;
            }

            if (json.see_all == 1) {
                $('#see-all-activity').show();
            }
            $('#activities').show();
            $('#island-activities-list').html(json.msg);

        }
        else {
            jsonErrorMessage(json.msg);
        }
    });

}




prevMonth = function (year, month) {
    activity_id = $('#activityFld').html();
    $.ajax({
        url: fcom.makeUrl('activity', 'calendar'),
        type: 'post',
        data: {'year': year, 'month': month, 'type': 'prev', 'activity_id': activity_id},
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                $('#book-now-section').html(json.html);

            } else
                jsonErrorMessage(json.msg);
        }
    });
}

nextMonth = function (year, month) {
    activity_id = $('#activityFld').html();
    $.ajax({
        url: fcom.makeUrl('activity', 'calendar'),
        type: 'post',
        data: {'year': year, 'month': month, 'type': 'next', 'activity_id': activity_id},
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                $('#book-now-section').html(json.html);

            } else
                jsonErrorMessage(json.msg);
        }
    });
}

currentMonth = function (year, month) {
    activity_id = $('#activityFld').html();
    $.ajax({
        url: fcom.makeUrl('activity', 'calendar'),
        type: 'post',
        data: {'year': year, 'month': month, 'type': 'current', 'activity_id': activity_id},
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                $('#book-now-section').html(json.html);

            } else
                jsonErrorMessage(json.msg);
        }
    });
}

minusMem = function () {
    vals = parseInt($("#memberCount").val());
    if (vals <= 1) {
        jsonErrorMessage("Please Set Valid No of Members.");
        return false;
    }

    if (vals > 1) {
        $("#memberCount").val(vals - 1);
        updatePrice();
    }
}

plusMem = function () {
    vals = parseInt($("#memberCount").val());
    
    if (vals >= activityMemberCount) {
        jsonErrorMessage("Total Available Seats are "+activityMemberCount);
        return false;
    }

    $("#memberCount").val(vals + 1);
    updatePrice();
}


writeReview = function (activity_id) {
    jsonNotifyMessage();
    $.ajax({
        url: fcom.makeUrl('reviews', 'form'),
        type: 'post',
        data: {'activity_id': activity_id},
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                jsonRemoveMessage();
                $('.modaal-content-container').html(json.msg);

            } else {
                jsonErrorMessage(json.msg);
                $('.write-review').modaal('close');
            }
        }
    });
}

submitReview = function (v) {
    $('#reviewForm').ajaxSubmit({
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
                $('.write-review').modaal('close');
                review();
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}


sendMsg = function (activity_id) {
    jsonNotifyMessage('Loading...');
    fcom.ajax(fcom.makeUrl('message', 'form'), {activity_id: activity_id}, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            jsonRemoveMessage();
            popupReply = true;
            $('.modaal-content-container').html(json.msg);
            //$.facebox(json.msg);
        } else {
            jsonErrorMessage(json.msg);
            $('.send-msg').modaal('close');
        }
    });
}

submitForm = function (v, form_id, thread) {
    var action_form = $('#' + form_id);
    v.validate();
    if (!v.isValid()) {
        return false;
    }
    data = fcom.frmData(action_form);

    jsonStrictNotifyMessage('Sending...');
    fcom.ajax($(action_form).attr('action'), data, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            $(action_form)[0].reset();
            jsonSuccessMessage(json.msg);
            //$.facebox.close();
            $('.send-msg').modaal('close');
        } else {

            jsonErrorMessage(json.msg);
        }
    });
    return false;

}



$(document).ready(function () {
    $('.addons-type').change(function () {
        updatePrice();
    });
})

validateEvent = function (obj, activityId) {
    jsonNotifyMessage();
    var eventSelected = $(obj).val();
    $.ajax({
        url: fcom.makeUrl("Activity", "validateEvent"),
        data: {"activity_id": activityId, "selevent": eventSelected, fIsAjax: 1},
        type: "post",
        success: function (json) {
            json = $.parseJSON(json);
            if (1 != json.status)
            {
                        
                jsonErrorMessage(json.msg);

            }
            else
            {
                activityMemberCount = json.availableSeats;
                jQuery.fn.openBlock('persons');
                jsonRemoveMessage();
                updatePrice();
            }
        }
    });
}

updatePrice = function () {
    $("#book-now").addClass("button--disabled");
    date = $(".selectedDate").html();
    year = $('#cal-year').attr('rel');
    month = $('#cal-month').attr('rel');
    memberCount = $("#memberCount").val();
    event_id = $("#eventOption").val();
    activity_id = $('#activityFld').html();
    flag = 1;
    if (typeof date == "" || typeof date === 'undefined') {
        flag = 0;
        jsonNotifyMessage("Please Select Any Date");
    }

    /* if(year == "" || typeof year === 'undefined'){
     flag = 0;
     }
     
     if(month == "" || typeof month === 'undefined'){
     flag = 0;
     } */

    if (typeof memberCount === 'undefined' || memberCount == "") {
        jsonNotifyMessage("Please Set Number Of Members");
        flag = 0;
    }

    if (typeof event_id === 'undefined' || event_id == "") {
        jsonNotifyMessage("Please Select An Event.");
        flag = 0;
    }

    if (flag == 0) {
        $("#book-now").addClass("button--disabled");
        return false;
    }



    addonss = {};
    $(".addons-type").each(function () {
        if ($(this).val() != 0 && $(this).val() != "") {
            addonss["" + $(this).attr('rel')] = $(this).val();
        }
    });


    $.ajax({
        url: fcom.makeUrl('cart', 'price'),
        type: 'post',
        data: {'date': date,
            'year': year,
            'month': month,
            'member_count': memberCount,
            'event_id': event_id,
            'activity_id': activity_id,
            'addons': addonss
        },
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                $("#book-now").removeClass("button--disabled");
                $('.priceOpt').html(json.msg);
            } else {
                jsonErrorMessage(json.msg);

            }
        }
    });
}

addInCart = function () {
    facebookTrackEvent();
    $("#book-now").addClass("button--disabled");
    date = $(".selectedDate").html();
    year = $('#cal-year').attr('rel');
    month = $('#cal-month').attr('rel');
    memberCount = $("#memberCount").val();
    event_id = $("#eventOption").val();
    activity_id = $('#activityFld').html();
    flag = 1;
    if (typeof date == "" || typeof date === 'undefined') {
        flag = 0;
        jsonNotifyMessage("Hey! You need to select a date first.");
    }


    if (typeof memberCount === 'undefined' || memberCount == "") {
        jsonNotifyMessage("Please Set Number Of Members");
        flag = 0;
    }

    if (typeof event_id === 'undefined' || event_id == "") {
        jsonNotifyMessage("Please Select An Event.");
        flag = 0;
    }

    if (flag == 0) {
        $("#book-now").addClass("button--disabled");
        return false;
    }


    addonss = {};
    $(".addons-type").each(function () {
        if ($(this).val() != 0 && $(this).val() != "") {
            addonss["" + $(this).attr('rel')] = $(this).val();
        }
    });


    $.ajax({
        url: fcom.makeUrl('cart', 'inCart'),
        type: 'post',
        data: {'date': date,
            'year': year,
            'month': month,
            'member_count': memberCount,
            'event_id': event_id,
            'activity_id': activity_id,
            'addons': addonss
        },
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                $('.cartCount').html(json.cart_count);
                $('.priceOpt').html(json.price);
                resetBooking();
                jsonSuccessMessage(json.msg);
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}


sendConfirmationRequest = function () {
    $("#book-now").addClass("button--disabled");
    date = $(".selectedDate").html();
    year = $('#cal-year').attr('rel');
    month = $('#cal-month').attr('rel');
    memberCount = $("#memberCount").val();
    event_id = $("#eventOption").val();
    activity_id = $('#activityFld').html();
    flag = 1;
    if (typeof date == "" || typeof date === 'undefined') {
        flag = 0;
        jsonNotifyMessage("Hey! You need to select a date first.");
    }


    if (typeof memberCount === 'undefined' || memberCount == "") {
        jsonNotifyMessage("Please Set Number Of Members");
        flag = 0;
    }

    if (typeof event_id === 'undefined' || event_id == "") {
        jsonNotifyMessage("Please Select An Event.");
        flag = 0;
    }

    if (flag == 0) {
        $("#book-now").addClass("button--disabled");
        return false;
    }


    addonss = {};
    $(".addons-type").each(function () {
        if ($(this).val() != 0 && $(this).val() != "") {
            addonss["" + $(this).attr('rel')] = $(this).val();
        }
    });


    $.ajax({
        url: fcom.makeUrl('cart', 'requestForApproval'),
        type: 'post',
        data: {
			'date': date,
            'year': year,
            'month': month,
            'member_count': memberCount,
            'event_id': event_id,
            'activity_id': activity_id,
            'addons': addonss
        },
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                resetBooking();
                jsonSuccessMessage(json.msg);
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}

resetBooking = function () {
    date = $(".selectedDate").html();
   $('.calendar__dates__body').find('.calc-dt').removeClass('selectedDate');
    memberCount = $("#memberCount").val(1);
    $('#act-event').remove();
    jQuery.fn.resetAllBlock();

}
function showMap(lat, lang) {
    L.mapbox.accessToken = mapbox_access_token;
    map = L.mapbox.map('map', 'mapbox.streets')
            .setView([lat, lang], 12);
    if (map.scrollWheelZoom) {
        map.scrollWheelZoom.disable();
    }

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
        draggable: false
    });
   // marker.bindPopup('This marker is draggable! Move it around.');
    marker.addTo(map);

}
$(document).ready(function () {
    $('.book-card__block').each(function () {
        var $blockElem = $(this);
        $blockElem.find('.book-card__block__header').click(function () {
            var $currentElem = $(this);
            var blockIndex = $blockElem.index();
            $('.book-card__block:eq(' + blockIndex + ')').removeClass('done');
            switch (blockIndex) {
                case 1:
                case 2:
                    date = $(".selectedDate").html();
                    activity_id = $('#activityFld').html();

                    if (typeof date == "" ||
                            typeof date === 'undefined' ||
                            typeof activity_id == "" ||
                            typeof activity_id == "undefined"
                            ) {

                        jsonNotifyMessage("Hey! You need to select a date first.");
                        return false;
                    }
                    $('.book-card__block:eq(0)').addClass('done');

                    if (blockIndex == 2) {
                        var memberCount = $("#memberCount").val();
                        if (typeof memberCount === 'undefined' || memberCount == "") {
                            jsonNotifyMessage("Please Set Number Of Members");
                            return false;
                        }
                        $('.book-card__block:eq(1)').addClass('done');
                    }
                    break;

            }


            jQuery.fn.closeAllBlock();
            jQuery.fn.activeCurrentBlock($blockElem);
        });


    });



});

jQuery.fn.closeAllBlock = function () {
    $('.book-card').not('.text--center').find('.book-card__block').removeClass('active');
}
jQuery.fn.resetAllBlock = function () {
    $('#ActivityBookingBlock').find('.book-card__block').removeClass("active");
     $('#ActivityBookingBlock').find('.book-card__block').removeClass("done");
    $('#ActivityBookingBlock').find('.book-card__block:eq(0)').addClass('active');
}
jQuery.fn.activeCurrentBlock = function ($current) {
    $current.addClass('active');
}
jQuery.fn.openBlock = function (blockName) {
    var index = '';
    switch (blockName) {
        case 'calender':
            index = 0;
            break;
        case 'persons':
            index = 1;
            break;
        case 'addon':
            index = 2;
            break;

    }
    $('.book-card__block:eq(' + index + ')').find('.book-card__block__header').trigger('click');
}



/*
 *  Reviews 
 *  
 */

showReviewDetail = function (review_id) {
    jsonNotifyMessage('Loading...');
    fcom.ajax(fcom.makeUrl('reviews', 'activity-review-detail'), {review_id: review_id}, function (json) {

        json = $.parseJSON(json);
        if (json.status == 1) {
            jsonRemoveMessage();
            $('.modaal-content-container').html(json.msg);

        } else {
            jsonErrorMessage(json.msg);
            $('.more-review').modaal('close');
        }
    });
}
reviewcurrentPage = 1;
review = function (page) {

    if (typeof page == undefined) {
        page = 1;
    }
    reviewcurrentPage = page;
    activity_id = $('#activityFld').html();
    jsonNotifyMessage('Review Loading...');
    $('#more-review-result').hide();
    $.ajax({
        url: fcom.makeUrl('reviews', 'activityReview'),
        type: 'post',
        data: {'activity_id': activity_id, page: page},
        success: function (json) {
            jsonRemoveMessage();
            json = $.parseJSON(json);
            if (json.status == 1) {
                if (page == 1) {
                    $('#review__block').html(json.msg);
                }
                else {
                    $('#review__block').find('.activity-review').append(json.msg);
                }
                if (json.more_record) {
                    $('#more-review-result').show();
                }

                $(".write-review").modaal();
                $(".more-review").modaal();

            }

        }
    });
}
loadMoreReviews = function () {
    review(reviewcurrentPage + 1);
}






/*
 *  Activity Inapropriate
 */

markAsInappropriate = function (activity_id) {
    jsonNotifyMessage('Loading...');

    fcom.ajax(fcom.makeUrl('activityAbuse', 'markAsAbuseForm'), {activity_id: activity_id}, function (json) {
        json = $.parseJSON(json);

        if (json.status == 1) {
            jsonRemoveMessage();
            $('.modaal-content-container').html(json.msg);
            //console.log(json.msg);
        }
        else {
            jsonErrorMessage(json.msg);
            $('.activity-abuse').modaal('close');
        }
    });
}

submitAbuseReport = function (v) {
    $('#abuseReviewForm').ajaxSubmit({
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
                $('.activity-abuse').modaal('close');

            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}

scrollToSection = function(obj){
	var $container = $('html, body'),
	$headerContatiner = $('#HEADER'),
	$stickyContainer = $('.js-sticky');
	$stickyOffset = $stickyContainer.data('sticky-offset');
	
	$scrollTo = $(obj).data('moveto');
	/* $('html, body').scrollTop($($scrollTo).offset().top - ($stickyOffset + $('#HEADER').height())); */
    
	$('html, body').animate({
        scrollTop: $($scrollTo).offset().top - ($stickyOffset + $headerContatiner.height())
    }, 2000);
}