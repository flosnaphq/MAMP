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
		jsonNotifyMessage("loading....")
		var data = fcom.frmData(document.frmUserSearchPaging);
	//	moveToTop();
		fcom.ajax(fcom.makeUrl('host', 'request-listing', [page]), data, function(json) {
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
	
		fcom.ajax(fcom.makeUrl('orders', 'lists',[1]), fcom.frmData(form), function(json) {
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
	

	
	getTransactionForm = function(order_id){
		if(typeof order_id === undefined){
			order_id = 0;
		}
		
		fcom.ajax(fcom.makeUrl('orders', 'transaction-form'), {"order_id":order_id}, function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$("#form-tab").html(json.msg);
					jsonSuccessMessage("Form Loaded.");
					moveToTop();
				}else{
					jsonErrorMessage(json.msg);
				}
			});
		
	}
	

	changeOrderStatus = function(msg,order_id, order_status){
		confirmCommentBox(msg, function(outcome){
			if(outcome){
				fcom.ajax(fcom.makeUrl('orders', 'changeOrderStatus'), {"order_id":order_id,order_status:order_status}, function(json) {
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

	
	
	
	clearSearch = function(){
		$('.search-input').val('');
		$('#pretend_search_form input').val('');
		listing(currentPage);
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
					//listing(currentTab,currentPage);
					
				}
				else{
					jsonErrorMessage(json.msg)
				}
			}
		}); 
	
		return false;

	}
	
	
	updateStatus = function(obj,request_id){
		obj = $(obj);
		var alertMsg = 'Are you sure to accept this booking?';
		if(obj.val() == 0) {
			alertMsg = 'Are you sure to change status of this request pending?';
		} else if(obj.val() == 2) {
			alertMsg = 'Are you sure to decline this request?';
		}
		if(!confirm(alertMsg)){
			obj.val(obj.attr('rel'));
			return false;
		}
		jsonStrictNotifyMessage('Processing..');
		updatedVal = obj.val();
		$.ajax({
			url:fcom.makeUrl('host', 'updateRequest'),
			type:"post",
			data:{'request_id':request_id,'request_status':updatedVal},
			success:function(json){
				json = $.parseJSON(json);
				if(json.status == 1){
					obj.attr('rel',updatedVal);
					jsonSuccessMessage(json.msg)
				}else{
					jsonErrorMessage(json.msg)
					obj.val(obj.attr('rel'));
				}
			}
			
		});	
		
	} 
})();