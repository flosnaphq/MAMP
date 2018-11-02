$(document).ready(function () {
    $('.modaal-ajax').modaal({
        type: 'ajax',
        fullscreen: true,
    });
    listing();
});

var currentPage = 1;

listing = function (page) {
    if (typeof page === 'undefined') {
        page = 1;
    }
    jsonNotifyMessage('Loading...');

    currentPage = page;

    fcom.ajax(fcom.makeUrl("country", "activities"), {'page': page, 'country_id': country_id}, function (json) {
        json = $.parseJSON(json);
        if (json.status == 1) {
                jsonRemoveMessage('');
            if (json.noResult == 1) {
                return false;
            }

            if (json.see_all == 1) {
                $('#see-all-activity').show();
            }
            $('#island-activities-list').html(json.msg);
            $('#ACTIVITIES').show();
        
        }
        else {
            jsonErrorMessage(json.msg);
        }
    });

}