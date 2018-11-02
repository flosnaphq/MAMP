$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
});
(function() {
	var currentPage = 1;
	var currentTab = 1;
	/* searchUsers = function(frm, page) {
		if (!page) {
			page = currentPage;
		}
		currentPage = page;
		var dv = $('#user-list');
		var data = fcom.frmData(frm);
		dv.html('Loading...');
		var pagesize = 10; 
		fcom.ajax(fcom.makeUrl('users', 'search', [page, pagesize]), data, function(t) {
			dv.html(t);
		});
	}; */
	
	listing = function(tab, page){
		if(typeof page==undefined || page == null){
			page =1;
		}
		if(typeof tab==undefined || tab == null){
			tab =1;
		}
		currentPage = page;
		currentTab = tab;
		var data = fcom.frmData(document.frmUserSearchPaging);
		moveToTop();
		fcom.ajax(fcom.makeUrl('merchants', 'lists', [page,tab]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	
	search = function(form,tab){
	
		fcom.ajax(fcom.makeUrl('merchants', 'lists',[1,tab]), fcom.frmData(form), function(json) {
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
	
	tab = function(tab){
		$('.search-input').val('');
		$('#pretend_search_form input').val('');
		$('#clearSearch').hide();
		closeForm();
		listing(tab);
	}
	
	getForm = function(user_id){
		if(typeof user_id === undefined){
			user_id = 0;
		}
		
		fcom.ajax(fcom.makeUrl('merchants', 'form'), {"user_id":user_id}, function(json) {
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
		listing(currentTab,currentPage);
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
					listing(currentTab,currentPage);
					
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
	 
	 getPasswordForm = function(user_id){
		if(typeof user_id === undefined){
			user_id = 0;
		}
		
		fcom.ajax(fcom.makeUrl('merchants', 'passwordForm'), {"user_id":user_id}, function(json) {
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