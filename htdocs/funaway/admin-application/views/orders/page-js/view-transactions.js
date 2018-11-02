$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
});
(function() {
	
	
	listing = function(){
		
		var data = fcom.frmData(document.frmUserSearchPaging);
		moveToTop();
		fcom.ajax(fcom.makeUrl('orders', 'lists-transactions', [order_id]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	
	
	

	
	getForm = function(order_id){
		if(typeof order_id === undefined){
			order_id = 0;
		}
		
		fcom.ajax(fcom.makeUrl('orders', 'transaction-form'), {"order_id":order_id}, function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$("#form-tab").html(json.msg);
					jsonSuccessMessage("Form Loaded.");
					moveToTop();
					
				}else{
					jsonErrorMessage(json.msg);
				}
			});
		
	}
	

	
	submitForm = function(v){
		var action_form = $('#action_form');
	
		$('#action_form').ajaxSubmit({ 
			delegation: true,
			 beforeSubmit:function(){
							v.validate();
							if (!v.isValid()){
								return false;
							}
						}, 
			success:function(json){
				json = $.parseJSON(json);
				
				if(json.status == "1"){
					closeForm();
					jsonSuccessMessage(json.msg);
					listing();
					
				}
				else{
					jsonErrorMessage(json.msg)
				}
			}
		}); 
	
		return false;

	}
	

})();