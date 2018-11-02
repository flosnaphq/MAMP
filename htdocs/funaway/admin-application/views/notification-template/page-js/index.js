$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
})

listing = function(page){
	if(typeof page==undefined || page == null){
		page =1;
	}
	var data = fcom.frmData(document.frmUserSearchPaging);
	
	moveToTop();
	fcom.ajax(fcom.makeUrl('notification-template', 'lists', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
		

}


getForm = function(tpl_id){
	if(typeof tpl_id === undefined){
		tpl_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('notification-template', 'form'), {"tpl_id":tpl_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				jsonSuccessMessage("Form Loaded.");
				moveToTop();
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});
	
}

search = function(form){
	
	fcom.ajax(fcom.makeUrl('notification-template', 'lists'), fcom.frmData(form), function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				$('#clearSearch').show();
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});

}

submitForm = function(v,form_id){
	var action_form = $('#'+form_id);
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
