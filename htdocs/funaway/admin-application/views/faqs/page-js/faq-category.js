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
	fcom.ajax(fcom.makeUrl('faqs', 'category-listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
}


getForm = function(category_id){
	if(typeof category_id === undefined){
		category_id = 0;
	}
	fcom.ajax(fcom.makeUrl('faqs', 'category-form'), {"category_id":category_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				moveToTop();
				$("#form-tab").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});
	
}

chanageFaqCategoryOrder = function(faqcat_id,input){
	if(typeof faqcat_id == undefined || faqcat_id == null){
		return false;
	}
	
	fcom.ajax(fcom.makeUrl('faqs', 'category-display-setup'), {faqcat_id:faqcat_id,faqcat_display_order:input.value}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg)
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});
}
search = function(form){
	
	fcom.ajax(fcom.makeUrl('faqs', 'category-listing'), fcom.frmData(form), function(json) {
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
