$(document).ready(function(){
	$('.js-order-cancel').modaal({
			type: 'ajax'
	});
	$(".js-send-msg").modaal();
	$(".write-review").modaal();
	
	  $("body").on("click", ".crating", function (e) {
        var offset = $(this).offset();
        points = e.clientX - offset.left;
        widths = $(this).width();
        setWidth = parseInt(points * 100 / widths);
        if (setWidth % 10 >= 5 && setWidth % 10 != 0)
            setWidth = setWidth - setWidth % 10 + 10;
        if (setWidth % 10 < 5 && setWidth % 10 != 0)
            setWidth = setWidth - setWidth % 10;
        setWidth = setWidth | 0;
        if (setWidth == 0)
            setWidth = 10;
        $(this).children(".rating__score").css("width", setWidth + "%");
        $(".ratesfld").val(setWidth / 20);
    });
});
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
					$('.js-order-cancel').modaal('close');
					jsonSuccessMessage(json.msg);
					window.location = window.location;
					
				}
				else{
					$('.js-order-cancel').modaal('close');
					jsonErrorMessage(json.msg)
				}
			}
		}); 
	
		return false;

	}
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
writeReview = function(event_id){
	jsonNotifyMessage();
	$.ajax({
		url:fcom.makeUrl('reviews','form'),
		type:'post',
		data:{'event_id':event_id},
		success:function(json){
			json = $.parseJSON(json);
			if(json.status == 1){
				jsonRemoveMessage();
				$('.modaal-content-container').html(json.msg);
				
			}else{
				jsonErrorMessage(json.msg);
				$('.write-review').modaal('close');
			}
		}
	});
}


submitReview = function(v){
	$('#reviewForm').ajaxSubmit({ 
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
					jsonSuccessMessage(json.msg);
					$('.write-review').modaal('close');
                                        $("#activityReview").text(json.reviewCount);
                                        $("#ratingBlock").find('.rating__score').css({width:json.reviewRating+"%"});
					review(1);
				}else{
					jsonErrorMessage(json.msg);
				}
			}
		});
}
