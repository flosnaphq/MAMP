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
	var faqcat_id = $('#faqcat_id').val();
	moveToTop();
	fcom.ajax(fcom.makeUrl('faqs', 'faq-listing', [faqcat_id,page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
		

}


getForm = function(faqcat_id, faq_id){
	if(typeof faq_id === undefined){
		faq_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('faqs', 'faq-form',[faqcat_id]), {"faq_id":faq_id}, function(json) {
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

chanageOrder = function(record_id,input){
	if(typeof record_id == undefined || record_id == null){
		return false;
	}
	
	fcom.ajax(fcom.makeUrl('faqs', 'change-faq-display-order'), {faq_id:record_id,faq_display_order:input.value}, function(json) {
			json = $.parseJSON(json);
			{
			if("1" == json.status){
				jsonSuccessMessage(json.msg)
				
			}else{
				jsonErrorMessage(json.msg)
			}
			}
		});
}


search = function(form){
	var faqcat_id = $('#faqcat_id').val();
	fcom.ajax(fcom.makeUrl('faqs', 'faq-listing',[faqcat_id]), fcom.frmData(form), function(json) {
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
