$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	
	tab();
});
(function() {
	var currentPage = 1;
	
	
	tab = function(t){
		if(typeof t==undefined || t == null){
			t =1;
		}
		n = t-1;
		$(".js-centered-nav a").removeClass('active');
		$(".js-centered-nav li:eq("+n+") a").addClass("active");

		fcom.ajax(fcom.makeUrl('order-cancel-requests', 'tab', [t]), {cancel_id:cancel_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				moveToTop();
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	


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
				tab(2);
			}else{
				jsonErrorMessage(json.msg);
			}
		});
	return false;

}

addNewComment = function(cancel_id){
	if(typeof cancel_id == undefined || cancel_id == null || cancel_id == 0){
		return false;
	}
	fcom.ajax(fcom.makeUrl('orderCancelRequests','addComment'), {cancel_id:cancel_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$.facebox(json.msg);
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
	return false;
}

submitComment = function (v, frm){
	v.validate();
	if (!v.isValid()){
		return false;
	}
	fcom.ajax($(frm).attr('action'), fcom.frmData($(frm)), function(json) {
		json = $.parseJSON(json);
		
		if("1" == json.status){
			$.facebox.close();
			tab(2);
			jsonSuccessMessage(json.msg);
			
		}else{
			$.facebox.close();
			jsonErrorMessage(json.msg);
		}
	});
}
	
})();