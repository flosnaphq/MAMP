$(document).ready(function(){
	listing();
});
(function() {
	var currentTab = 1;
	
	
	listing = function(tab){
		$('#new-add-on').hide();
		if(typeof tab==undefined || tab == null){
			tab =1;
		}
		$("#form-tab").html('');
		$('#new-review').hide();
		currentTab = tab;
		fcom.ajax(fcom.makeUrl('activities', 'tab', [activity_id,tab]), {}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				if(tab == 7 && typeof json.canEdit != undefined && json.canEdit == 1){
					$('#new-add-on').show();
				}
				if(tab == 8){
					$('#new-review').show();
				}
				$("#listing").html(json.msg);
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	
	addonImages = function(addon_id){
		$('#new-add-on').hide();
		fcom.ajax(fcom.makeUrl('activities', 'addonImages', [activity_id]), {addon_id:addon_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
			}else{
				jsonErrorMessage(json.msg);
			}
		});
	}
	
	removeAddonImage = function(image_id){
	if(confirm('Do You want to delete?')){
		
		fcom.ajax(fcom.makeUrl('activities','removeAddonImage'),{image_id:image_id},function(json){
			json =$.parseJSON(json);
			if(json.status == 1){
				jsonSuccessMessage(json.msg);
				addonImages(json.addon_id);
			}
			else{
				jsonErrorMessage(json.msg);
			}
		});
	}
	
	
}
	
	getAddOnForm = function(addon_id){
		if(typeof addon_id == 'undefined' || addon_id == 'null'){
			addon_id = 0;
		}
		jsonNotifyMessage();
		fcom.ajax(fcom.makeUrl('activities', 'getAddOnForm', [activity_id]), {addon_id:addon_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
			}else{
				jsonErrorMessage(json.msg);
			}
		});
		

	}
	getReviewForm = function(review_id){
		if(typeof review_id == 'undefined' || review_id == 'null'){
			review_id = 0;
		}
		jsonNotifyMessage();
		fcom.ajax(fcom.makeUrl('activities', 'reviewForm', [activity_id]), {review_id:review_id,activity_id:activity_id}, function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
			}else{
				jsonErrorMessage(json.msg);
			}
		});
	}
	
	changeFileStatus = function(afile_id, afile_approved){
		
		confirmbox('Do You Want To Change?',function(outcome){
				if(outcome){
					fcom.ajax(fcom.makeUrl('activities', 'changePhotoStatus'), {activity_id:activity_id,afile_id:afile_id,afile_approved:afile_approved}, function(json) {
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
	changeVideoStatus = function(video_id, status){
		
		confirmbox('Do You Want To Change?',function(outcome){
				if(outcome){
					fcom.ajax(fcom.makeUrl('activities', 'changeVideoStatus'), {activity_id:activity_id,video_id:video_id,status:status}, function(json) {
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
	
	getAbuseForm = function(abreport_id){
		if(typeof abreport_id == undefined || abreport_id == null){
			abreport_id = 0;
		}
		fcom.ajax(fcom.makeUrl('reviews', 'abuseform'),{abreport_id:abreport_id}, function(json) {
			json = $.parseJSON(json);
			
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				moveToTop();
			}else{
				moveToTop();
				jsonErrorMessage(json.msg);
			}
		});
	}
	
	removeFile = function(msg, afile_id){
		
		confirmbox(msg,function(outcome){
				if(outcome){
					fcom.ajax(fcom.makeUrl('activities', 'removeFile'), {activity_id:activity_id,afile_id:afile_id}, function(json) {
						json = $.parseJSON(json);
						if("1" == json.status){
							listing(currentTab);
							jsonSuccessMessage(json.msg);
						}else{
							jsonErrorMessage(json.msg);
						}
					});
				}
		});
	}
	
	defaultImage = function(el){
			if($(el).is(':checked')){
				fcom.ajax(fcom.makeUrl('activities', 'defaultImage'), {activity_id:activity_id,afile_id:$(el).val()}, function(json) {
					json = $.parseJSON(json);
					if("1" == json.status){
						listing(currentTab);
						jsonSuccessMessage(json.msg);
					}else{
						jsonErrorMessage(json.msg);
					}
				});
			}
			
		
		
	}
	
	deleteAddOn = function(msg, activity_id, addon_id){
		
		confirmbox(msg,function(outcome){
				if(outcome){
					fcom.ajax(fcom.makeUrl('activities', 'removeAddons'), {activity_id:activity_id,addon_id:addon_id}, function(json) {
						json = $.parseJSON(json);
						if("1" == json.status){
							listing(currentTab);
							jsonSuccessMessage(json.msg);
						}else{
							jsonErrorMessage(json.msg);
						}
					});
				}
		});
	}
	removeVideo = function(msg, activityvideo_id){
		
		confirmbox(msg,function(outcome){
				if(outcome){
					fcom.ajax(fcom.makeUrl('activities', 'removeVideo'), {activity_id:activity_id,activityvideo_id:activityvideo_id}, function(json) {
						json = $.parseJSON(json);
						if("1" == json.status){
							listing(currentTab);
							jsonSuccessMessage(json.msg);
						}else{
							jsonErrorMessage(json.msg);
						}
					});
				}
		});
	}
	
	
	tab = function(tab, el){
		$('.search-input').val('');
		var extra = arguments[2];
		if(extra == undefined){
			extra ='';
		}
		
		$('#tab-heading').html($(el).text()+extra);
              
		$('#pretend_search_form input').val('');
		$('#clearSearch').hide();
		listing(tab);
	}
	
		
	clearSearch = function(){
		$('.search-input').val('');
		$('#pretend_search_form input').val('');
		listing(currentTab,currentPage);
		$('#clearSearch').hide();
	}
	
	submitAddonImageForm = function(v, addon_id){
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
					jsonSuccessMessage(json.msg);
					addonImages(addon_id);
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
					listing(currentTab);
					$('#form-tab').html('');
					jsonSuccessMessage(json.msg);
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
	
	
	
	
	
	changeBooking = function(v){
		if(v == 100){
			$("#booking-day-field").parents('.booking_days_wrapper').show();
			
		}else{
			$("#booking-day-field").parents('.booking_days_wrapper').hide();
		}
		return;
	}
	changeDuration = function(v){
		if(v == 100){
			$("#duration-day-field").parents('.duration_days_wrapper').show();
		}else{
			$("#duration-day-field").parents('.duration_days_wrapper').hide();
		}
		return;
	}
	prevMonth = function(year,month){
	jsonNotifyMessage('Loading...');
	$.ajax({
		url:fcom.makeUrl('activities','availability',[activity_id]),
		type:'post',
		data:{'year':year,'month':month,'type':'prev'},
		success:function(json){
			
			json = $.parseJSON(json);
			if(json.status == 1){
				jsonRemoveMessage();
				$("#listing").html(json.msg);
				
					
			}else
				jsonErrorMessage(json.msg);
		}
	});
}

nextMonth = function(year,month){
	jsonNotifyMessage('Loading...');
	$.ajax({
		url:fcom.makeUrl('activities','availability',[activity_id]),
		type:'post',
		data:{'year':year,'month':month,'type':'next'},
		success:function(json){
			
			json = $.parseJSON(json);
			if(json.status == 1){
				jsonRemoveMessage();
				$("#listing").html(json.msg);
				
					
			}else
				jsonErrorMessage(json.msg);
		}
	});
}
onChangeTimeOption = function(){
	
	onChangeTime($(".serviceopt-type:checked").val());
	
}
onChangeTime = function(cVal){
	if(cVal == 1){
		$(".time-opt").hide();
	}else{
		$(".time-opt").show();
	}
}
addNewEvent = function(){
	serviceType = $(".service-type:checked").val();
	confrimType = $(".confirm-type:checked").val();
	$('#new-event').ajaxSubmit({ 
			delegation: true,
			url: fcom.makeUrl('activities', 'event-action',[activity_id]),
			success: function(json){
				json = $.parseJSON(json);
				$.facebox.close();
				if(json.status == "1"){
					jsonSuccessMessage(json.msg);
					currentMonth($('.current-yr').text(),$('.current-mon').text());
				}else{
					jsonErrorMessage(json.msg);
				}
			}
		}); 
}

currentMonth = function(year,month){
	
	jsonNotifyMessage('Loading...');
	$.ajax({
		url:fcom.makeUrl('activities','availability',[activity_id]),
		type:'post',
		data:{'year':year,'month':month,'type':'current'},
		success:function(json){
			
			json = $.parseJSON(json);
			
			if(json.status == 1){
				jsonRemoveMessage();
				$("#listing").html(json.msg);
			}else
				jsonErrorMessage(json.msg);
		}
	});
}

actionStep6= function(){
	
	
	$('#time-slot-form').ajaxSubmit({ 
			url: fcom.makeUrl('activities','setup6',[activity_id]),
			delegation: true,
			
			data:{'year':$('.current-yr').text(),'month':$('.current-mon').text()},
			success: function(json){
				
				json = $.parseJSON(json);
				if(json.status == "1"){
					jsonSuccessMessage(json.msg);
					currentMonth($('.current-yr').text(),$('.current-mon').text());
				}else{
					jsonErrorMessage(json.msg);
					

				}
			}
		}); 
		return false;
}

serviceChange = function(){
	 var val = $('input[name=service_type]:checked').val();
	if(val == 1){
		$('.time-slot-wrapper').hide();
		$('.time-slot-wrapper-add-more').hide();
	}
	else{
		$('.time-slot-wrapper').show();
		$('.time-slot-wrapper-add-more').show();
	}
	
}

addMoreTimeSlot = function(){
	$('.time-slot-wrapper').append($('.time-slot').html());
	if($('.time-slot-in').length > 1){
		$('#remove-slot').show();
	}
}

removeTimeSlot = function(){
	console.log($('.time-slot-in').length);
	if($('.time-slot-in').length > 1){
		$('.time-slot-wrapper .time-slot-in:last').remove();
	}
	 if($('.time-slot-in').length == 1){
		$('#remove-slot').hide();
	} 
	
}

entryOption = function(val){
	if(val == 1){
		$('#week-slot').hide();
	}
	else if(val == 2){
		$('#week-slot').show();
	}
	
}

replyToReview = function(review_id,reviewmsg_id){
    if (typeof review_id == undefined || review_id == null) {
        review_id = 0;
    }
    if (typeof reviewmsg_id == undefined || reviewmsg_id == null) {
        reviewmsg_id = 0;
    }
    fcom.ajax(fcom.makeUrl('reviews','replyToReviewForm'),{review_id:review_id, reviewmsg_id:reviewmsg_id},function(json){
        json = $.parseJSON(json);
        moveToTop();
        
        if(json.status == 1){
            jsonRemoveMessage();
            $('img.close_image:visible').click();
            $("#form-tab").html(json.msg);
            $('textarea[name=reviewmsg_message]').focus();
        }
        else{
            jsonErrorMessage(json.msg);
        }
        
    });
}

submitReplyToReview = function(v){
    
    $('#replyToReviewFrm').ajaxSubmit({ 
        delegation: true,
        beforeSubmit:function(){
                    v.validate();
                    if (!v.isValid()){
                        return false;
                    }
                },
        success: function(json){
            
            json = $.parseJSON(json);
            if(json.status == "1"){
                closeForm();
                jsonSuccessMessage(json.msg);
                listing(currentTab);
            }else{
                jsonErrorMessage(json.msg);
            }
        }
    });
}

})();

function showMap(lat,lang){
	L.mapbox.accessToken =mapbox_access_token;
	 map = L.mapbox.map('map', 'mapbox.streets')
		.setView([lat,lang],12);

	  layers = {
		  Streets: L.mapbox.tileLayer('mapbox.streets'),
		  Outdoors: L.mapbox.tileLayer('mapbox.outdoors'),
		  Satellite: L.mapbox.tileLayer('mapbox.satellite')
	  };

	  layers.Streets.addTo(map);
	  L.control.layers(layers).addTo(map);
	  marker = L.marker(new L.LatLng(lat,lang), {
		icon: L.mapbox.marker.icon({
			'marker-color': 'ff8888'
		}),
		draggable: true
	});
	marker.bindPopup('This marker is draggable! Move it around.');
	marker.addTo(map); 
	marker.on('dragend', ondragend);
	ondragend();
}

function ondragend() {
    m = marker.getLatLng();
	$('#act_lat').val(m.lat);
	$('#act_long').val(m.lng);
}

getSubService = function(obj){
	$.ajax({
		url:fcom.makeUrl('services','sub-service',[],'/'),
		type:'post',
		data:{'service_id':$(obj).val()},
		success:function(json){
			json = $.parseJSON(json);
			$('#subcat-list').html(json.msg);
		}
	});
}
cleareMonthRecord = function(){
	if(!confirm('Are You Sure?')){
		return;
	}
	$.ajax({
		url:fcom.makeUrl('activities','delete-all-event',[activity_id]),
		data:{'year':$('.current-yr').text(),'month':$('.current-mon').text()},
		type:'post',
		success:function(json){
			json = $.parseJSON(json);
			if(json.status == 1){
				jsonSuccessMessage(json.msg);
				currentMonth($('.current-yr').text(),$('.current-mon').text());
			}else{
				jsonErrorMessage(json.msg);
			}
		}
	});
}
deleteEvent = function(obj,eventId){
	if(!confirm('Are You Sure?')){
		return;
	}
	$.ajax({
		url:fcom.makeUrl('activities','delete-event',[activity_id]),
		data:{'event_id':eventId},
		type:'post',
		success:function(json){
			json = $.parseJSON(json);
			if(json.status == 1){
				jsonSuccessMessage(json.msg);
				currentMonth($('.current-yr').text(),$('.current-mon').text());
			}else{
				jsonErrorMessage(json.msg);
			}
		}
	});
};

selectAttr = function (el){
	var data_attr = $.parseJSON($(el).attr('data-attr'));
	
	if($(el).is(':checked')){
		$('#attr_file_wrapper_'+data_attr.attr_id).show();
	}
	else{
		$('#attr_file_wrapper_'+data_attr.attr_id).hide();
	}
	
}

