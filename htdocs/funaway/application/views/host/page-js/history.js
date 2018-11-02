$(document).ready(function(){
	$('.js-balance-info').modaal();
	$('.js-credit-info').modaal();
	$('.js-debit-info').modaal();
	listing();
});
(function() {
	var currentPage = 1;
	var currentTab = 1;

	
	listing = function(page){
		if(typeof page==undefined || page == null){
			page =1;
		}
		
		currentPage = page;
		jsonNotifyMessage("loading...");
		var data = fcom.frmData(document.frmUserSearchPaging);
	//	moveToTop();
		fcom.ajax(fcom.makeUrl('host', 'history-listing', [page]), data, function(json) {
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
	
	submitSearch = function(form){
		jsonNotifyMessage("loading...");
		fcom.ajax(fcom.makeUrl('host', 'history-listing',[1]), fcom.frmData(form), function(json) {
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