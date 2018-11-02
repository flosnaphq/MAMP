(function() {
	updatePaymentMethod = function(frm, v) {
		
		v.validate();
		if (!v.isValid()){
			return false;
		}
		var data = new FormData(frm);
		data.append('fIsAjax', 1);
		$.mbsmessage('Please wait...');
		// fcom.updateWithAjax(fcom.makeUrl('PaymentMethods', 'updatePaymentMethod'), data, function(t) {
		fcom.uploadFilesWithAjax(fcom.makeUrl('PaymentMethods', 'updatePaymentMethod'), data, function(t) {
			$.mbsmessage.close();
			if(t.status == 1){
				location.href=window.location;
			}else{
				$.systemMessage(t.msg);
			}
		});
	};
})();