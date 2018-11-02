$(document).ready(function () {

    readlessmore('', 110, 200);
    $("img.lazy").lazyload({
        event: "sporty"
    });
    
    $('.form-element__control span').removeClass('twitter-typeahead');
    $('.form-element__control span').removeAttr('style');
});


$(window).bind("load", function () {
    var timeout = setTimeout(function () {
        $("img.lazy").trigger("sporty")
        getFeaturedList();
    }, 300);
});
$(function () {
    var $window = $(window),
            $body = $('body');
    //Scroll top
    var _isTop = function () {
        if ($window.scrollTop() > 0)
            $body.removeClass('is--top').addClass('is--bottom');
        else
            $body.removeClass('is--bottom').addClass('is--top');
    };
    _isTop();
    $window.on('scroll', function () {
        _isTop();
    });

});


searchFilter = function (frm) {
    var url = fcom.makeUrl('activity');
    var query_string = '';
    $(frm).find('select').each(function () {
        var field_name = $(this).attr('name');
        var field_value = $(this).val();
        if (field_value != '') {
            if (query_string == '') {
                query_string += field_name + '=' + field_value;
            } else {
                query_string += '&' + field_name + '=' + field_value;
            }
        }

    });
    if (query_string == '') {
        return false;
    }

    url += '?' + query_string;
    window.location = url;

}

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

function getFeaturedList() {
    jsonNotifyMessage('Loading...');
    fcom.ajax(fcom.makeUrl('home', 'ajaxLoad'), {}, function (json) {
        json = $.parseJSON(json);
        jsonRemoveMessage();
        if (json.status == 1) {
            if (typeof json.featureList != 'undefined' && json.noResult != '1') {
                $('#feature-list').html(json.featureList);
                $('#feature-list-wrapper').show();
                $('#activities').show();
                nicescrollbar();
            }
        }
    });
}

showFeatureActivities = function (el, tab_id)
{
	var featuredCitiesCount = $(el).closest("li").parent('ul').data('featured-cities-count');
	
	if(featuredCitiesCount < 2 || $(el).hasClass('is--active')) {
		return;
	}

    $('.js-feature-tab').slideUp();
    $('#js-feature-tab-' + tab_id).slideDown();
	
    $('.js-featured-island li a').removeClass('is--active');
    /* // $('.js-featured-island li a').removeClass('button--red');
    // $('.js-featured-island li a').addClass('button--dark');
	
    // $(el).removeClass('button--dark');
    // $(el).addClass('button--red'); */
    $(el).addClass('is--active');
    return;
}

