

$(document).ready(function(){
	tab(1);
});
(function() {

	tab = function(tab){
		if(typeof tab== undefined || tab==null){
			tab = 1
		}
		jsonNotifyMessage('Loading...');
		$('.list--vertical li a').removeClass('active');
		 
		$(".list--vertical li:nth-child("+tab+") a").addClass("active");
		showProfileLoader();
		fcom.ajax(fcom.makeUrl('host', 'payout-step'),{'tab':tab}, function(json) {
			json = $.parseJSON(json);
			if(json.status == '1'){
				jsonRemoveMessage();
				$('#form-wrapper').html(json.msg);
			}
			else{
				jsonErrorMessage(json.msg);
			}
		});
	}
	
	updateBankDetails = function(v,frm) {
		v.validate();
		if (!v.isValid()) return false;
		jsonStrictNotifyMessage();
		fcom.ajax(fcom.makeUrl('host', 'setupBankAccount'),fcom.frmData(frm), function(json) {
			json = $.parseJSON(json);
			if(json.status == '1'){
				jsonSuccessMessage(json.msg);
			}
			else{
				jsonErrorMessage(json.msg);
			}
		});
	};
	showProfileLoader = function(){
		var loader = '<span>Loading...</span>';
		$('#form-wrapper').html(loader);
	}
	
})();