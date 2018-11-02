
(function() {
	
	
	submitForm = function(v,frm){
		
		v.validate();
		if (!v.isValid()) return false;
		
	//	moveToTop();
		fcom.ajax(fcom.makeUrl('host', 'setupWithdrawalRequest'), fcom.frmData(frm), function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg);
				window.location = fcom.makeUrl('host','withdrawalRequests');
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	
	

})();