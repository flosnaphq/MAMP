$(document).ready(function(){
	
	listing();
	$('.js-withdrawal-info').modaal();
});
(function() {
	var currentPage = 1;
	var currentTab = 1;

	
	listing = function(page){
		if(typeof page==undefined || page == null){
			page =1;
		}
		
		currentPage = page;
		 jsonNotifyMessage("loading....");
		var data = fcom.frmData(document.frmUserSearchPaging);
	//	moveToTop();
		fcom.ajax(fcom.makeUrl('host', 'withdrawalRequestLists', [page]), data, function(json) {
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