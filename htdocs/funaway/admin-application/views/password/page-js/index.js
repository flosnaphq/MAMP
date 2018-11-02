

submitForm = function(v){
	var action_form = $('#action_form');
	v.validate();
	if (!v.isValid()){
		return false;
	}
	fcom.ajax(action_form.attr('action'), fcom.frmData(action_form), function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg);
				$(action_form).find("input[type=text], textarea").val("");
				$(action_form).find("input[type=password], textarea").val("");

			}else{
				jsonErrorMessage(json.msg);
			}
		});
	return false;

}