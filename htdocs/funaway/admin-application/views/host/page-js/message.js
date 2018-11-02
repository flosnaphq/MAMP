$(document).ready(function(){
	listing(1);
});

listing = function(page){
	if( typeof user_id === 'undefined'){
		return;
	}
	
	if(typeof page === 'undefined'){
		page = 1;
	}
	var data = fcom.frmData(document.frmUserSearchPaging);
	data+='&user_id='+user_id;
	data+='&fIsAjax=1';
	data+='&page='+page;
	$.ajax({
		url:fcom.makeUrl("chats","listing"),
		data: data,
		type: "post",
		success:function(json){
			json = $.parseJSON(json);
			if(1 == json.status){
				$(".message-list").html(json.msg);
			}else{
				jsonErrorMessage(json.msg);
			}
		}
		
	});
}

search = function(form){
		jsonNotifyMessage("loading...");
		fcom.ajax(fcom.makeUrl('chats', 'listing',[1,user_id]), fcom.frmData(form)+'&user_id='+user_id+'&page=1&fIsAjax:1', function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$(".message-list").html(json.msg);
					jsonRemoveMessage();
				}else{
					jsonErrorMessage(json.msg);
				}
			});

	}

newMessage = function(){
	$.ajax({
		url:fcom.makeUrl("chats","form"),
		data: {"user_id":user_id},
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


submitForm = function(v,form_id,thread){
	var action_form = $('#'+form_id);
	v.validate();
	if (!v.isValid()){
		return false;
	}
	data = fcom.frmData(action_form);
	data +="&user_id="+user_id;
	fcom.ajax($(action_form).attr('action'),data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg);
				if(typeof thread  === "undefined"){
					listing(1);
				}else{
					viewThread(thread);
				}	
				$.facebox.close();
			}else{
				
				jsonErrorMessage(json.msg);
			}
		});
	return false;

}


viewThread = function(thread_id){
	$.ajax({
		url:fcom.makeUrl("chats","view",[thread_id]),
		type: "post",
		success:function(json){
			json = $.parseJSON(json);
			if(1 == json.status){
				$(".message-listing").remove();
				$(".message-box").append(json.msg);
				$(".thread-listing").hide();
			}else{
				alert("something went wrong");
			}
		}
		
	});
}

backThread = function(){
	$(".message-listing").remove();
	$(".thread-listing").show();
}

