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
	var data = fcom.frmData(document.frmUserSearchPaging)+'&fIsAjax=1&fOutMode=html';
	
	moveToTop();
	var city_id = $('#city_id').val();
	jQuery.ajax({
		type:"POST",
		data : data,
		url:fcom.makeUrl("locations","regions-lists",[city_id,page]),
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

deleteRegion = function(city_id,region_id){
	if(!confirm('Do you real want to delete')) return false;
	jQuery.ajax({
		type:"POST",
		data:{region_id:region_id,city_id:city_id,'fIsAjax':1,fOutMode:'html'},
		url:fcom.makeUrl("locations","region-delete"),
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				//$("#form-tab").html(json.msg);
				$("#listing").html(json.msg);
				jsonSuccessMessage("Region deleted Admin.");
				listing();
				
			}else{
				jsonErrorMessage(json.msg)
			}
		}
	});
}

getForm = function(city_id,region_id){
	if(typeof city_id === undefined){
		city_id = 0;
	}
	if(typeof region_id === undefined){
		region_id = 0;
	}
	jQuery.ajax({
		type:"POST",
		url:fcom.makeUrl("locations","region-form"),
		data:{"city_id":city_id,"region_id":region_id,'fIsAjax':1,fOutMode:'html'},
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

searchForm = function(form,city_id){
	
	jQuery.ajax({
		type:"POST",
		data:{keyword:form.keyword.value,city_id:form.city_id.value,'fIsAjax':1,fOutMode:'html'},
		url:fcom.makeUrl("locations","regions-lists",[city_id]),
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
