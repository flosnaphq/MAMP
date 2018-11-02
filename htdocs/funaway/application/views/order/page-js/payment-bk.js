var emailExist = false;
paymentTab = function(tab){
	if(typeof tab == 'undefined' || tab == 'null'){
		tab =1;
	}
	jsonNotifyMessage("Loading...");
	fcom.ajax(fcom.makeUrl('order','paymentTab',[tab]),{},function(json){
		json = $.parseJSON(json);
		if(json.status == 1){
			$('#form-tab').html(json.msg);
			jsonRemoveMessage();
		}
		else{
			jsonErrorMessage(json.msg);
			if(json.next_step != 'undefined'){
				paymentTab(json.next_step);
			}
		}
	});
}

checkEmail = function(email){
	emailExist = false;
	jsonStrictNotifyMessage("Processing...");
	fcom.ajax(fcom.makeUrl('order','checkEmail'),{email:email},function(json){
		json = $.parseJSON(json);
		
		if(json.status == 1){
			jsonRemoveMessage();
			if(json.emailExist == 1){
				emailExist = true;
				$('#js-email-msg').html(json.msg);
				$('#js-email-msg').show();
			//	$('#js-current-password-wrapper').show();
				$('#js-current-password').focus();
			}
			else{
				$('#js-email-msg').html('');
				$('#js-email-msg').hide();
			//	$('#js-current-password-wrapper').hide();
			}
		}
		else{
			jsonErrorMessage(json.msg);
			
			
		}
	});

}

loginUser = function (){
	
	var pwd = $('#js-current-password').val();
	var email = $('#js-email').val();
	if(email == '' || pwd == ''){
		return false;
	}
	if(emailExist == false){
		return false;
	}
	jsonStrictNotifyMessage("Processing...");
	fcom.ajax(fcom.makeUrl('order','loginUser'),{email:email,pwd:pwd}, function(json){
		json = $.parseJSON(json);
		if(json.status == 1){
			jsonSuccessMessage(json.msg);
			paymentTab(1);
		}
		else if(json.status == 2){
			// Do nothing
			jsonRemoveMessage();
		}
		else{
			jsonErrorMessage(json.msg);
		}
	});
	
}

updateProfile = function(v,frm){
	$(frm).ajaxSubmit({ 
			delegation: true,
			beforeSubmit:function(){
						v.validate();
						if (!v.isValid()){
							return false;
						} 
						jsonStrictNotifyMessage("Processing...");
					},
			success: function(json){
			
				json = $.parseJSON(json);
				if(json.status == "1"){
					jsonSuccessMessage(json.msg);
					window.location = window.location;
					// paymentTab(2);
				}else{
					jsonErrorMessage(json.msg);
				}
			}
		}); 
}

actionForm = function(v){
	$('#omiseForm').ajaxSubmit({ 
			delegation: true,
			beforeSubmit:function(){
						v.validate();
						if (!v.isValid()){
							return false;
						} 
						jsonStrictNotifyMessage("processing......");
					},
			success: function(json){
			
				json = $.parseJSON(json);
				if(json.status == "1"){
					jsonSuccessMessage(json.msg);
					  window.location = json.redirect;
				}else{
					jsonErrorMessage(json.msg);
					
				}
			}
		}); 
}