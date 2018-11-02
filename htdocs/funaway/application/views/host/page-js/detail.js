$(document).ready(function(){
	
	$(".js-send-msg").modaal();
});
(function() {
	sendMsg = function(booking_id){
		jsonNotifyMessage('Loading...');
		fcom.ajax(fcom.makeUrl('message','msgToTraveler'),{booking_id:booking_id},function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonRemoveMessage();
				popupReply = true;
				$('.modaal-content-container').html(json.msg);
				//$.facebox(json.msg);
			}else{
				jsonErrorMessage(json.msg);
				$('.send-msg').modaal('close');
			}
		});
	}
	
	
submitForm = function(v,form_id,thread){
	var action_form = $('#'+form_id);
	v.validate();
	if (!v.isValid()){
		return false;
	}
	data = fcom.frmData(action_form);
	
	jsonStrictNotifyMessage('Sending...');
	fcom.ajax($(action_form).attr('action'),data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$(action_form)[0].reset();
				jsonSuccessMessage(json.msg);
				//$.facebox.close();
				$('.js-send-msg').modaal('close');
			}else{
				$('.js-send-msg').modaal('close');
				jsonErrorMessage(json.msg);
			}
		});
	return false;

}

	
})();