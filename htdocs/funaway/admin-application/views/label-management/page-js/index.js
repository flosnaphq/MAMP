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
	fcom.ajax(fcom.makeUrl('label-management', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.");
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

}

getForm = function(trans_key){
	if(typeof trans_key === undefined){
		trans_key = '';
	}
	
	fcom.ajax(fcom.makeUrl('label-management', 'form'), {"trans_key":trans_key}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				jsonSuccessMessage("Form Load")
				moveToTop();
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
	
}

search = function(form){
	
		fcom.ajax(fcom.makeUrl('label-management', 'listing'), fcom.frmData(form), function(json) {
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



submitForm = function(v){
	var action_form = $('#translationfrm');


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
clearSearch = function(){
		$('.search-input').val('');
		$('#pretend_search_form input').val('');
		listing();
		$('#clearSearch').hide();
	}
closeForm = function(){
	$("#form-tab").html('');
}