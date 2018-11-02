$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
});
(function() {

	
	var currentPage = 1;
	listing = function(page){
		if(typeof page==undefined || page == null){
			page =1;
		}

		currentPage = page;
		var data = fcom.frmData(document.frmUserSearchPaging);
		moveToTop();
		fcom.ajax(fcom.makeUrl('merchants', 'sub-user-lists', [merchant_id,page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	
	search = function(form){
	
		fcom.ajax(fcom.makeUrl('merchants', 'sub-user-lists',[merchant_id]), fcom.frmData(form), function(json) {
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
	
	
	
	getForm = function(user_id){
		if(typeof user_id === undefined){
			user_id = 0;
		}
		
		fcom.ajax(fcom.makeUrl('merchants', 'subUserForm'), {"user_id":user_id,merchant_id:merchant_id}, function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$("#form-tab").html(json.msg);
					jsonSuccessMessage('Form Load');
					moveToTop();
					
				}else{
					jsonErrorMessage(json.msg);
				}
			});
		
	}
	
	getPasswordForm = function(user_id){
		if(typeof user_id === undefined){
			user_id = 0;
		}
		
		fcom.ajax(fcom.makeUrl('merchants', 'passwordForm'), {"user_id":user_id,merchant_id:merchant_id}, function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$("#form-tab").html(json.msg);
					jsonSuccessMessage('Form Load');
					moveToTop();
					
				}else{
					jsonErrorMessage(json.msg);
				}
			});
		
	}
	
	clearSearch = function(){
		$('.search-input').val('');
		$('#pretend_search_form input').val('');
		listing();
		$('#clearSearch').hide();
	}
	
	submitForm = function(v){
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
					listing(currentPage);
					
				}
				else{
					jsonErrorMessage(json.msg)
				}
			}
		}); 
		/* 
		v.validate();
		if (!v.isValid()){
			return false;
		}
		fcom.ajax($(action_form).attr('action'), fcom.frmData(action_form), function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					jsonSuccessMessage(json.msg);
					closeForm();
					listing(currentTab,currentPage);
				}else{
					jsonErrorMessage(json.msg);
				}
			}); */
		return false;

	}
	
	submitImage = function(user_id){
		$('#upload_image_'+user_id).ajaxSubmit({ 
			delegation: true,
			/* beforeSubmit:function(){
							v.validate();
							if (!v.isValid()){
								return false;
							}
						}, */
			success:function(json){
				json = $.parseJSON(json);
				
				if(json.status == "1"){
					$('#user_profile_photo_'+user_id).attr('src',$('#user_profile_photo_'+user_id).attr('src')+'?'+Math.random()) ;
					jsonSuccessMessage(json.msg)
				}
				else{
					jsonErrorMessage(json.msg)
				}
			}
		}); 
	 
	 }
	
/* 	showUserSearchPage = function(page) {
		searchUsers(document.frmUserSearchPaging, page);
	};
	
	reloadUserList = function() {
		searchUsers(document.frmUserSearchPaging, currentPage);
	}
	
	verifyUser = function(id, v) {
		fcom.updateWithAjax(fcom.makeUrl('users', 'verify'), {userId: id, v: v}, function(t) {
			reloadUserList();
		});
	};
	activateUser = function(id, v) {
		fcom.updateWithAjax(fcom.makeUrl('users', 'activate'), {userId: id, v: v}, function(t) {
			reloadUserList();
		});
	}; */
})();