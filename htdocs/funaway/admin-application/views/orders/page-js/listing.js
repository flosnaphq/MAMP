$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
	
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
			$('input[name=\'host_id\']').val(itemValue.item['value']);
			loadActivityValues(itemValue.item['value'],'activitiesId');
			return false;
		},
		focus: function(event, ui){
		  $(this).trigger('keydown.autocomplete');
		}
	});

	$('input[name=\'host_name\']').keyup(function(){
		$('input[name=\'host_id\']').val('');
		$('#activitiesId').find('option').remove().end().append('<option value="">Select A Host First</option>').val('');
	});		
	
});
(function() {
	var currentPage = 1;
	var currentTab = 1;

	
	listing = function(tab, page){
		if(typeof page==undefined || page == null){
			page =1;
		}
		if(typeof tab==undefined || tab == null){
			tab =1;
		}
		if(typeof user_id==undefined || user_id == null){
			user_id =0;
		}
		currentPage = page;
		currentTab = tab;
		var data = fcom.frmData(document.frmUserSearchPaging);
		moveToTop();
		fcom.ajax(fcom.makeUrl('orders', 'lists', [page,tab,user_id,order_type]), data, function(json) {
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
	

})();

/* Host Activity Search */

loadActivityValues = function (hostId, elemId) {

    var requesturl = fcom.makeUrl('activities', 'getHostActivities', [hostId]);
    fcom.ajax(requesturl, {}, function (json) {
        json = $.parseJSON(json);
        if ("1" == json.status) {
           fillSelectBox(elemId,json.msg);
        } else {
            jsonErrorMessage(json.msg);
        }
    });
    return false;
};

fillSelectBox = function(elemId, options){

    if (!$('#' + elemId)) {
        console.log("Object not found");
        return false;
    }

    if (!options instanceof Object) {
        return false;
    }
    $('#' + elemId).find('option[value!=""]').remove();

    $.each(options, function (index, value) {
        $('#' + elemId).append("<option value='" + index + "'>" + value + "</option>");
    });
    return  true;
}
