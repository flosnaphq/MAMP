(function ($) {
    listing = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var data = fcom.frmData(document.frmSearch);

        moveToTop();
        fcom.ajax(fcom.makeUrl('traveler', 'listing', [page]), data, function (json) {
            json = $.parseJSON(json);
            if ("1" == json.status) {
                $("#listing").html(json.msg);
                jsonSuccessMessage("List Updated.")

            } else {
                jsonErrorMessage("something went wrong.")
            }
        });
    }
    sortTable = function (obj) {


        var data = $(obj).data('href');
        moveToTop();
        fcom.ajax(fcom.makeUrl('traveler', 'listing'), data, function (json) {
            json = $.parseJSON(json);
            if ("1" == json.status) {
                $("#listing").html(json.msg);
                jsonSuccessMessage("List Updated.");

            } else {
                jsonErrorMessage(json.msg);
            }
        });
    }
    search = function (form) {

        fcom.ajax(fcom.makeUrl('traveler', 'listing'), fcom.frmData(form), function (json) {
            json = $.parseJSON(json);
            if ("1" == json.status) {
                $("#listing").html(json.msg);
                $('#clearSearch').show();
                jsonSuccessMessage("List Updated.")

            } else {
                jsonErrorMessage("something went wrong.")
            }
        });

    }

    clearSearch = function () {
        $('.search-input').val('');
        $('#pretend_search_form input').val('');
        listing();
        $('#clearSearch').hide();
    }
})(jQuery);


$(document).ready(function () {
    $(document).ajaxStart(function () {
        jsonNotifyMessage("loading....")
    });
    listing();
});