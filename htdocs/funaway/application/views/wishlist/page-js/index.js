$(document).ready(function(){
	listing();
});
var currentPage = 1;

listing = function(page){
	if(typeof page === 'undefined'){
		page = 1;
	}
	jsonNotifyMessage('Loading...');
	currentPage = page;
	$.ajax({
		url:fcom.makeUrl("wishlist","listing"),
		data: {"page":page},
		type: "post",
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(1 == json.status){
				$(".activity-list").html(json.msg);
				$('.modaal-ajax').modaal({
						type: 'ajax'
				});
			
			}else{
				$(".activity-list").html(json.msg);
			}
		}
		
	});
}

deleteActivity = function(msg, activity_id){
	
	confirmbox(msg,function(outcome){
		if(outcome){
			jsonStrictNotifyMessage();
			fcom.ajax(fcom.makeUrl('wishlist','delete'),{activity_id:activity_id},function(json){
				json = $.parseJSON(json);
				if(json.status == 1){
					jsonSuccessMessage(json.msg);
					listing(currentPage);
				}
				else{
					jsonErrorMessage(json.msg);
				}
			});
		}
	});
	
}

