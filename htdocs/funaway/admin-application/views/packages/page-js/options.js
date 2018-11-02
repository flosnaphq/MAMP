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
	moveToTop();
	var package_id = $('#package_id').val();
	jQuery.ajax({
		type:"POST",
		data : {package_id:package_id,fIsAjax:1},
		url:fcom.makeUrl("packages","option-lists",[page]),
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		}
	});
}


getForm = function(package_id,option_id){
	if(typeof package_id === undefined || package_id==null){
		jsonErrorMessage("something went wrong.")
		return false;
	}
	if(typeof option_id === undefined || option_id==null){
		option_id = 0;
	}
	jQuery.ajax({
		type:"POST",
		url:fcom.makeUrl("packages","option-form",[package_id]),
		data:{"option_id":option_id,package_id:package_id,fIsAjax:1},
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
					
					
				jsonSuccessMessage("Add/Update.")
				
			}else{
				jsonErrorMessage(json.msg);
			}
		}
	});
}




submitForm = function(v){
	$('#frm_fat_id_frmAdmin').ajaxSubmit({ 
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
				jsonSuccessMessage(json.msg)
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
