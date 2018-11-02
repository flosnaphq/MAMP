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
	fcom.ajax(fcom.makeUrl('advertisements', 'ad-lists', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
		

}


getForm = function(ad_id){
	if(typeof ad_id === undefined){
		ad_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('advertisements', 'ad-form'), {"ad_id":ad_id}, function(json) {
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
	
	fcom.ajax(fcom.makeUrl('advertisements', 'ad-lists'), fcom.frmData(form), function(json) {
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

submitForm1 = function(v,form_id){
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

submitForm = function(v,form_id){
 	$('#'+form_id).ajaxSubmit({ 
		delegation: true,
		beforeSubmit:function(){
						v.validate();
						if (!v.isValid()){
							return false;
						}
					},
		success:function(json){
			json = $.parseJSON(json);
			if(json.status == "1"){
				jsonSuccessMessage(json.msg);
				closeForm();
				listing();
				
			}
			else{
				jsonErrorMessage(json.msg);
			}
		}
	}); 
 
 }


changeOrder = function(record_id,input){
	if(typeof record_id == undefined || record_id == null){
		return false;
	}
	
	fcom.ajax(fcom.makeUrl('advertisements', 'change-display-order'), {record_id:record_id, display_order:input.value}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg)
				
			}else{
				jsonErrorMessage(json.msg)
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
