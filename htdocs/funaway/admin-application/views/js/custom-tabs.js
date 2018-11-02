jQuery.fn.intializeTab = function (defaultHash) {
    var hash = window.location.hash;

    if (!hash) {
        hash = defaultHash;
        window.location.hash = defaultHash;
    }
    jQuery('a[href="' + hash + '"]').trigger('click');
};

jQuery.fn.tabLoader = function (tab, obj) {
    $(".tab_content").hide();
    $(".nmltabs > li").removeClass("active as");
    $(obj).parent("li").addClass("active as");
    var $selectedTab = $('#tab-' + tab);
    $selectedTab.show();
    //Not to load tab if already loaded
    if ($selectedTab.html().length > 10) {
        return false;
    }

    var href = $(obj).data('href');
    jQuery.fn.showLoader($selectedTab);

    fcom.ajax(href, {}, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            setTimeout(function () {
                $selectedTab.html(json.msg);
            }, 1000);

        } else {
            jsonErrorMessage("something went wrong.")
        }
    });
};


jQuery.fn.showLoader = function ($elm) {

    var loader = '<div class="wraplayer"> \
                                <div class="layercontent"> \
                                    <div class="circularLoader">\
                                        <svg class="circular" height="30" width="30"> \
                                            <circle class="path" cx="25" cy="25.2" r="19.9" fill="none" stroke-width="6" stroke-miterlimit="10"></circle> \
                                        </svg> \
                                    </div> \
                                </div></div>';
    $elm.html(loader);

};

$(document).ready(function () {

    jQuery.fn.intializeTab('#info');

});

jQuery.fn.submitImageForm = function (frm_id) {

    $('#' + frm_id).ajaxSubmit({
        delegation: true,
        url: $('#' + frm_id).attr('action'),
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == "1") {
                window.location.reload();
            }
            else {
                jsonErrorMessage(json.msg);
            }
        }
    });
};



jQuery.fn.changeDisplayOrder = function (afile_id, elem) {
    if (typeof elem === undefined) {
        return false;
    }
    if (typeof afile_id === undefined) {
        afile_id = 0;
    }
    var display_order = $(elem).val();
    var frmAction = $(elem).data('action');

    fcom.ajax(frmAction, {"afile_id": afile_id, display_order: display_order}, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
            jsonSuccessMessage(json.msg);
            window.location.reload();

        } else {
            jsonErrorMessage(json.msg);
        }
    });
}

jQuery.fn.removeImage = function (elem, msg, afile_id) {
    if (typeof elem === undefined) {
        return false;
    }
    if (typeof afile_id === undefined) {
        afile_id = 0;
    }
    var frmAction = $(elem).data('href');
    confirmbox(msg, function (outcome) {
        if (outcome) {
            fcom.ajax(frmAction, {"afile_id": afile_id}, function (json) {
                json = $.parseJSON(json);
                if ("1" == json.status) {
                    jsonSuccessMessage(json.msg);
                    window.location.reload();

                } else {
                    jsonErrorMessage(json.msg);
                }
            });
        }
    });
}


function successCallback(json) {
    if (json.reload == '1') {
      window.location.href = window.location.pathname +"/"+ json.recordId;
  
    }
}