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
	var data = fcom.frmData(document.frmUserSearchPaging)+'&fIsAjax=1';
	console.log(data);
	moveToTop();
	jQuery.ajax({
		type:"POST",
		data : data,
		url:fcom.makeUrl("locations","city-lists",[page]),
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


getForm = function(city_id){
	if(typeof city_id === undefined){
		city_id = 0;
	}
	jQuery.ajax({
		type:"POST",
		url:fcom.makeUrl("locations","city-form"),
		data:{"city_id":city_id,'fIsAjax':1},
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				jsonSuccessMessage("Add/Update Admin.")
				
			}else{
				jsonErrorMessage(json.msg)
			}
		}
	});
}

searchCity = function(form){
	
	jQuery.ajax({
		type:"POST",
		data:{city_name:form.city_name.value,'fIsAjax':1},
		url:fcom.makeUrl("locations","city-lists"),
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				//$("#form-tab").html(json.msg);
				$("#listing").html(json.msg);
				$('#clearSearch').show();
				jsonSuccessMessage("Add/Update Admin.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		}
	});
}

deleteCity = function(city_id){
	if(!confirm('Do you real want to delete')) return false;
	jQuery.ajax({
		type:"POST",
		data:{city_id:city_id,'fIsAjax':1},
		url:fcom.makeUrl("locations","city-delete"),
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				//$("#form-tab").html(json.msg);
				$("#listing").html(json.msg);
				jsonSuccessMessage("City deleted Admin.");
				listing();
				
			}else{
				jsonErrorMessage(json.msg)
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
