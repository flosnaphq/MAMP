$(document).ready(function(){
$(".write-review").modaal();
	listing(1);
});
currentPage = 1;
listing = function(page){
	if(typeof page == undefined){
		page = 1;
	}
	$('#more-review-result').hide();
	currentPage = page;
	jsonNotifyMessage('Review Loading...');
	$.ajax({
		url:fcom.makeUrl('reviews','activityReviewListing'),
		type:'post',
		data:{'activity_id':activity_id,page:page},
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(json.status == 1){
				if(page == 1){
					$('#listing').html(json.msg);
				}
				else{
					$('#listing').append(json.msg);
				}
				if(json.more_record){
					$('#more-review-result').show();
				}
			}
			else{
				jsonErrorMessage(json.msg);
			}
				
		}
	});
}

loadMoreReviews= function(){
	listing(currentPage+1);

}

writeReview = function(activity_id){
	jsonNotifyMessage();
	$.ajax({
		url:fcom.makeUrl('reviews','form'),
		type:'post',
		data:{'activity_id':activity_id},
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
					listing(1);
				}else{
					jsonErrorMessage(json.msg);
				}
			}
		});
}

