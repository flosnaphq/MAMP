$(document).ready(function(){
	listing(1,-1);
	
	
});

var CurrentUserType = -1;
var CurrentPage =1;
var popupReply = false;
var message_id =0;
listing = function(page, userType){
	jsonNotifyMessage('Loading...');
	if(typeof page === 'undefined'){
		page = 1;
	}
	if(typeof userType === 'undefined'){
		userType = -1;
	}
	CurrentPage = page;
	CurrentUserType = userType;
	$.ajax({
		url:fcom.makeUrl("message","listing"),
		data: {"page":page, userType:userType},
		type: "post",
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(1 == json.status)
			{
				$(".message-list").html(json.msg);
				readlessmore();
				$(".reply-msg").modaal();
			}
			else
			{
				$(".message-list").html(json.msg);
			}
		}
		
	});
}


/* newMessage = function(){
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
} */


submitForm = function(v,form_id,thread){
	var action_form = $('#'+form_id);
	v.validate();
	if (!v.isValid()){
		return false;
	}
	data = fcom.frmData(action_form);
	data +='&message_id='+message_id;
	jsonStrictNotifyMessage('Sending...');
	fcom.ajax($(action_form).attr('action'),data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status)
			{
				$(action_form)[0].reset();
				jsonSuccessMessage(json.msg);
				if(popupReply == false)
				{
					if(typeof thread  === "undefined" || thread == 0  || thread == null)
					{
						listing(1,CurrentUserType);
					}
					else
					{
						//viewThread(thread);
						appnedMsg(json.html);
						message_id = json.message_id;
					}	
				}
				else
				{
					listing(1, CurrentUserType);
				}
				//$.facebox.close();
				$('.reply-msg').modaal('close');
			}
			else
			{
				jsonErrorMessage(json.msg);
			}
		});
	return false;

}

/* 
viewThread = function(thread_id){
	popupReply = false;
	$.ajax({
		url:fcom.makeUrl("message","view",[thread_id]),
		type: "post",
		success:function(json){
			json = $.parseJSON(json);
			if(1 == json.status){
				$(".message-list").html(json.msg);
				moveToLastMessage();
			}else{
				jsonErrorMessage(json.msg);
			}
		}
		
	});
} */

replyForm = function(thread_id){
	jsonNotifyMessage('Loading...');
	if(typeof thread_id === undefined || thread_id == null){
		thread_id = 0;
	}
	fcom.ajax(fcom.makeUrl('message','form'),{message_thread:thread_id},function(json){
		json = $.parseJSON(json);
		if("1" == json.status){
			jsonRemoveMessage();
			popupReply = true;
			$('.modaal-content-container').html(json.msg);
			//$.facebox(json.msg);
		}else{
			jsonErrorMessage(json.msg);
			$('.reply-msg').modaal('close');
		}
	});
}

/* function moveToLastMessage(){
	var p = $( "#last_msg" );
	pos =p.position();
	pos = pos.top;
	$("html, body").animate({ scrollTop: pos }, "slow");
} */

appnedMsg = function(htm){
	$('#chat').append(htm) .scrollTop($('#chat')[0].scrollHeight);
}
