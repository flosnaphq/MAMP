var availableCities = [];
var availableRegions = [];
var suggestionClass = '';
var mobileMenu = false;
var systemMessageTime = 5000;
var systemMessageTimer;
mbsmessageTime = 5000;
$(document).ready(function () {
    /* $( document ).ajaxComplete(function() {
     jsonRemoveMessage()
     }); */
    $(".city-tab").on("change", function () {
        city_id = $(this).val();
        if (typeof city_id === "undefined" || city_id == "")
            return;
        $.ajax({
            type: "POST",
            url: fcom.makeUrl("info", "regions", [city_id], "/"),
            success: function (json) {
                json = $.parseJSON(json);
                $(".region-tab").html(json.options);
            }
        });
    });

    $('.current-lang').on('click', function () {
        $('.lang-option').slideToggle(100);
    });
    /* for filters */
    $('.filterslink').click(function () {
        $(this).toggleClass("active");
        $('#stickyleft').slideToggle();
    });

    $('.boxRound .openToggle').on('click', function () {
        if ($(this).parent().parent().children('.toggleWrap').css('display') == 'none') {
            $(this).removeClass('active');
        }
        else {
            $(this).addClass('active');
        }
        $(this).parent().parent().children('.toggleWrap').slideToggle(400);


    });
    ;

    $('#dashboard-menu-icon').click(function () {
        $(this).toggleClass("active");
        $('.fixed-navBar').slideToggle(400);
    });

    /* for menu list */
    $('.togl_icn').click(function () {
        $(this).toggleClass("active");
        $('.left-menufltr').slideToggle();
        mobileMenu = true;
    });

    $("input[type=submit]").removeAttr("title");
    /* for footer */
    $('.trigger').click(function () {
        if ($(window).width() > 990)
            return;
        var activeSiblingDisplay = $(this).siblings('.gridcontent').css('display');
        $('.trigger').removeClass('active');
        $('.gridcontent').slideUp();

        if (activeSiblingDisplay == 'none') {
            $(this).toggleClass("active");
            if ($(window).width() < 990)
                $(this).siblings('.gridcontent').slideToggle();
        }
    });
    $('#user-name').click(function () {
        $('#drop_menu').toggleClass('open_menu');
    });
    setTimerSystemMessage();
});
$(document).ajaxComplete(function () {
    $("input[type=submit]").removeAttr("title");

});

function setTimerSystemMessage() {
    systemMessageTimer = setTimeout('clearSystemMessage()', systemMessageTime);
}

function clearSystemMessage() {
    $('.system_message').fadeOut(systemMessageTimer);
    clearTimeout(systemMessageTimer);
}



function sticky_relocate(topDivObj, sticky_left, cls) {

    var window_top = $(window).scrollTop();
    var div_top = $(topDivObj).offset().top;
    //var sticky_left = $('#stickyleft');
    if ((window_top + sticky_left.height()) >= ($('#footer').offset().top - 25)) {
        var to_reduce = ((window_top + sticky_left.height()) - ($('#footer').offset().top - 25));
        var set_stick_top = 20 - to_reduce;
        sticky_left.css('top', set_stick_top + 'px');
    } else {
        sticky_left.css('top', '20px');
        if (window_top > div_top) {
            sticky_left.addClass(cls);
        } else {
            sticky_left.removeClass(cls);
        }
    }
}
function getRegions(city_id, region_id) {
    if (typeof city_id === "undefined" || city_id == "")
        return;
    $.ajax({
        type: "POST",
        url: fcom.makeUrl("info", "regions", [city_id], "/"),
        success: function (json) {
            json = $.parseJSON(json);
            $("#" + region_id).html(json.options);
        }
    });
}

newsLetter = function (v, form) {
    v.validate();
    if (!v.isValid()) {
        return false;
    }
    $.ajax({
        type: "POST",
        data: {'email': form.email.value, 'fIsAjax': 1},
        url: fcom.makeUrl('home', "add-news"),
        success: function (json) {
            json = $.parseJSON(json);
            $.mbsmessage(json.msg);
            moveToTop();

        }
    });
};

function jsonErrorMessage(msg) {
    $.mbsmessage(msg, true);
    $('#mbsmessage').removeClass("alert alert_info");
    $('#mbsmessage').removeClass("alert alert_success");
    $('#mbsmessage').addClass("alert alert_danger");
    jsonRemoveMessage(mbsmessageTime);
}

function jsonSuccessMessage(msg) {
    $.mbsmessage(msg, true);
    $('#mbsmessage').removeClass("alert alert_info");
    $('#mbsmessage').removeClass("alert alert_danger");
    $('#mbsmessage').addClass("alert alert_success");
    jsonRemoveMessage(mbsmessageTime);
}

function jsonNotifyMessage(msg) {
    $.mbsmessage(msg);
    $('#mbsmessage').removeClass("alert alert_danger");
    $('#mbsmessage').removeClass("alert alert_success");
    $('#mbsmessage').addClass("alert alert_info");
    jsonRemoveMessage(mbsmessageTime);
}
function jsonRemoveMessage(time)
{
    var time = time || 0;
    /* $.mbsmessage(msg);	
     $('#mbsmessage').removeClass("alert alert_danger");
     $('#mbsmessage').removeClass("alert alert_success");
     $('#mbsmessage').removeClass("alert alert_info has--click"); */
    setTimeout('$(document).trigger("close.mbsmessage")', time);
}

function closePopup(src) {
    submitImageForm();
    $.facebox.close();
}
function moveToTop(moveTO) {
    var pos = 0;
    if (typeof moveTO == undefined || moveTO == null) {
        pos = 0;
    }
    else {
        var p = $("#" + moveTO);
        pos = p.position();
        pos = pos.top;
    }

    $("html, body").animate({scrollTop: pos}, "slow");
}



function confirmbox(msg, handler) {
    $.facebox('<div class="confirm-box"><div class="message">' + msg + '</div><div class="buttons"><button class="isYes" id="button1" value="1">Yes</button><button class="isNo" id="button2" name="button2" value="1">No</button></div></div>');

    $("#button1").on("click", function (evt) {
        handler(true);
        $.facebox.close();

    });
    $("#button2").on("click", function (evt) {
        handler(false);
        $.facebox.close();

    });
}




function CloseAddOnPopup() {
    $("#add_on_popup").remove();
}

function CloseCustomPopup() {
    $("#custom-popup-content").html('');
    $("#custom-popup").removeClass('active');
    $('body').removeClass('overflow-hide');
}
function showCustomPopup(popup_content, addOnClass) {
    if (popup_content == '' || popup_content == null || popup_content == undefined) {
        return false;
    }
    if (addOnClass == '' || addOnClass == null || addOnClass == undefined) {
        addOnClass = '';
    }
    $('#custom-popup-content').html(popup_content);
    $("#custom-popup").addClass(addOnClass);
    $("#custom-popup").addClass('active');
    $('body').addClass('overflow-hide');
}

function showTopping(id) {
    $('#' + id).slideToggle(400);
}
showMoreRestDescription = function () {
    $('#read-more-desc').slideDown(400);
    $('#read-more-button').hide();
    $('#hide-more-button').show();
};
hideMoreRestDescription = function () {
    $('#read-more-desc').slideUp(400);
    $('#read-more-button').show();
    $('#hide-more-button').hide();
};

function searchLocation(v) {
    $('#searchLocation').ajaxSubmit({
        delegation: true,
        beforeSubmit: function () {
            v.validate();
            if (!v.isValid()) {
                return false;
            }
        },
        success: function (json) {
            json = $.parseJSON(json);

            if (json.status == "1") {
                window.location = json.url;
            }
            else {
                moveToTop();
                jsonErrorMessage(json.msg);
            }
        }
    });
}

function searchPopLocation(v, frm) {

    $(frm).ajaxSubmit({
        delegation: true,
        beforeSubmit: function () {
            v.validate();
            if (!v.isValid()) {
                return false;
            }
        },
        success: function (json) {
            json = $.parseJSON(json);
            CloseCustomPopup();
            console.log(json.url);
            if (json.status == "1") {
                window.location = json.url;
            }
            else {
                jsonErrorMessage(json.msg);
                moveToTop();
            }
        }
    });
}

function showDeals() {
    $('#deals-wrap').slideToggle(400);
}
function hideDeals() {
    //$('#deals-wrap').hide(400);
    $('#deals-wrap').slideToggle(400);
}
var isPopupView = false;
function popupView(url) {
    isPopupView = true;
    $.facebox({ajax: url});

}

function openRestList() {
    $('#merchant_restaurant').slideToggle();
}

selectCurretRestaurant = function (rest_id) {
    fcom.ajax(fcom.makeUrl('merchant', 'currentRestaurant'), {restaurant_id: rest_id}, function (json) {
        json = $.parseJSON(json);

        if ("1" == json.status) {
            window.location = fcom.makeUrl('restaurant', 'restaurant-form', [rest_id]);
            ;
        } else {

        }
    });
};

function confirmCommentBox(msg, handler) {
    $.facebox('<div class="confirm-box"><div class="message">' + msg + '</div><div class="buttons"><button class="isYes" id="button1" value="1">Yes</button><button class="isNo" id="button2" name="button2" value="1">No</button></div></div>');

    $("#button1").on("click", function (evt) {
        handler(true);
        $.facebox.close();

    });
    $("#button2").on("click", function (evt) {
        handler(false);
        $.facebox.close();

    });


}
showPromoCode = function () {
    $(".apply-code-input").slideToggle();
};



function showCart(cart_id) {
    var el = $('#cart_' + cart_id);

    if ($(el).parent().children('.cart-box').css('display') == 'block') {
        $(el).parent().children('.cart-box').slideUp(400);
        $('.accordianTab').removeClass('active');
    }
    else {
        $('.accordianTab').removeClass('active');
        $(el).parent().addClass('active');
        $(el).parent().children('.cart-box').slideDown(400);
    }
}
changeLocation = function () {
    var url = window.location.href;
    CloseCustomPopup();

    fcom.ajax(fcom.makeUrl('cart', 'change-location'), {url: url}, function (json) {

        json = $.parseJSON(json);
        if ("1" == json.status) {
            showCustomPopup(json.msg, 'location-search');
        }
        else {
            jsonErrorMessage(json.msg);
        }
    });
};


var citySearchInput = '.citySearch';
var regionSearchInput = '.regionSearch';
$(function () {


    $(document).delegate(".location-search input.citySearch", "focus", function () {
        citySearchInput = this;
    });
    $(document).delegate(".location-search input.regionSearch", "focus", function () {
        regionSearchInput = this;
    });
    $(document).delegate(".location-search input.citySearch", "keydown.autocomplete", function () {
        $(this).autocomplete({
            source: availableCities,
        }).data("ui-autocomplete")._renderItem = function (ul, item) {

            var srch = $(citySearchInput).val();

            var filterValue = item.value.replace(new RegExp(srch, 'gi'), '<b>' + srch + '</b>');
            return $("<li>")
                    .data("ui-autocomplete-item", item)
                    .append(filterValue)
                    .appendTo(ul);
        };

    });

    $(document).delegate('.citySearch', 'blur', function () {
        availableRegions = [];
        $(regionSearchInput).val('');
        loadSuggetionRegion($(citySearchInput).val());
    });


    $(document).delegate(".location-search input.regionSearch", "keydown.autocomplete", function () {
        $(this).autocomplete({
            source: availableRegions,
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            var srch = $(regionSearchInput).val();
            var filterValue = item.value.replace(new RegExp(srch, 'gi'), '<b>' + srch + '</b>');

            return $("<li>")
                    .data("ui-autocomplete-item", item)
                    .append(filterValue)
                    .appendTo(ul);
        };
    });


    String.prototype.replaceAll = function (search, replacement) {
        var target = this;
        return target.replace(new RegExp(search, 'gi'), replacement);
    };


});

loadSuggetionRegion = function (city) {
    fcom.ajax(fcom.makeUrl('home', 'getRegions'), {city: city}, function (json) {

        json = $.parseJSON(json);
        if ("1" == json.status) {
            availableRegions = $.parseJSON(json.msg);

        }

    });
};

reorder = function (order_id) {
    var url = window.location.href;
    fcom.ajax(fcom.makeUrl('cart', 'reorder'), {order_id: order_id, url: url}, function (json) {

        json = $.parseJSON(json);

        if ("1" == json.status) {
            if ("3" == json.screen_type) {
                CloseCustomPopup();
                showCustomPopup(json.msg, 'location-search');
            }
            else {
                window.location = json.msg
            }

        }
        else {
            jsonErrorMessage(json.msg);
        }

    });
};


/* Cropper JS */


function sumbmitProfileImage() {
    $('#avatar-action').val('avtar');
    $('#user_image').remove();
    $("#profile-img").submit();
}

var $image;
function cropImage(obj) {
    $image = obj;
    obj.cropper({
        aspectRatio: 1,
        autoCropArea: 0.4545,
        // strict: true,
        guides: false,
        highlight: false,
        dragCrop: false,
        cropBoxMovable: false,
        cropBoxResizable: false,
        responsive: true,
        crop: function (e) {
            var json = [
                '{"x":' + e.x,
                '"y":' + e.y,
                '"height":' + e.height,
                '"width":' + e.width,
                '"rotate":' + e.rotate + '}'
            ].join();
            $("#img-data").val(json);
        },
        built: function () {
            $(this).cropper("zoom", 0.5);
        },
    })
}



function popupImage(formId) {
    wid = $(window).width();
    if (wid > 767) {
        wid = 500;
    } else {
        wid = 280;
    }


    $('#' + formId).ajaxSubmit({
        delegation: true,
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == 1) {
                var fn = "sumbmitProfileImage();";
                $.facebox('<div class="img-container"><img alt="Picture" src="" class="img_responsive" id="new-img" /></div><span class="gap"></span><div class="aligncenter"><span title="Rotate Left" data-option="-90" data-method="rotate" type="button"><span class="docs-tooltip" title="" data-toggle="tooltip" data-original-title="$().cropper(\'rotate\', -90)"><img src="/images/left-rotate.png"></span></span><span><img src="/images/save.png" onclick="' + fn + '"></span><span title="Rotate Right" data-option="90" data-method="rotate" type="button"><span class="docs-tooltip" title="" data-toggle="tooltip" data-original-title="$().cropper(\'rotate\', 90)"><img src="/images/right-rotate.png"></span></span></div>');
                $("#facebox").addClass("profile-popup");
                $('#new-img').attr('src', json.link);
                $('#new-img').width(wid);
                $('#crop-response').val(json.response);
                cropImage($('#new-img'));
            } else {
                $.facebox(json.msg);
            }

        }
    });
}


$(function () {

    $(document.body).on('click', '[data-method]', function () {

        var data = $(this).data(),
                $target,
                result;



        if (data.method) {
            data = $.extend({}, data); // Clone a new one

            if (typeof data.target !== 'undefined') {
                $target = $(data.target);

                if (typeof data.option === 'undefined') {
                    try {
                        data.option = JSON.parse($target.val());
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }



            result = $image.cropper(data.method, data.option);
            if (data.method === 'getCroppedCanvas') {
                $('#getCroppedCanvasModal').modal().find('.modal-body').html(result);
            }

            if ($.isPlainObject(result) && $target) {
                try {
                    $target.val(JSON.stringify(result));
                } catch (e) {
                    console.log(e.message);
                }
            }

        }
    });
});

///////////////////////////////////////////////////// End Of Cropper JS \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\


removeUserImage = function (ids) {
    confirmbox('Do You Want To Remove Image ?', function (outcome) {
        if (outcome) {
			
            jsonNotifyMessage('processing...');
            $.ajax({
                type: "post",
                data: {"user_id": ids, fIsAjax: 1},
                url: fcom.makeUrl("users", "remove-image"),
                success: function (json) {
                    json = $.parseJSON(json);
                    if (1 == json.status) {
                        $("#u-pic").attr("src", $("#u-pic").attr("src") + "1");
                        jsonSuccessMessage(json.msg);
                    }
                    else {
                        jsonErrorMessage(json.msg);
                    }

                }
            });
        }

    });

};

closeForm = function () {
    $('#form-tab').html('');
};
function addMoreImages() {
    $("#clone_image_div").find('.fieldadd').clone().appendTo("#elem_main_image_div");
}
function setFileInputName(obj) {
    var file_name = $(obj).val()
    $(obj).parent().find('.file_input_name').text(file_name);
}
function removeImageInput(obj) {
    $(obj).parents('.fieldadd').remove();
}

/*
 * 
 * Code Added By Abhineet
 * 
 */


/*
 *  Theme Switcher
 */
jQuery.fn.submitForm = function (v, form_id, onSuccess, onFailure) {
    var action_form = $('#' + form_id);
    v.validate();
    if (!v.isValid()) {
        return false;
    }
    fcom.ajax($(action_form).attr('action'), fcom.frmData(action_form), function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            jsonSuccessMessage(json.msg);
            if (onSuccess) {
                onSuccess(json);
            }
        } else {
            jsonErrorMessage(json.msg);
            if (onFailure) {
                onFailure(json);
            }
        }
    });
    return false;
};

var themeLayoutSwitcher = function (val, type) {
    if (type == 'layout') {
        var data = 'layout=' + val;
    }

    fcom.ajax(fcom.makeUrl('home', 'themesetup'), data, function (t) {
        var json = JSON.parse(t);
        if (1 == json.status) {
            jsonSuccessMessage(json.msg);
        } else {
            jsonErrorMessage(json.msg);
        }
    });


};

$(document).ready(function () {

    $('.switchtoggle .switch-label, .switchtoggle .switch-handle').click(function () {
        if ($("body").hasClass('switch_layout')) {
            themeLayoutSwitcher(0, 'layout');
        } else {
            themeLayoutSwitcher(1, 'layout');
        }
    });
});

function getCountries(regionId) {
	fcom.ajax(fcom.makeUrl('misc', 'getCountries', [regionId], "/"), {}, function (response) {
        var result = $.parseJSON(response);

        if (result.status == 0) {
            return false;
        }
        fillSelectBox("countries", result.msg);
    });
}

function getCities(countryId) {
	fcom.ajax(fcom.makeUrl('misc', 'getCities', [countryId], "/"), {}, function (response) {
        var result = $.parseJSON(response);

        if (result.status == 0) {
            return false;
        }

        fillSelectBox("cities", result.msg);
    });
}

function fillSelectBox(elemId, options) {

    if (!$('#' + elemId)) {
        console.log("Object not found");
        return false;
    }

    if (!options instanceof Object) {
        return false;
    }
    $('#' + elemId).find('option[value!=""]').remove();

    $.each(options, function (index, value) {
        $('#' + elemId).append("<option value='" + index + "'>" + value + "</option>");
    });
    return  true;
}
var loadDependValues = function (obj, elemId) {

    if (!obj) {
        return false;
    }
    var hostId = $(obj).val();
    var href = $(obj).data('href');
    if(!href){
        return false;
    }
    var requesturl = href+"/"+hostId;
    fcom.ajax(requesturl, {}, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
           fillSelectBox(elemId,json.msg);
        } else {
            jsonErrorMessage(json.msg);
        }
    });
    return false;
};

var loadCountryCodes = function (obj) {

    if (!obj) {
        return false;
    }
    var countryId = $(obj).val();
    fcom.ajax(fcom.makeUrl('users', 'getCountryCodes', [countryId]), {}, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            $("#country_code").text(json.msg);
        } else {
            jsonErrorMessage(json.msg);
        }
    });
    return false;
};


var editSlug = function (obj) {
    if (!$(obj)) {
        return false;
    }
    var recordId = $(obj).data('record-id');
    var recordType = $(obj).data('record-type');

    if (!recordId || !recordType) {
        return false;
    }
    fcom.ajax(fcom.makeUrl('routes', 'routeUpdate', [recordType, recordId]), {}, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            $.facebox(json.msg);
            moveToTop();
        } else {
            jsonErrorMessage(json.msg);
        }
    });
    return false;
};