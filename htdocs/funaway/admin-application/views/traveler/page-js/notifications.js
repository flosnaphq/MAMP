$(document).ready(function(){
	listing(1);
});

listing = function(page){
	if(typeof page==undefined || page == null){
		page =1;
	}
	
	
	fcom.ajax(fcom.makeUrl('notifications', 'listing'), {user_id:user_id,page:page}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage('Data Loaded');
				
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

