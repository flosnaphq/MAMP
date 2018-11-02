$(document).ready(function(){
	listing();
});
var currentPage =1;
listing  = function (page){
	if(typeof page==undefined  || page == null){
		page = 1
	}
	currentPage= page;
	jsonNotifyMessage('Loading...');
	fcom.ajax(fcom.makeUrl('activity','host-activity-listing'),{page:page,host_id:host_id},function(json){
		json = $.parseJSON(json);
		if(json.status == 1){
			jsonRemoveMessage();
			
			if(page == 1){
				$('#js-activity-list').html(json.msg);
			}
			else{
				$('#js-activity-list').append(json.msg);
			}
                        if(json.more_record==1){
                            $('.showMoreButton').show();
                        }else{
                            
                               $('.showMoreButton').hide();
                        }
                        
			$('.modaal-ajax').modaal({
						type: 'ajax'
				});
		}
		else{
			jsonErrorMessage(json.msg);
		}
	});
}

showMoreActivity  = function(){
	listing(currentPage+1);
}

wishlist = function(obj,activityId){
	jsonNotifyMessage();
	$.ajax({
		url:fcom.makeUrl("wishlist","add-to-wish"),
		data: {"activity_id":activityId,fIsAjax:1
			  },
		type: "post",
		success:function(json){
			json = $.parseJSON(json);
			if(1 == json.status ){
				jsonSuccessMessage(json.msg);
				if(json.type == "add"){
					$(obj).addClass('has--active');
				}else{
					$(obj).removeClass('has--active');
				}
			}else{
				jsonErrorMessage(json.msg);
			}
		}
		
	});
}
