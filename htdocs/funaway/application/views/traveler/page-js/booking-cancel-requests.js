$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 
	});
	listing();
});
(function() {
	var currentPage = 1;
	

	
	listing = function(page){
		if(typeof page==undefined || page == null){
			page =1;
		}
		currentPage = page;
		jsonNotifyMessage("loading....");
		var data = fcom.frmData(document.frmUserSearchPaging);
	//	moveToTop();
		fcom.ajax(fcom.makeUrl('traveler', 'booking-cancel-lists', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonRemoveMessage();
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	
	
	

})();