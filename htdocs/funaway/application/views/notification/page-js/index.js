$(document).ready(function(){
	listing(1);
});

listing = function(page){
	jsonNotifyMessage('Loading...');
	if(typeof page === 'undefined'){
		page = 1;
	}
	
	$.ajax({
		url:fcom.makeUrl("notification","listing"),
		data: {"page":page},
		type: "post",
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(1 == json.status){
				$(".message-list").html(json.msg);
				//moveTo('.message-list');
			}else{
				$(".message-list").html(json.msg);
			}
		}
		
	});
}

deleteNotification = function (msg, nid){
	if(confirm(msg)){
		
			jsonNotifyMessage();
			fcom.ajax(fcom.makeUrl('notification','delete'),{notification_id:nid},function(json){
				json = $.parseJSON(json);
				if(json.status == 1){
					jsonSuccessMessage(json.msg);
					listing();
				}
				else{
					jsonErrorMessage(json.msg);
				}
			});
		
	}
}

/* 
newMessage = function(){
	$.ajax({
		url:fcom.makeUrl("message","form"),
		type: "post",
		success:function(json){
			json = $.parseJSON(json);
			if(1 == json.status){
				$.facebox(json.msg);
			}else{
				$.facebox(json.msg);
			}
		}
		
	});
}
 */




