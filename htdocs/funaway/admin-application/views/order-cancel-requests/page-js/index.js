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
		var data = fcom.frmData(document.frmUserSearchPaging);
		
		fcom.ajax(fcom.makeUrl('order-cancel-requests', 'lists', [page]), data, function(json) {
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
	
	
search = function(form){
	
	fcom.ajax(fcom.makeUrl('order-cancel-requests', 'lists'), fcom.frmData(form), function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				$('#clearSearch').show();
				jsonSuccessMessage("List Updated.");
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});

}

getForm = function(request_id){
	if(typeof request_id === undefined){
		request_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('order-cancel-requests', 'form'), {"request_id":request_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				moveToTop();
				
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
				closeForm();
				listing();
			}else{
				jsonErrorMessage(json.msg);
			}
		});
	return false;

}
clearSearch = function(){
	$('.search-input').val('');
	$('#pretend_search_form input').val('');
	listing();
	$('#clearSearch').hide();
}
	
})();