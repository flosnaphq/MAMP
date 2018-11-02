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
		
		fcom.ajax(fcom.makeUrl('wallet', 'lists', [page,0]), data, function(json) {
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
	
getForm = function(){
	
	
	fcom.ajax(fcom.makeUrl('wallet', 'walletTransaction'), {"user_id":0,wtran_user_type:0}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				jsonSuccessMessage("List Updated.");
				listing();
				
			}else{
				jsonErrorMessage(json.msg);
				
			}
		});
	
}	

submitForm = function(v, frm_id){
	var action_form = $('#'+frm_id);
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

closeForm = function(){
	$("#form-tab").html('');
}

})();