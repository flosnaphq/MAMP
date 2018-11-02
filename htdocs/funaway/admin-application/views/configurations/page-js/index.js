$(document).ready(function(){
	$( document ).ajaxStart(function() {
		// jsonNotifyMessage("loading....")
	});
	form();
})

form = function(form_type){
	if(typeof form_type==undefined || form_type == null){
		form_type =1;
	}
	jQuery.ajax({
		type:"POST",
		data : {form:form_type,fIsAjax:1},
		url:fcom.makeUrl("configurations","form"),
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#tabs_0"+form_type).html(json.msg);
			}else{
				jsonErrorMessage(json.msg)
			}
		}
	});
}




submitForm = function(form,v){
	 $(form).ajaxSubmit({ 
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
				jsonSuccessMessage(json.msg)
				
			}else{
				jsonErrorMessage(json.msg);
			}
		}		
	}); 	
	return false;
}


