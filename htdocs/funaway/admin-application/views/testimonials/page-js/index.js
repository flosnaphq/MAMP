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
	fcom.ajax(fcom.makeUrl('testimonials', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.");
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

}

changeDisplayOrder = function(display_order, id){
	
	
	fcom.ajax(fcom.makeUrl('testimonials', 'changeDisplayOrder'), {id:id,display_order:display_order}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg);
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

}


getForm = function(id){
	if(typeof id === undefined){
		id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('testimonials', 'form'), {"id":id}, function(json) {
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
	
	fcom.ajax(fcom.makeUrl('testimonials', 'listing'), fcom.frmData(form), function(json) {
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


	
	var action_form = $('#action_form');
	
		$('#action_form').ajaxSubmit({ 
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
					closeForm();
					jsonSuccessMessage(json.msg);
					listing();
					
				}
				else{
					jsonErrorMessage(json.msg)
				}
			}
		}); 
	
	
	return false;

}

limitCharacters = function(limitField){
	var limitNum = $(limitField).attr('maxLength');
	limitNum = limitNum || 0;
	
	if(limitNum < 1)
	{
		return true;
	}
	
	var limitCount = $(limitField).parent().find('span.wordsremain');
	var textLength = $(limitField).val().length;
	var textRemaining = limitNum - textLength;
	
	if (textLength > limitNum)
	{
		$(limitField).val($(limitField).substring(0, limitNum));
	}
	
	$(limitCount).html(textRemaining + ' remaining');
};

clearSearch = function(){
	$('.search-input').val('');
	$('#pretend_search_form input').val('');
	listing();
	$('#clearSearch').hide();
}

closeForm = function(){
	$("#form-tab").html('');
}