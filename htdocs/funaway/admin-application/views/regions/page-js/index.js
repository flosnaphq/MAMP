(function() {
	var order = '';
	var canBeInactive = false;
	
	listing = function(page){
		if(typeof page==undefined || page == null){
			page =1;
		}
		
		var data = fcom.frmData(document.frmUserSearchPaging);
		
		moveToTop();
		fcom.ajax(fcom.makeUrl('regions', 'listing', [page]), data, function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$("#listing").html(json.msg);
					dndordering();
					jsonSuccessMessage("List Updated.")
					
				}else{
					jsonErrorMessage("something went wrong.")
				}
			});
			

	};


	getForm = function(region_id){
		if(typeof region_id === undefined){
			region_id = 0;
		}
		
		fcom.ajax(fcom.makeUrl('regions', 'form'), {"region_id":region_id}, function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$("#form-tab").html(json.msg);
					moveToTop();
					
				}else{
					jsonErrorMessage("something went wrong.")
				}
			});
		
	};

	search = function(form){
		
		fcom.ajax(fcom.makeUrl('regions', 'listing'), fcom.frmData(form), function(json) {
				json = $.parseJSON(json);
				if("1" == json.status){
					$("#listing").html(json.msg);
					$('#clearSearch').show();
					jsonSuccessMessage("List Updated.")
					
				}else{
					jsonErrorMessage("something went wrong.")
				}
			});

	};

	submitForm = function(v,form_id){
		var action_form = $('#'+form_id);
		

		v.validate();
		if (!v.isValid()){
			return false;
		}
		var regionId = parseInt($('#region_id').val());
		// alert(regionId);
		
		if(regionId == 0) {
			canBeInactive = true;
			updateRegion(action_form);
			return;
		}
		
		var regionStatus = parseInt($('#region_active').val());
		
		if(isNaN(regionStatus) || regionStatus == "undefined")
		{
			alert('Please select a valid value.');
			return;
		}
		if(regionStatus == 0) {
			canBeInactive = false;
			fcom.updateWithAjax(fcom.makeUrl('Regions', 'isActivityAssigned', [regionId]), '', function(json){
				if("1" == json.status){
					canBeInactive = true;
					updateRegion(action_form);
				}
				else if ("2" == json.status){
					if(confirm(json.msg)) {
						canBeInactive = true;
						updateRegion(action_form);
					}
				}
			});
			return;
		}
		canBeInactive = true;
		updateRegion(action_form);
	};
	
	updateRegion = function(action_form) 
	{
		if(false === canBeInactive) {
			alert('Unable to update record');
			return;
		}
		fcom.ajax($(action_form).attr('action'), fcom.frmData(action_form), function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg);
				closeForm();
				listing();
			}else{
				jsonErrorMessage(json.msg);
			}
		});
	}

	updateOrder = function(){
		$.mbsmessage('Please wait...');
		fcom.updateWithAjax(fcom.makeUrl('Regions', 'updateOrder', []), order, function(json){
			jsonSuccessMessage(json.msg);
			// listing();
		});
	};
	
	dndordering = function(){
		jQuery('#regions-list').tableDnD({
			onDrop: function(table, row) {
				order = jQuery.tableDnD.serialize('id');
				$.mbsmessage('<div onClick="updateOrder();" style="color:#fff;cursor: pointer;">Click Here to Update Order</p>');
			}
		}).mouseup(function(e){
			e && e.preventDefault();
			/*Added to check click on same row*/
			var droppedRow = jQuery.tableDnD.dragObject;
			if(!jQuery(droppedRow).hasClass('tDnD_whileDrag')){
				jQuery.tableDnD.dragObject   = null;
				jQuery.tableDnD.currentTable = null;
				return false;
			}
		});
	};
	
	clearSearch = function(){
		$('.search-input').val('');
		$('#pretend_search_form input').val('');
		listing();
		$('#clearSearch').hide();
	};
})();

function testFunc()
{
	alert();
}
$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
});