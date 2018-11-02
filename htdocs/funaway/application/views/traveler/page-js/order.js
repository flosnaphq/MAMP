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
		jsonNotifyMessage("loading....");
		var data = fcom.frmData(document.frmUserSearchPaging);
	//	moveToTop();
		fcom.ajax(fcom.makeUrl('traveler', 'order-listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonRemoveMessage();
				moveTo($("#listing"));
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	
	search = function(form,tab){
		jsonNotifyMessage("loading....");
		fcom.ajax(fcom.makeUrl('orders', 'lists',[1]), fcom.frmData(form), function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$("#listing").html(json.msg);
					$('#clearSearch').show();
					jsonRemoveMessage();
					
				}else{
					jsonErrorMessage("something went wrong.")
				}
			});

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
					//listing(currentTab,currentPage);
					
				}
				else{
					jsonErrorMessage(json.msg)
				}
			}
		}); 
	
		return false;

	}
	

})();