$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
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
		var data = fcom.frmData(document.frmUserSearchPaging);
	//	moveToTop();
		fcom.ajax(fcom.makeUrl('host', 'order-cancel-lists', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonRemoveMessage();
				moveTo($("#listing"));
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	
	
	

})();