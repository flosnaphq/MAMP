hostup = function (v) {
    $('#frmRegister').ajaxSubmit({
        delegation: true,
        beforeSubmit: function () {
            v.validate();
            if (!v.isValid()) {
                return false;
            }
            jsonStrictNotifyMessage('Processing....');
        },
        success: function (json) {
            json = $.parseJSON(json);
            if (json.status == "1") {
                jsonSuccessMessage(json.msg);
                facebookHostSignupSuccess();
                window.location = json.url;
            } else {
                jsonErrorMessage(json.msg);
            }
        }
    });
}


		