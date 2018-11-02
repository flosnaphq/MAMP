$(document).ready(function(){
	listing(1);
});

listing = function(page){
	if(typeof page==undefined || page == null){
		page =1;
	}
	
	moveToTop();
	var data = fcom.frmData(document.frmUserSearchPaging);
	data+='&user_id='+user_id;
	data+='&wtran_user_type=1';
	data+='&page='+page;
	jsonNotifyMessage("loading...");
	fcom.ajax(fcom.makeUrl('wallet', 'lists',[page,user_id]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonRemoveMessage();
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

}

search = function(form){
		jsonNotifyMessage("loading...");
		fcom.ajax(fcom.makeUrl('wallet', 'lists',[1,user_id]), fcom.frmData(form)+'&wtran_user_type=1', function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$("#listing").html(json.msg);
					jsonRemoveMessage();
				}else{
					jsonErrorMessage(json.msg);
				}
			});

	}

getForm = function(){
	$.ajax({
		url:fcom.makeUrl("wallet","walletTransaction"),
		data: {"user_id":user_id,'wtran_user_type':1},
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




submitForm = function(v,form_id){
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
				window.location = window.location;
				$.facebox.close();
			}else{
				$.facebox.close();
				jsonErrorMessage(json.msg);
			}
		});
	return false;

}



