$(document).ready(function(){
	getCart();
});

deleteFromCart = function(msg,cart_id,activity_id,event_id){
	
	confirmbox(msg,function(outcome){
		if(outcome){
			$.ajax({
				url:fcom.makeUrl('cart','removeActivity'),
				type:'post',
					data:{'activity_id':activity_id,
						'event_id':event_id,
						'cart_id':cart_id,
					 },
				success:function(json){
					json = $.parseJSON(json);
					if(json.status == 1){
						$('#cartListing').html(json.htm);
                                                 $('.cartCount').html(json.cart_count);
					}else{
						jsonErrorMessage(json.msg);
					}
				}
			});	
		}
	});
	
}

updateParticpant = function(cart_id,activity_id,event_id,obj, preVal){
	$.ajax({
		url:fcom.makeUrl('cart','updateMember'),
		type:'post',
		data:{'activity_id':activity_id,
				'member':$(obj).val(),
				'event_id':event_id,
				'cart_id':cart_id,
			 },
		success:function(json){
			json = $.parseJSON(json);
			if(json.status == 1){
				$('#cartListing').html(json.htm);
			}else{
				$(obj).val(preVal);
				jsonErrorMessage(json.msg);
			}
		}
	});
}

getCart = function(){
	jsonNotifyMessage("Cart Loading....");
	$.ajax({
		url:fcom.makeUrl('cart','listing'),
		success:function(json){
			json = $.parseJSON(json);
			if(json.status == 1){
				$('#cartListing').html(json.htm);
				jsonRemoveMessage();
			}else{
				jsonErrorMessage(json.msg);
			}
		}
	});
}