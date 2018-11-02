$(document).ready(function(){
	listing(1);
	//$(".abuse-review").modaal();
});
var currentPage = 1;
var currentActivityId = '';
listing = function(page, activity_id){
	jsonNotifyMessage('Loading...');
	if(typeof page === 'undefined'){
		page = 1;
	}
	if(typeof activity_id === 'undefined'){
		activity_id = 0;
	}
	currentActivityId = activity_id;
	currentPage = page;
	$.ajax({
		url:fcom.makeUrl("review","listing"),
		data: {"page":page,activity_id:activity_id},
		type: "post",
		success:function(json){
			
			json = $.parseJSON(json);
			if(1 == json.status){
				jsonRemoveMessage();
				$(".review-list").html(json.msg);
				$(".abuse-review").modaal();
			}else{
				jsonErrorMessage(json.msg);
			}
		}
		
	});
}

replyToReview = function(review_id){
	jsonStrictNotifyMessage();
	fcom.ajax(fcom.makeUrl('review','replyToReviewForm'),{review_id:review_id},function(json){
		json = $.parseJSON(json);
		
		if(json.status == 1){
			jsonRemoveMessage();
			$('.modaal-content-container').html(json.msg);
			//console.log(json.msg);
		}
		else{
			jsonErrorMessage(json.msg);
			$('.abuse-review').modaal('close');
		}
		
	});
}

submitReplyToReview = function(v){
	
	$('#replyToReviewFrm').ajaxSubmit({ 
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
                $('.abuse-review').modaal('close');
                listing(currentPage, currentActivityId);
            }else{
                jsonErrorMessage(json.msg);
            }
        }
    });
}
markAsInappropriate = function(review_id){
	jsonStrictNotifyMessage();
	fcom.ajax(fcom.makeUrl('review','markAsAbuseForm'),{review_id:review_id},function(json){
		json = $.parseJSON(json);
		
		if(json.status == 1){
			jsonRemoveMessage();
			$('.modaal-content-container').html(json.msg);
			//console.log(json.msg);
		}
		else{
			jsonErrorMessage(json.msg);
			$('.abuse-review').modaal('close');
		}
		
	});


}

submitAbuseReport = function(v){
	
	$('#abuseReviewForm').ajaxSubmit({ 
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
					$('.abuse-review').modaal('close');
					listing(currentPage, currentActivityId);
				}else{
					jsonErrorMessage(json.msg);
				}
			}
		});
}

