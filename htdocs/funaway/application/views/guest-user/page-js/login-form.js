(function() {
	login = function(frm, v) {
		v.validate();
		if (!v.isValid()) return false;
		jsonStrictNotifyMessage();
		fcom.ajax(fcom.makeUrl('GuestUser', 'login'),fcom.frmData(frm), function(json) {
			json = $.parseJSON(json);
			if(json.status == '1'){
				
				jsonSuccessMessage(json.msg);
				location.reload();
			}
			else{
				jsonErrorMessage(json.msg);
			}
		});
		
		
	};
})();




