$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....");
	});
	listing();
})
var currentPage = 1;
listing = function(page){
	if(typeof page==undefined || page == null){
		page =1;
	}
	var data = {page:page}
	currentPage = currentPage;
	moveToTop();
	fcom.ajax(fcom.makeUrl('UserRequest', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage('Data Loaded');
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

}
search = function(form){
	
	fcom.ajax(fcom.makeUrl('UserRequest', 'listing'), fcom.frmData(form), function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				$('#clearSearch').show();
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});

}
changeStatus = function(msg, request_id, status){
		
		confirmbox(msg,function(outcome){
				if(outcome){
					fcom.ajax(fcom.makeUrl('UserRequest', 'changeStatus'), {request_id:request_id,status:status}, function(json) {
						json = $.parseJSON(json);
						if("1" == json.status){
							jsonSuccessMessage(json.msg);
							listing(currentPage);
						}else{
							jsonErrorMessage(json.msg);
						}
					});
				}
		});
	}


