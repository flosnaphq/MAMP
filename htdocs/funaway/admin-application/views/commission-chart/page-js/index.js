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
	fcom.ajax(fcom.makeUrl('commission-chart', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
		

}


getForm = function(com_id){
	if(typeof com_id === undefined){
		com_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('commission-chart', 'form'), {"com_id":com_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				jsonSuccessMessage("List Updated.");
				moveToTop();
				
			}else{
				jsonErrorMessage(json.msg);
				moveToTop();
			}
		});
	
}

deleteCommission = function(com_id){
	confirmbox('Do You Want To Delete?', function(outcome){
		if(outcome){
			if(typeof com_id === undefined){
				com_id = 0;
			}
			
			fcom.ajax(fcom.makeUrl('commission-chart', 'deleteCommission'), {"com_id":com_id}, function(json) {
					json = $.parseJSON(json);
					if("1" == json.status){
						jsonSuccessMessage("List Updated.");
						listing();
						
					}else{
						jsonErrorMessage(json.msg);
						
					}
				});
		}
	});
	
	
	
}

search = function(form){
	
	fcom.ajax(fcom.makeUrl('commission-chart', 'listing'), fcom.frmData(form), function(json) {
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

closeForm = function(){
	$("#form-tab").html('');
}
