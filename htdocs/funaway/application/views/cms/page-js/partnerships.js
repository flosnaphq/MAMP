(function() {
	

	submitForm = function(v,frm) {
		v.validate();
		if (!v.isValid()) return;
		jsonStrictNotifyMessage();
		fcom.ajax($(frm).attr('action'),fcom.frmData(frm), function(json) {
			
			json = $.parseJSON(json);
			if(json.status == '1'){
				
				jsonSuccessMessage(json.msg);
				$(frm)[0].reset();
				//refreshCaptcha('image');
			}
			else{
				//refreshCaptcha('image');
				jsonErrorMessage(json.msg);
			}
		});
		
		
	};
	
	
})();