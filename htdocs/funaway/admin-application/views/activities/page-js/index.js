$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	
	/* Host Search */
	
	$('input[name=\'host_name\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: fcom.makeUrl('Users', 'autoComplete'),
				data: {keyword: request,fIsAjax:1},
				dataType: 'json',
				type: 'post',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item['name'],value: item['id']	
						};

					}));
				},
			});
		},
		'select': function(item,itemValue) {
			$('input[name=\'host_name\']').val(itemValue.item['label']);
			$('input[name=\'activity_user_id\']').val(itemValue.item['value']);
			return false;
		},
		focus: function(event, ui){
		  $(this).trigger('keydown.autocomplete');
		}
	});

	$('input[name=\'host_name\']').keyup(function(){
		$('input[name=\'activity_user_id\']').val('');
	});		
	
	
	listing();
});

listing = function(page){
	if(typeof page==undefined || page == null){
		page =1;
	}
	var data = fcom.frmData(document.frmUserSearchPaging);
	
	moveToTop();
	fcom.ajax(fcom.makeUrl('activities', 'listing', [page]), data, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.");
				
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

}

search = function(form){
	
	fcom.ajax(fcom.makeUrl('activities', 'listing'), fcom.frmData(form), function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				$('#clearSearch').show();
				jsonSuccessMessage("List Updated.")
				
			}else{
				jsonErrorMessage(json.msg)
			}
		});

}


changeConfirmStatus = function(msg, activity_id, status){
		
		confirmbox(msg,function(outcome){
				if(outcome){
					fcom.ajax(fcom.makeUrl('activities', 'changeConfirmStatus'), {activity_id:activity_id,status:status}, function(json) {
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
	
changeStatus = function(msg, activity_id, status){
		
		confirmbox(msg,function(outcome){
				if(outcome){
					fcom.ajax(fcom.makeUrl('activities', 'changeStatus'), {activity_id:activity_id,status:status}, function(json) {
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
	listing();
	$('#clearSearch').hide();
}

showTab = function(tab){
	if(tab == 1){
		$('#tab1').css('display','block');
		$('#tab2').css('display','none');
	}
	else if(tab == 2){
		$('#tab1').css('display','none');
		$('#tab2').css('display','block');
	}

}

changeHost = function(activity_id, v){
	if(typeof activity_id == undefined || activity_id == null || activity_id == 0){
		return false;
	}
	if(typeof v == undefined || v == null || v == 0){
		return false;
	}
	confirmbox('Do You Want To Change?',function(outcome){
		if(outcome){
			fcom.ajax(fcom.makeUrl('activities', 'changeHost'),{activity_id:activity_id,host_id:v},function(json){
				json = $.parseJSON(json);
				if(json.status == 1){
					jsonSuccessMessage(json.msg);
				}
				else{
					jsonErrorMessage(json.msg);
				}
			});
		}
		
	});
	
}


setPopular = function(el, activity_id){
	var status = 0;
		if($(el).hasClass('active')){
			status = 1;
		}
		$(el).toggleClass("active");
		fcom.ajax(fcom.makeUrl('activities', 'setPopular'), {"activity_id":activity_id, status : status}, function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					jsonSuccessMessage(json.msg);
				}else{
					$(el).toggleClass("active");
					jsonErrorMessage(json.msg);
				}
			});
}