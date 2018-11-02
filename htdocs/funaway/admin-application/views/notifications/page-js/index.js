$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....");
	});
	listing();
})

listing = function(page){
	if(typeof page==undefined || page == null){
		page =1;
	}
	var data = {user_id:user_id,page:page}
	
	moveToTop();
	fcom.ajax(fcom.makeUrl('notifications', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage('Data Loaded');
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

}




