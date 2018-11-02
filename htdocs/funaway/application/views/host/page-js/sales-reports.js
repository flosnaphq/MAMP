

$(document).ready(function(){
	tab();
});
(function() {

	tab = function(){
		
		jsonNotifyMessage('Loading...');
	
		fcom.ajax(fcom.makeUrl('host', 'getSalesReport'),{}, function(json) {
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
	
	
})();