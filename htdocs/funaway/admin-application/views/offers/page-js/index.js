$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
});
(function() {


	listing = function(page){
		if(typeof page==undefined || page == null){
			page =1;
		}
		
	
		var data = fcom.frmData(document.frmUserSearchPaging);
		moveToTop();
		fcom.ajax(fcom.makeUrl('offers', 'lists', [page]), data, function(json) {
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
	
		fcom.ajax(fcom.makeUrl('offers', 'lists',[]), fcom.frmData(form), function(json) {
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
	
	
	
	getForm = function(discoupon_id){
		if(typeof discoupon_id === undefined){
			discoupon_id = 0;
		}
		
		fcom.ajax(fcom.makeUrl('offers', 'coupon-form'), {"discoupon_id":discoupon_id}, function(json) {
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
	
	weekdays = function (value){
		if(value == 1){
			$('#weekdays-wrapper').show();
			return;
		}
		$('#weekdays-wrapper').hide();
		
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
					closeForm();
					jsonSuccessMessage(json.msg);
					listing();
					
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
	
	changeCouponType = function(value){
		discoupon_type = value;
		showTab();
	}
	
	showTab = function(){
		$('#tabs_02_li').hide();
		$('#tabs_03_li').hide();
		$('#tabs_04_li').hide();
		if(discoupon_type == 1){
			$('#tabs_02_li').show();
		}
		else if(discoupon_type == 2){
			$('#tabs_03_li').show();
		}
		else if(discoupon_type == 3){
			$('#tabs_04_li').show();
		}
	}
	
})();