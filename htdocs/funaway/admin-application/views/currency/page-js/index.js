$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
})

listing = function(page){
	if(typeof page==undefined || page == null){
		page =1;
	}
	var data = fcom.frmData(document.frmUserSearchPaging);
	
	moveToTop();
	fcom.ajax(fcom.makeUrl('currency', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage('Data Loaded');
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

}


getForm = function(currency_id){
	if(typeof lang_id === undefined){
		lang_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('currency', 'form'), {"currency_id":currency_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				//jsonSuccessMessage("Form Loaded.");
				moveToTop();
				
			}else{
				jsonErrorMessage(json.msg);
				moveToTop();
			}
		});
	
}


submitForm = function(v){
	var action_form = $('#action_form');
	v.validate();
	if (!v.isValid()){
		return false;
	}
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
				jsonSuccessMessage(json.msg);
				closeForm();
				listing();
				
			}
			else{
				jsonErrorMessage(json.msg);
			}
		}
	}); 
	
	
	

}



closeForm = function(){
	$("#form-tab").html('');
}
