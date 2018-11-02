$(document).ready(function(){
	
	listing();
});
(function() {
	var currentPage = 1;
	var currentTab = 1;

	
	listing = function(page){
		if(typeof page==undefined || page == null){
			page =1;
		}
		
		currentPage = page;
		
		var data = fcom.frmData(document.frmUserSearchPaging);
	//	
		jsonNotifyMessage("loading...");
		fcom.ajax(fcom.makeUrl('host', 'order-listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonRemoveMessage();
				moveTo($("#listing"));
				$('.js-order-cancel').modaal({
						type: 'ajax'
				});
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	
	submitSearch = function(){
		jsonNotifyMessage("loading...");
		var form = $('#ordSearchFrm');
		fcom.ajax(fcom.makeUrl('host', 'order-listing',[1]), fcom.frmData(form), function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					jsonRemoveMessage();
					$("#listing").html(json.msg);
					$('.js-order-cancel').modaal({
							type: 'ajax'
					});
					moveTo($("#listing"));
				//	$('#clearSearch').show();
					
				}else{
					jsonErrorMessage(json.msg);
				}
			});

	}
	

	getDetails = function(v){
		$( "select[name='booking_type']" ).val(v);
		submitSearch();
	}
	
	
	clearSearch = function(){
		$('.search-input').val('');
		$('#pretend_search_form input').val('');
		listing(currentPage);
		$('#clearSearch').hide();
	}
	
	/* submitForm = function(v){
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
					//listing(currentTab,currentPage);
					
				}
				else{
					jsonErrorMessage(json.msg)
				}
			}
		}); 
	
		return false;

	} */
	
	submitCancelForm = function(v, frm){
		
		$(frm).ajaxSubmit({ 
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
					$('.js-order-cancel').modaal('close');
					jsonSuccessMessage(json.msg);
					listing(currentPage);
					
				}
				else{
					$('.js-order-cancel').modaal('close');
					jsonErrorMessage(json.msg)
				}
			}
		}); 
	
		return false;

	}
	

})();