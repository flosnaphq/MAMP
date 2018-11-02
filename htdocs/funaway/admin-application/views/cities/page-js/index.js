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
	fcom.ajax(fcom.makeUrl('cities', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
		

}


getForm = function(country_id){
	if(typeof country_id === undefined){
		country_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('cities', 'form'), {"country_id":country_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				moveToTop();
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
	
}

search = function(form){
	
	fcom.ajax(fcom.makeUrl('cities', 'listing'), fcom.frmData(form), function(json) {
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




setFeatured = function(el, city_id){
	var status = 0;
		if($(el).hasClass('active')){
			status = 1;
		}
		$(el).toggleClass("active");
		fcom.ajax(fcom.makeUrl('cities', 'setFeatured'), {"city_id":city_id, status : status}, function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					jsonSuccessMessage(json.msg);
				}else{
					$(el).toggleClass("active");
					jsonErrorMessage(json.msg);
				}
			});
}

changeOrder = function(record_id,input){
	if(typeof record_id == undefined || record_id == null){
		return false;
	}
	
	fcom.ajax(fcom.makeUrl('cities', 'city-display-setup'), {city_id:record_id,city_display_order:input.value}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg)
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});
}