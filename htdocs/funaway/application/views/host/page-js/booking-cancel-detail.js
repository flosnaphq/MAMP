$(document).ready(function(){
	
});
(function() {
	
	
	submitCancelForm = function(v, frm){
		
		$(frm).ajaxSubmit({ 
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
					$('.js-add-comment').modaal('close');
					jsonSuccessMessage(json.msg);
					window.location = window.location;
					
				}
				else{
					$('.js-add-comment').modaal('close');
					jsonErrorMessage(json.msg)
				}
			}
		}); 
	
		return false;

	}
	

})();