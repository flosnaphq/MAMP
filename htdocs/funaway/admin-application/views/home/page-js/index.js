(function($){
	var runningAjaxRequest = false;
	
	listing = function(){
		fcom.ajax(fcom.makeUrl('home', 'orderList'), {}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
	};
})(jQuery);

$(document).ready(function(){
	listing();
});