$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
});
(function() {
	var currentPage = 1;
	listing = function(page){
		if(typeof page==undefined || page == null){
			page =1;
		}
		currentPage = page;
		jsonNotifyMessage("loading....");
		var data = fcom.frmData(document.frmUserSearchPaging);
		fcom.ajax(fcom.makeUrl('traveler', 'request-listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonRemoveMessage();
				moveTo($("#listing"));
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		
	}
})();


addRequestToCart = function(request_id){
	$.ajax({
		url:fcom.makeUrl('traveler','requestInCart'),
		type:'post',
		data:{'request_id':request_id},
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				$('.cartCount').html(json.cart_count);
				jsonSuccessMessage(json.msg);
			}else{
				jsonErrorMessage(json.msg);
			}
		}
		
	});
	
}