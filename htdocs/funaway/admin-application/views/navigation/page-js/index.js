$(document).ready(function(){
	$('.centered_nav > li:first  > a').trigger('click');
});
addElement = function (loc_id){
	
}

addInListing = function(cms_id){
	$.ajax({
		url:fcom.makeUrl('navigation','addCmsPage'),
		type:'post',
		data:{'loc_id':cloc_id,'cms_id':cms_id},
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(json.status == 1){
				showListing(cloc_id);
			}
		}
	});
}

addOtherInListing = function(other_id){
	$.ajax({
		url:fcom.makeUrl('navigation','addOtherPage'),
		type:'post',
		data:{'loc_id':cloc_id,'other_id':other_id},
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(json.status == 1){
				showListing(cloc_id);
			}
		}
	});
}



/* editNavigation = function(nav_id){
	$.ajax({
		url:fcom.makeUrl('navigation','removeNavigation'),
		type:'post',
		data:{'nav_id':nav_id},
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(json.status == 1){
				showListing(cloc_id);
			}
				
		}
	});
} */

deleteNavigation = function(nav_id){
	if(!confirm("Are You Sure?")){
		return;
	}
	$.ajax({
		url:fcom.makeUrl('navigation','removeNavigation'),
		type:'post',
		data:{'nav_id':nav_id},
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(json.status == 1){
				showListing(cloc_id);
			}
				
		}
	});
}

var cloc_id = 0;
showListing = function(loc_id){
	if(typeof loc_id === "undefined"){
		loc_id = 1;
	}
	cloc_id = loc_id;
	$('.nav-section').removeClass('active');
	$("#navsec-"+cloc_id).addClass('active');
	$.ajax({
		url:fcom.makeUrl('navigation','getNavigation'),
		type:'post',
		data:{'loc_id':cloc_id},
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(json.status == 1){
				$('#list-section').html(json.msg);
			}
				
		}
	});
}

addCustomLink = function(){
	$.ajax({
		url:fcom.makeUrl('navigation','addCustomLink'),
		type:'post',
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(json.status == 1){
				showCustomPopup(json.msg);
			}
		}
	});
}


submitCustomLink = function(v){

	$('#action_form').ajaxSubmit({ 
			delegation: true,
			data:{'loc_id':cloc_id},
			 beforeSubmit:function(){
							v.validate();
							if (!v.isValid()){
								return false;
							}
						}, 
			success:function(json){
				json = $.parseJSON(json);
				
				if(json.status == "1"){
					showListing(cloc_id);
				}
				CloseCustomPopup();
			}
		}); 
}

changeOrder = function(record_id,input){
	if(typeof record_id == undefined || record_id == null){
		return false;
	}
	
	fcom.ajax(fcom.makeUrl('navigation', 'navigation-display-setup'), {navigation_id:record_id,navigation_display_order:input.value}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg)
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});
}


changeWindowType = function(msg, navigarion_id, status){
		
		confirmbox(msg,function(outcome){
				if(outcome){
					fcom.ajax(fcom.makeUrl('navigation', 'changeWindowType'), {navigation_id:navigarion_id,navigation_open:status}, function(json) {
						json = $.parseJSON(json);
						if("1" == json.status){
							jsonSuccessMessage(json.msg);
						}else{
							jsonErrorMessage(json.msg);
						}
					});
				}
		});
	}