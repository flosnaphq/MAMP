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
	data+= '&service_parent_id='+service_parent_id;
	moveToTop();
	fcom.ajax(fcom.makeUrl('service', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
		

}


getForm = function(service_id){
	if(typeof service_id === undefined){
		service_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('service', 'form'), {"service_id":service_id,service_parent_id:service_parent_id}, function(json) {
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

chanageServiceOrder = function(input){
	fcom.ajax(fcom.makeUrl('service', 'service-display-order',[service_id]), {service_display_order:input.value}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg)
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});
}

search = function(form){
	var data = fcom.frmData(form);
	data+= '&service_parent_id='+service_parent_id;
	fcom.ajax(fcom.makeUrl('service', 'listing'), data, function(json) {
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
	/*  $('#frm_mbs_id_frmMeta').ajaxSubmit({ delegation: true, beforeSubmit:function(){ v.validate(); if (!v.isValid()){ return false; } }, success: function(json){ json = $.parseJSON(json); $.mbsmessage(json.msg);	if(json.status == "1"){ } } }); } */
	 
	 action_form.ajaxSubmit({
			delgation: true,
			beforeSubmit:function(){ 
				v.validate();
				if (!v.isValid()){ 
					return false; 
				}	 
			},
			success:function(json){
				json = $.parseJSON(json);
				if("1" == json.status){
					jsonSuccessMessage(json.msg);
					closeForm();
					listing();
				}else{
					jsonErrorMessage(json.msg);
				}
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
	$('#form-tab').html('');
}

setFeatured = function(el, service_id){
	var status = 0;
		if($(el).hasClass('active')){
			status = 1;
		}
		$(el).toggleClass("active");
		fcom.ajax(fcom.makeUrl('service', 'setFeatured'), {"service_id":service_id, status : status}, function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					jsonSuccessMessage(json.msg);
				}else{
					$(el).toggleClass("active");
					jsonErrorMessage(json.msg);
				}
			});
}

removeImage = function(ids){
		$.ajax({
			type:"post",
			data:{"service_id":ids},
			url:fcom.makeUrl("service","remove-image"),
			success:function(json){
				json = $.parseJSON(json);
				if(1==json.status){
					$(".img-container").remove();	
				}
				alert(json.msg);	
				location.reload(); 
			}
		});
}
