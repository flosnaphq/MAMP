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
	fcom.ajax(fcom.makeUrl('homepage-banners', 'lists', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.");
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

}


getForm = function(banner_id,banner_type){
	if(typeof banner_id === undefined){
		banner_id = 0;
	}
	
	fcom.ajax(fcom.makeUrl('homepage-banners', 'form'), {"banner_id":banner_id,banner_type:banner_type}, function(json) {
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				moveToTop();
				
			}else{
				jsonErrorMessage(json.msg);
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




