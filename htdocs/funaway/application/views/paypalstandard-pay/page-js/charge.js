(function($){ 
	var runningAjaxReq = false;
	processOrder = function(){
		fcom.updateWithAjax(fcom.makeUrl('paypalstandardPay', 'process'), '', function(t) {
			runningAjaxReq = false;
			
		});
	}
})(jQuery);