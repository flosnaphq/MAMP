$(document).ready(function () {
    listing();
    getActivities();
});
var currentPage = 1;

listing = function (page) {
    if (typeof page === 'undefined') {
        page = 1;
    }
    jsonNotifyMessage();
    $('#load-more').hide();
    currentPage = page;
    fcom.ajax(fcom.makeUrl("services", "listing"), {'page': page, 'service_id': service_id}, function (json) {
        json = $.parseJSON(json);
        if (json.status == 1) {


            if (json.more_record == 1) {
                $('#load-more').show();
            }
            if (page == 1) {
                $('#listing').html(json.msg);
            }
            else {
                $('#listing').append(json.msg);
            }

            jsonRemoveMessage('');
        }
        else {
            jsonErrorMessage(json.msg);
        }
    });

}

loadMore = function () {
    listing(currentPage + 1);
}

getActivities = function (page) {

    var page = page || 1;

    jsonNotifyMessage('Loading...');

    fcom.ajax(fcom.makeUrl("Services", "getActivities"), {'page': page, 'service_id': service_id}, function (json) {
        json = $.parseJSON(json);
        if (json.status == 1) {
            jsonRemoveMessage('');
            if (json.noResult == 1) {
                return false;
            }

            if (json.see_all == 1) {
                $('#see-all-activity').show();
            }
            $('#theme-activities-list').html(json.msg);
            $('#ACTIVITIES').show();

        }
        else {
            jsonErrorMessage(json.msg);
        }
    });
}