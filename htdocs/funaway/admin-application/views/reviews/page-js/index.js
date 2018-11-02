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
        fcom.ajax(fcom.makeUrl('reviews', 'listing', [page]), data, function (json) {
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
        fcom.ajax(fcom.makeUrl('reviews', 'listing'), data, function (json) {
            json = $.parseJSON(json);
            if ("1" == json.status) {
                $("#listing").html(json.msg);
                jsonSuccessMessage("List Updated.");

            } else {
                jsonErrorMessage(json.msg);
            }
        });
    }


    getReviewForm = function (review_id) {
        if (typeof review_id == undefined || review_id == null) {
            review_id = 0;
        }
        fcom.ajax(fcom.makeUrl('reviews', 'reviewForm'), {review_id: review_id}, function (json) {
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

    getAbuseForm = function (abreport_id) {
        if (typeof abreport_id == undefined || abreport_id == null) {
            abreport_id = 0;
        }
        fcom.ajax(fcom.makeUrl('reviews', 'abuseform'), {abreport_id: abreport_id}, function (json) {
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

        fcom.ajax(fcom.makeUrl('reviews', 'listing'), fcom.frmData(form), function (json) {
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

    closeForm = function () {
        $('#form-tab').html('');
    }
    replyToReview = function(review_id,reviewmsg_id){
        if (typeof review_id == undefined || review_id == null) {
            review_id = 0;
        }
        if (typeof reviewmsg_id == undefined || reviewmsg_id == null) {
            reviewmsg_id = 0;
        }
        fcom.ajax(fcom.makeUrl('reviews','replyToReviewForm'),{review_id:review_id, reviewmsg_id:reviewmsg_id},function(json){
            json = $.parseJSON(json);
            moveToTop();
            
            if(json.status == 1){
                jsonRemoveMessage();
                $('img.close_image:visible').click();
                $("#form-tab").html(json.msg);
                $('textarea[name=reviewmsg_message]').focus();
            }
            else{
                jsonErrorMessage(json.msg);
            }
            
        });
    }
    
    submitReplyToReview = function(v){
        
        $('#replyToReviewFrm').ajaxSubmit({ 
            delegation: true,
            beforeSubmit:function(){
                        v.validate();
                        if (!v.isValid()){
                            return false;
                        }
                    },
            success: function(json){
                
                json = $.parseJSON(json);
                if(json.status == "1"){
                    closeForm();
                    jsonSuccessMessage(json.msg);
                    listing(currentPage);
                }else{
                    jsonErrorMessage(json.msg);
                }
            }
        });
    }


})();