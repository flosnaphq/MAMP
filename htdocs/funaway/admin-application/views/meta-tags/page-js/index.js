$(document).ready(function () {
    $(document).ajaxStart(function () {
        jsonNotifyMessage("loading....")
    });
    listing();
});
(function () {

    var currentPage = 1;

    listing = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        currentPage = page;
        var data = fcom.frmData(document.frmReviewSearch);
        moveToTop();
        fcom.ajax(fcom.makeUrl('MetaTags', 'listing', [page]), data, function (json) {
            json = $.parseJSON(json);
            if ("1" == json.status) {
                $("#listing").html(json.msg);
                jsonSuccessMessage("List Updated.");

            } else {
                jsonErrorMessage(json.msg);
            }
        });


    }

    sortTable = function (obj) {


        var data = $(obj).data('href');
        moveToTop();
        fcom.ajax(fcom.makeUrl('MetaTags', 'listing'), data, function (json) {
            json = $.parseJSON(json);
            if ("1" == json.status) {
                $("#listing").html(json.msg);
                jsonSuccessMessage("List Updated.");

            } else {
                jsonErrorMessage(json.msg);
            }
        });
    }


    getMetaForm = function (meta_id) {
        if (typeof meta_id == undefined || meta_id == null) {
            meta_id = 0;
        }
        fcom.ajax(fcom.makeUrl('MetaTags', 'metaForm'), {meta_id: meta_id}, function (json) {
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





    search = function (form) {

        fcom.ajax(fcom.makeUrl('MetaTags', 'listing'), fcom.frmData(form), function (json) {
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

    submitForm = function (v) {
        var action_form = $('#meta_tag');

        $('#meta_tag').ajaxSubmit({
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

    closeForm = function () {

        $('#form-tab').html('');
    }



})();