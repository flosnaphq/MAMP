(function() {
	

	submitForm = function(v,frm) {
		v.validate();
		if (!v.isValid()) return;
		jsonStrictNotifyMessage();
		fcom.ajax(fcom.makeUrl('guest-user', 'forgotPasswordSetup'),fcom.frmData(frm), function(json) {
			
			json = $.parseJSON(json);
			if(json.status == '1'){
				
				jsonSuccessMessage(json.msg);
				$('#forgotPassword')[0].reset();
				//refreshCaptcha('image');
			}
			else{
				//refreshCaptcha('image');
				jsonErrorMessage(json.msg);
			}
			grecaptcha.reset();
		});
		
		
	};
	
	
})();