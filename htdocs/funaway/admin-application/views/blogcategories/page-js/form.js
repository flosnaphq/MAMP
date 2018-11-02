function setSeoName(el, fld_id) {
    txt_val = el.value;

    if (txt_val.trim()) {

        txt_val = $.trim(txt_val.toLowerCase());
        txt_val = txt_val.replace(/[^a-zA-Z0-9 ]+/g, "-");
        txt_val = txt_val.replace(/\s+/g, "-");
        txt_val = $.trim(txt_val);
        txt_val = rtrim(txt_val, '-');

        var data = "category_id=" + $("#category_id").val() + "&cat_seo_name=" + txt_val;

        fcom.updateWithAjax(fcom.makeUrl('Blogcategories', 'checkUniqueSeoName'), data, function (t) {
            $.mbsmessage.close();
            $('#' + fld_id.id).val(t.category_seo_name_);
        });
    }
}

function rtrim(str, chr) {
    var rgxtrim = (!chr) ? new RegExp('\\s+$') : new RegExp(chr + '+$');
    return str.replace(rgxtrim, '');
}

function cancelCategory() {
    window.location.href = fcom.makeUrl('blogcategories');
}
(function () {
    submitCategory = function (frm, v) {

        v.validate();
        if (!v.isValid()) {
            $('ul.errorlist').each(function () {
                $(this).parents('.field_control:first').addClass('error');
            });
            return;
        }
        fcom.updateWithAjax($(frm).attr('action'), fcom.frmData(frm), function (t) {
            location.href = fcom.makeUrl('blogcategories', '');
        });

        return false;
    }

})();
