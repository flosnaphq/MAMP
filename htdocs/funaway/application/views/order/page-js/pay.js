
actionForm = function(v){
	$('#omiseForm').ajaxSubmit({ 
			delegation: true,
			beforeSubmit:function(){
						v.validate();
						if (!v.isValid()){
							return false;
						} 
						jsonStrictNotifyMessage("processing......");
					},
			success: function(json){
			
				json = $.parseJSON(json);
				if(json.status == "1"){
					jsonSuccessMessage(json.msg);
					  window.location = json.redirect;
				}else{
					jsonErrorMessage(json.msg);
				}
			}
		}); 
}