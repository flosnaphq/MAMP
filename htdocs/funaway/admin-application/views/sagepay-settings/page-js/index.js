(function () {
    updateSagepaySettings = function (frm, v) {

        v.validate();
        if (!v.isValid()) {
            return false;
        }
        var data = fcom.frmData(frm);
        $.mbsmessage('Please wait...');
        fcom.updateWithAjax(fcom.makeUrl('SagepaySettings', 'saveSettings'), data, function (t) {
            $.mbsmessage.close();
            if (t.status == 1) {
                $.systemMessage(t.msg);
            } else {
                $.systemMessage(t.msg);
                return;
            }
        });
    };
})();