$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
})

listing = function(){
	moveToTop();
	fcom.ajax(fcom.makeUrl('banners', 'lists'), {fIsAjax:1}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		});
		

}



submitImage = function(v){
 	$('#frm_fat_id_frmImage').ajaxSubmit({ 
		delegation: true,
		beforeSubmit:function(){
						v.validate();
						if (!v.isValid()){
							return false;
						}
					},
		success:function(json){
			json = $.parseJSON(json);
			$.mbsmessage(json.msg);	
			if(json.status == "1"){
				listing();
				$('#frm_fat_id_frmImage')[0].reset();
			}
		}
	}); 
 
 }


function deleteFile(afile_id){
	confirmbox("Are You Sure?", function (outcome){ 
		if(outcome){
			if(typeof afile_id ==="undefined"){
				return;	
			}
			jQuery.ajax({
				type:"POST",
				data:{'afile_id':afile_id,'fIsAjax':1},
				url:fcom.makeUrl("banners","delete-file"),
				success:function(json){
					json = $.parseJSON(json);
					if(json.status == "0"){
						jsonErrorMessage(json.msg)
					}else{
						listing();
					}
				}
			});	
		}
	});
}

function updateOrder(obj,afile_id){
	var file_display_order = $(obj).val();
	if(typeof afile_id ==="undefined"){
		return;	
	}
	$.mbsmessage("Processing....");	
	jQuery.ajax({
		type:"POST",
		data:{'fIsAjax':1,'afile_id':afile_id,'display_order':file_display_order},
		url:fcom.makeUrl("banners","update-order"),
		success:function(json){
			json = $.parseJSON(json);
			if(json.status == "0"){
				jsonErrorMessage(json.msg)
			}else{
				jsonSuccessMessage(json.msg)
			}
		}
	});	
	
}





