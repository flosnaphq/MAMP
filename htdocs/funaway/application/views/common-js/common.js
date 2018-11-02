var processing_message = 'Processing...';
var systemMessageTime = 15000;
var mainSearchCurrentPage = 1;
$(document).ready(function () {
    setTimerSystemMessage();
    $(".js-main-search").modaal({fullscreen: true, custom_class: 'h__serach-wrapper', hide_close: true, after_open: function () {
            $('#main-search-input').focus()
            bindAutoComplete();
        }, after_close: function () {
            $('#search-card-result-wrapper').hide();
            $('#main-search-input').val('');
        }});

    $('#main-search-input').keyup(function (e) {
        mainSearch($(this).val());
    });


});

function setPhoneCode(country_id, code_id) {

    if (typeof country_id == undefined || country_id == null) {
        return false;
    }

    if (typeof country_phone_code == undefined) {
        return false;
    }
    /* if($('#'+code_id).val() != ''){
     return false;
     } */

    if (country_phone_code[country_id] == '') {
        return false;
    }
    codes = country_phone_code[country_id];
    $.each(codes, function (index, value) {
        if (index != '') {
            $('#' + code_id).val(index);
            return false;
        }
    });

}
function getCountries(regionId) {
    fcom.ajax(fcom.makeUrl('misc', 'getCountries', [regionId]), {}, function (response) {
        var result = $.parseJSON(response);

        if (result.status == 0) {
            return false;
        }

        fillSelectBox("countries", result.msg);
    });
}

function getCities(countryId) {

    fcom.ajax(fcom.makeUrl('misc', 'getCities', [countryId]), {}, function (response) {
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
closeMainSearch = function () {
    $(".js-main-search").modaal('close');
}
mainSearch = function (v, page) {
    if (typeof page == undefined || page == null) {
        page = 1;
    }
    mainSearchCurrentPage = page;
    $('#more-result').hide();
    if (v == '')
        return false;
    fcom.ajax(fcom.makeUrl('home', 'mainSearch'), {'keyword': v, 'page': page}, function (json) {
        json = $.parseJSON(json);
        if (json.status == 1) {
            $('#search-card-result-wrapper').show();
            if (page == 1) {
                $('#search-card__result').html(json.msg);
            } else {
                $('#search-card__result').append(json.msg);
            }
            if (json.more_record > 0) {
                $('#more-result').show();
            }
        }

    });
}
var loadCountryCodes = function (obj) {
    if (!obj) {
        return false;
    }
    var countryId = $(obj).val();
    /* fcom.ajax(fcom.makeUrl('user', 'getCountryCodes', [countryId]), {}, function (json) { */
    fcom.ajax(fcom.makeUrl('Country', 'getCountryPhoneCodes', [countryId]), {}, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            $("#country_code").text("+" + json.msg);
        } else {
            jsonErrorMessage(json.msg);
        }
    });
    return false;
}
loadMoreMainSearch = function () {
    mainSearch($('#main-search-input').val(), mainSearchCurrentPage + 1);
}
function setTimerSystemMessage() {
    systemMessageTimer = setTimeout('clearSystemMessage()', systemMessageTime);
}

function clearSystemMessage() {
    $('.js-system-message').fadeOut(systemMessageTime);
    clearTimeout(systemMessageTimer);
}
function jsonErrorMessage(msg) {
    $.mbsmessage(msg, true);
    $('#mbsmessage').removeClass("alert alert_info has--click");
    $('#mbsmessage').removeClass("alert alert_success");
    $('#mbsmessage').addClass("alert alert_danger");
}

function jsonSuccessMessage(msg) {
    $.mbsmessage(msg, true);
    $('#mbsmessage').removeClass("alert alert_info has--click");
    $('#mbsmessage').removeClass("alert alert_danger");
    $('#mbsmessage').addClass("alert alert_success");
}

function jsonNotifyMessage(msg) {
    if (typeof msg == undefined || msg == null) {
        msg = processing_message;
    }
    $.mbsmessage(msg);
    $('#mbsmessage').removeClass("alert alert_danger");
    $('#mbsmessage').removeClass("alert alert_success");
    $('#mbsmessage').addClass("alert alert_info");
}

function jsonStrictNotifyMessage(msg) {
    if (typeof msg == undefined || msg == null) {
        msg = processing_message;
    }
    $.mbsmessage(msg);
    $('#mbsmessage').removeClass("alert alert_danger");
    $('#mbsmessage').removeClass("alert alert_success");
    $('#mbsmessage').addClass("alert alert_info has--click");
}

function jsonRemoveMessage()
{
    $(document).trigger('close.mbsmessage');
}


function underDevelopment() {
    alert('under development');
}

function refreshCaptcha(img_id) {
    document.getElementById(img_id).src = fcom.makeUrl('image', 'captcha') + '?sid=' + Math.random();
    return false;
}

function includeJs(jsFilePath) {
    var js = document.createElement("script");

    js.type = "text/javascript";
    js.src = jsFilePath;

    document.body.appendChild(js);
}


/////////////////////////////////////////////////////////////////////////
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
                $("body").append('<a href="#inline" class="inline">Show</a><div id="inline" style="display:none;"><div class="img-container"><img alt="Picture" src="" class="img_responsive" id="new-img" /></div><span class="gap"></span><div class="button__group text--center" style="margin-bottom:1.25em; margin-top:1.25em;"><span class="button button--small button--fill button--dark" title="Rotate Left" data-option="-90" data-method="rotate"><span class="docs-tooltip" title="" data-toggle="tooltip" data-original-title="$().cropper(\'rotate\', -90)"><!--img src="/images/left-rotate.png"-->Rotate Left</span></span><span class="button button--fill button--green" onclick="' + fn + '"><!--img src="/images/save.png" -->Done</span><span class="button button--small button--fill button--dark" title="Rotate Right" data-option="90" data-method="rotate"><span class="docs-tooltip" title="" data-toggle="tooltip" data-original-title="$().cropper(\'rotate\', 90)"><!--img src="/images/right-rotate.png"-->Rotate Right</span></span></div></div>');
                //	$("#facebox").addClass("profile-popup");
                $('.inline').modaal({
                    after_close: function () {
                        $(".inline").remove();
                        $("#inline").remove();
                    }
                });
                $('.inline').trigger('click');
                $('#new-img').attr('src', json.msg);
                $('#new-img').width(wid);
                //$('#crop-response').val(json.response);
                cropImage($('#new-img'), function () {

                });
            } else {
                jsonErrorMessage(json.msg);
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


/////////////////////////////////////////////////////////





function confirmbox(msg, handler) {
    if (confirm(msg)) {
        handler(true);
    } else {
        handler(false);
    }
}


function queryStringToJSON(str) {
    var pairs = str.slice(1).split('&');
    var result = {};
    pairs.forEach(function (pair) {
        pair = pair.split('=');
        var name = pair[0]
        var value = pair[1]
        if (name.length)
            if (result[name] !== undefined) {
                if (!result[name].push) {
                    result[name] = [result[name]];
                }
                result[name].push(value.split(',') || '');
            } else {
                result[name] = value.split(',') || '';
            }
    });
    return(result);
}

resendVerification = function () {
    fcom.ajax(fcom.makeUrl('guest-user', 'resendVerificationEmail'), {}, function (json) {
        json = $.parseJSON(json);
        if (json.status == 1) {
            jsonSuccessMessage(json.msg);
        } else {
            jsonErrorMessage(json.msg);
        }

    });
}

$(document).ready(function () {
    /*	$(document).ajaxStart(function(){
     console.clear();
     });
     $(document).ajaxComplete(function(){
     console.clear();
     });  */
    $('.js-currency-class').change(function () {
        window.location = fcom.makeUrl('currency', 'set-currency', [$(this).val()]);
    });

    /*Date Picker Today Button Functionality*/
    $.datepicker._gotoToday = function (id) {
        $(id).datepicker('setDate', new Date()).datepicker('hide').blur();
    };

});


function moveTo(id) {
    console.log('MoveToHere');
    return true;
    var p = $(id);
    pos = p.position();
    pos = pos.top;
    $("html, body").animate({scrollTop: pos}, "slow");
}

function fillWithLoader(elm)
{
    var elm = elm || '';
    if (elm == '' || 'undefined' == elm)
    {
        return;
    }
    var img = '<div class="img-loader"><img src="' + webRootUrl + 'images/ajax-loader.gif" alt="loading..."></div>';
    $(elm).html(img);
    return;
}

$(document).ready(function () {
    bindAutoComplete();
});
function bindAutoComplete() {
    var autoCompletecountries = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: fcom.makeUrl('home', 'cities', ['QUERY']),
            wildcard: 'QUERY'
        }
    });

    var autoCompleteActivity = new Bloodhound({
        datumTokenizer: function (d) {
            return Bloodhound.tokenizers.whitespace(d.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: fcom.makeUrl('home', 'activity', ['QUERY']),
            wildcard: 'QUERY'
        }
    });
    $('#search-autocomplete').unbind('typeahead');
    var typeHead = $('#search-autocomplete').typeahead({
        hint: false,
        highlight: true,
        minLength: 1
    },
            {
                name: 'states',
                display: 'value',
                source: autoCompletecountries,
				templates: {
                    header: '<h3 class="league-name">Countries/Cities</h3>'
                }
            },
            {
                name: 'activities',
                display: 'value',
                source: autoCompleteActivity,
                templates: {
                    header: '<h3 class="league-name">Activities</h3>'
                }
            }

    );

    typeHead.on('typeahead:selected', function (e, datum) {
        window.location = datum.redirect;
    });
}
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
            jsonErrorMessage(json);
            if (onFailure) {
                onFailure(json);
            }
        }
    });
    return false;
}

wishlist = function (obj, activityId) {
    jsonNotifyMessage();
    //  facebookWishListTrack();
    $.ajax({
        url: fcom.makeUrl("home", "add-to-wish"),
        data: {"activity_id": activityId, fIsAjax: 1
        },
        type: "post",
        success: function (json) {
            json = $.parseJSON(json);
            if (1 == json.status) {
                jsonSuccessMessage(json.msg);
                if (json.type == "add") {
                    $(obj).addClass('has--active');
                } else {
                    $(obj).removeClass('has--active');
                }
            } else {
                jsonErrorMessage(json.msg);
            }
        }

    });
}

$(document).on('click', '.avatar-alert .close', function () {
    $('.avatar-alert').remove();
});

$(document).ready(function(){	
	 window.fbAsyncInit = function() {
	  FB.init({
		appId      : fb_app_id,
		cookie     : true,  // enable cookies to allow the server to access 
							// the session
		xfbml      : true,  // parse social plugins on this page
		version    : 'v2.6' // use version 2.0
	  });
	};  (function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	  }(document, 'script', 'facebook-jssdk'));
});


function graphStreamPublish(lnk,pic,caption,desc){
	FB.ui({
        method: 'share_open_graph',
        action_type: 'og.shares',
        action_properties: JSON.stringify({
            object : {
               'og:url': lnk,
               'og:title': caption,
               'og:description': desc,
               'og:image': pic
            }
        })
    });
}




