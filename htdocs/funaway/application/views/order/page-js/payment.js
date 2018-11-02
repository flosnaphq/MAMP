makeActive = function (n){
	n = n - 1;
	
	$(".js-pay-ul li a").removeClass('current');
	$(".js-pay-ul li:eq("+n+") a").addClass("current");
}
paymentTab = function(tab){
	if(typeof tab == 'undefined' || tab == 'null'){
		tab = 1;
	}
	
	makeActive(tab);
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
orderEmail = function(v,frm){
	v.validate();
	if (!v.isValid()){
		return false;
	} 
	jsonStrictNotifyMessage("Processing...");
	fcom.ajax($(frm).attr('action'),fcom.frmData(frm),function(json){
		json = $.parseJSON(json);
		if(json.status == 1){
			$('#form-tab').html(json.msg);
			/*0081 #013863[*/
			loadCountryCodes($('select[name="user_country_id"]'));
			/*]*/
			jsonRemoveMessage();
		}
		else{
			jsonErrorMessage(json.msg);
		}
	});
	
}

orderLogin = function(v, frm){
	
	v.validate();
	if (!v.isValid()){
		return false;
	} 
	jsonStrictNotifyMessage("Processing...");
	fcom.ajax($(frm).attr('action'),fcom.frmData(frm),function(json){
		json = $.parseJSON(json);
		if(json.status == 1){
			window.location = window.location;
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