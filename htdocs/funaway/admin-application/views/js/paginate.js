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
        var data = fcom.frmData(document.frmSearch);
        moveToTop();
        fcom.ajax(fcom.makeUrl(paginateController, 'listing', [page]), data, function (json) {
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
        fcom.ajax(fcom.makeUrl(paginateController, 'listing'), data, function (json) {
            json = $.parseJSON(json);
            if ("1" == json.status) {
                $("#listing").html(json.msg);
                jsonSuccessMessage("List Updated.");

            } else {
                jsonErrorMessage(json.msg);
            }
        });
    }

    clearSearch = function () {
        $('.search-input').val('');
        $('#pretend_search_form input').val('');
        listing();
        $('#clearSearch').hide();
    }

})();