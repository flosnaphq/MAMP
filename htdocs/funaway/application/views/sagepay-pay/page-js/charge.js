(function ($) {
    var runningAjaxReq = false;
    processOrder = function () {
        fcom.updateWithAjax(fcom.makeUrl('sagepayPay', 'process'), '', function (t) {
            runningAjaxReq = false;

        });
    }

})(jQuery);





        