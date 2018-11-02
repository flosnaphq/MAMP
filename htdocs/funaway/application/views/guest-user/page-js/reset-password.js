submitForm = function(v){
		$('#resetForm').ajaxSubmit({ 
			delegation: true,
			beforeSubmit:function(){
							v.validate();
							if (!v.isValid()){
								return false;
							}
							jsonStrictNotifyMessage();
						},
			success: function(json){
				json = $.parseJSON(json);
				if(json.status == "1"){
					window.location=fcom.makeUrl('guest-user','login-form');
				}
				else{
					jsonErrorMessage(json.msg);
					
				}
			}
		});
	}