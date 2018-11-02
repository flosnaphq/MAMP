$(document).ready(function () {
    $(document).ajaxStart(function () {
        jsonNotifyMessage("loading....")
    });
    listing();
})

listing = function (page) {
    if (typeof page == undefined || page == null) {
        page = 1;
    }
    var data = fcom.frmData(document.frmUserSearchPaging);

    moveToTop();
    fcom.ajax(fcom.makeUrl('banners', 'lists', [page]), data, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            $("#listing").html(json.msg);
            jsonSuccessMessage("List Updated.");

        } else {
            jsonErrorMessage(json.msg);
        }
    });


}


getForm = function (banner_id) {
    if (typeof banner_id === undefined) {
        banner_id = 0;
    }

    fcom.ajax(fcom.makeUrl('banners', 'form'), {"banner_id": banner_id}, function (json) {
        jsonRemoveMessage();
        json = $.parseJSON(json);
        if ("1" == json.status) {
            $("#form-tab").html(json.msg);
            moveToTop();

        } else {
            jsonErrorMessage(json.msg);
        }
    });

}

search = function (form) {

    fcom.ajax(fcom.makeUrl('banners', 'lists'), fcom.frmData(form), function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            $("#listing").html(json.msg);
            $('#clearSearch').show();
            jsonSuccessMessage("List Updated.")

        } else {
            jsonErrorMessage(json.msg);
        }
    });

}


submitForm = function (v, form_id) {
    $('#' + form_id).ajaxSubmit({
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
                jsonSuccessMessage(json.msg);
                closeForm();
                listing();
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });

}


changeOrder = function (record_id, input) {
    if (typeof record_id == undefined || record_id == null) {
        return false;
    }

    fcom.ajax(fcom.makeUrl('banners', 'change-display-order'), {record_id: record_id, display_order: input.value}, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            jsonSuccessMessage(json.msg)

        } else {
            jsonErrorMessage(json.msg)
        }
    });
}

removeBanner = function (b_id) {
    if (!confirm("Are You Sure!")) {
        return;
    }
    fcom.ajax(fcom.makeUrl('banners', 'remove'), {banner_id: b_id}, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            jsonSuccessMessage(json.msg)
            listing();
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



