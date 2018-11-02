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
	fcom.ajax(fcom.makeUrl('cms', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
		

}

getShortCodeList = function()
{
	$.facebox($('#fat-shortcodes').html());
}

getForm = function(cms_id){
	if(typeof cms_id === undefined){
		cms_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('cms', 'form'), {"cms_id":cms_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				moveToTop();
				jsonSuccessMessage("Form Loaded")
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
	
}

showImageTags = function(){
	if($("input[name=cms_show_banner]:checked").val() == 1){
		$('.cms-image').show();
	}
	else{
		$('.cms-image').hide();
	}
}

search = function(form){
	
	fcom.ajax(fcom.makeUrl('cms', 'listing'), fcom.frmData(form), function(json) {
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
	
	
	var action_form = $('#'+form_id);


}


submitMetaTagForm = function(v,form_id){
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
	
	fcom.ajax(fcom.makeUrl('cms', 'cms-display-setup'), {cms_id:record_id,cms_display_order:input.value}, function(json) {
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