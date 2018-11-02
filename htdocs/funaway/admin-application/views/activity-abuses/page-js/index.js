(function ($) {
    currentPage = 1;
    listing = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
            currentPage = page;
 
        var data = fcom.frmData(document.frmSearch);

        moveToTop();
        fcom.ajax(fcom.makeUrl('ActivityAbuses', 'listing', [page]), data, function (json) {
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
        fcom.ajax(fcom.makeUrl('ActivityAbuses', 'listing'), data, function (json) {
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

        fcom.ajax(fcom.makeUrl('ActivityAbuses', 'listing'), fcom.frmData(form), function (json) {
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

    getAbuseForm = function (abreport_id) {
        if (typeof abreport_id == undefined || abreport_id == null) {
            abreport_id = 0;
        }
        fcom.ajax(fcom.makeUrl('activityAbuses', 'abuseform'), {abreport_id: abreport_id}, function (json) {
            json = $.parseJSON(json);

            if ("1" == json.status) {
                $("#form-tab").html(json.msg);
                moveToTop();
            } else {
                moveToTop();
                jsonErrorMessage(json.msg);
            }
        });
    }

    submitForm = function (v) {
        var action_form = $('#action_form');

        $('#action_form').ajaxSubmit({
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
                    closeForm();
                    jsonSuccessMessage(json.msg);
                    listing(currentPage);

                }
                else {
                    jsonErrorMessage(json.msg)
                }
            }
        });


        return false;

    }
})(jQuery);


$(document).ready(function () {
    $(document).ajaxStart(function () {
        jsonNotifyMessage("loading....")
    });
    listing();
});