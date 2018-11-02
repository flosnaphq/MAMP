$(document).ready(function(){
	editForm();
});

editForm = function(){
	$.ajax({
		url:fcom.makeUrl("users","edit"),
		data: {"user_id":user_id},
		type: "post",
		success:function(json){
			json = $.parseJSON(json);
			if(1 == json.status){
				$(".message-list").html(json.msg);
			}else{
				$(".message-list").html(json.msg);
			}
		}
		
	});
}


submitForm = function(v,form_id){
	var action_form = $('#'+form_id);
	v.validate();
	if (!v.isValid()){
		return false;
	}
	data = fcom.frmData(action_form);
	data +="&user_id="+user_id;
	fcom.ajax($(action_form).attr('action'),data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg);
			}else{
				
				jsonErrorMessage(json.msg);
			}
		});
	return false;
}
removeUserImage = function (ids) {
    confirmbox('Do You Want To Remove Image ?', function (outcome) {
        if (outcome) {
			
            jsonNotifyMessage('processing...');
            $.ajax({
                type: "post",
                data: {"user_id": ids, fIsAjax: 1},
                url: fcom.makeUrl("users", "remove-image"),
                success: function (json) {
                    json = $.parseJSON(json);
                    if (1 == json.status) {
                        $("#u-pic").attr("src", $("#u-pic").attr("src") + "1");
                        jsonSuccessMessage(json.msg);
						location.reload();
                    }
                    else {
                        jsonErrorMessage(json.msg);
                    }

                }
            });
        }

    });

};

