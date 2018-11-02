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
	fcom.ajax(fcom.makeUrl('offices', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.");
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

}


getForm = function(office_id){
	if(typeof office_id === undefined){
		office_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('offices', 'form'), {"office_id":office_id}, function(json) {
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
	
	fcom.ajax(fcom.makeUrl('offices', 'listing'), fcom.frmData(form), function(json) {
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




clearSearch = function(){
	$('.search-input').val('');
	$('#pretend_search_form input').val('');
	listing();
	$('#clearSearch').hide();
}

closeForm = function(){
	$("#form-tab").html('');
}