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
	fcom.ajax(fcom.makeUrl('cancellationPolicies', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});
		

}


getForm = function(cancellationpolicy_id){
	if(typeof cancellationpolicy_id === undefined){
		cancellationpolicy_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('cancellationPolicies', 'form'), {"cancellationpolicy_id":cancellationpolicy_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				jsonSuccessMessage('Form Loaded');
				moveToTop();
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});
	
}

search = function(form){
	
	fcom.ajax(fcom.makeUrl('CancellationPolicies', 'listing'), fcom.frmData(form), function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				$('#clearSearch').show();
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});

}

submitForm = function(v,form_id){
	var action_form = $('#'+form_id);


	v.validate();
	if (!v.isValid()){
		return false;
	}
	fcom.ajax($(action_form).attr('action'), fcom.frmData(action_form), function(json) {
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


changeOrder = function(record_id,input){
	if(typeof record_id == undefined || record_id == null){
		return false;
	}
	
	fcom.ajax(fcom.makeUrl('CancellationPolicies', 'display-setup'), {cancellationpolicy_id:record_id,cancellationpolicy_display_order:input.value}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg)
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});
}

getSlug = function(name){
	if($('#cms_slug').val() != '') return false;
	
	fcom.ajax(fcom.makeUrl('cms', 'get-slug'), {cms_name:name}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$('#cms_slug').val(json.msg)
				
			}
		});
}

clearSearch = function(){
	$('.search-input').val('');
	$('#pretend_search_form input').val('');
	listing();
	$('#clearSearch').hide();
}

showTab = function(tab){
	if(tab == 1){
		$('#tab1').css('display','block');
		$('#tab2').css('display','none');
	}
	else if(tab == 2){
		$('#tab1').css('display','none');
		$('#tab2').css('display','block');
	}

}

closeForm = function(){
	$("#form-tab").html('');
}