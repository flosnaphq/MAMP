submitSearch = function(v, form){
	v.validate();
	if (!v.isValid()){
		return false;
	}
	jsonNotifyMessage('Loading...');
		fcom.ajax(fcom.makeUrl('hostReports', 'reportsListing'), fcom.frmData(form), function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$("#listing").html(json.msg);
					jsonRemoveMessage();
				//	moveTo($("#listing"));
					
				}else{
					jsonErrorMessage(json.msg);
				}
			});

	}