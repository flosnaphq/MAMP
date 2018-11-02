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

search = function(form){
	
	fcom.ajax(fcom.makeUrl('reports', 'listing'), fcom.frmData(form), function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.");
				
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

closeForm = function(){
	$("#form-tab").html('');
}

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
