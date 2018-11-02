

function showHtmlElementLoading(el){
	el.html('<img src="' + webroot + 'images/facebox/loading.gif">');
}

function setPage(page,frm){
	frm.elements['page'].value=page;
	//document.getElementById('page').value=page;
	frm.submit();
}

function limitTextCharacters(ele,maxLength) {
	maxLength = typeof maxLength == 'undefined' ? 500 : maxLength;
	
	var textlength = ele.value.length;
	var counter_id = $(ele).attr('name') + '_counter';
	/* var characters_left = (maxLength - textlength);
	
	if (characters_left < 0) {
		characters_left = 0;
	} */
		
	$('#'+counter_id).html(textlength);
	ele.value = ele.value.substring(0, maxLength);
	
	if (textlength == 0) {
		$('#'+counter_id).html(0);
	}
}

function nl2br(text) {
	return text.replace('/\n/gm', '<br/>');
}

function getFrmDataForUrl(frm,arr_ele) {
	var data = [];
	
	if (arr_ele === 'undefined') arr_ele = [];

	frm.each(function() {
		if ($(this).val() == '') return true;
		data.push($(this).attr('name'));
		data.push($(this).val());
	});
	
	/* Remove unwanted array items */
	if (arr_ele.length > 0) {
		$.each(arr_ele, function(key, value) {
			data.splice(data.indexOf(value),1);
		});
	}
	
	return data;
}




function alertbox(msg) {
   $.facebox('<div class="confirm-box"><div class="message">'+msg+'</div><div class="buttons"><button class="isYes" onclick="$.facebox.close();" id="alerts" value="1">OK</button></div></div>');
 }

function showbox(msg) {
   $.facebox('<div class="alert-box"><div class="message">'+msg+'</div></div>');
}
 

	
function confirmbox(msg, handler) {
	$.facebox('<div class="confirm-box"><div class="message">'+msg+'</div><div class="buttons"><button class="isYes" id="button1" value="1">Yes</button><button class="isYes" id="button2" name="button2" value="1">No</button></div></div>');
   
   $("#button1").on("click",function(evt){
		handler(true);
		$.facebox.close();
		
    });
  $("#button2").on("click",function(evt){
		 handler(false);
		$.facebox.close();
	   
    });
}

function confirmCommentBox(msg, handler) {
	$.facebox('<div class="confirm-box"><div class="message">'+msg+'</div><div class="buttons"><button class="isYes" id="button1" value="1">Yes</button><button class="isYes" id="button2" name="button2" value="1">No</button></div></div>');
   
   $("#button1").on("click",function(evt){
		handler(true);
		$.facebox.close();
		
    });
  $("#button2").on("click",function(evt){
		 handler(false);
		$.facebox.close();
	   
    });
}


function promptbox(type,msg, handler) {
	if(type=="textarea") text="<textarea style='width:100%;' name='textdata'></textarea>";
	if(type=="input-text") text="<input style='width:100%;' type='text' name='textdata' />";
	$.facebox('<div class="confirm-box"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="formTable siteForm"><tbody><tr><td><div class="message">'+msg+'</div></td></tr><tr><td>'+text+'</td></tr><tr><td>&nbsp;</td></tr><tr><td><div class="buttons"><button class="isYes" id="button1" value="1">Submit</button><button class="isYes" id="button2" name="button2" value="1">Cancel</button></div></tr></tbody></table></div>');
   
   $("#button1").on("click",function(evt){
		handler(true,$("textarea[name='textdata']").val());
		$.facebox.close();
		
    });
  $("#button2").on("click",function(evt){
		 handler(false);
		$.facebox.close();
	   
    });
}

function sendNotification(type){
	jQuery.ajax({
		type:"POST",
		url:generateUrl("notification","notify",[type]),
		success:function(json){
			json = $.parseJSON(json);
		}
	});
}


function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.readAsDataURL(input.files[0]);
		reader.onload = function (e) {
			$('#avtar-img')
				.attr('src', e.target.result)
				.width(230)
				.height(230);
		};
	}
}




function checkRadio(class_name){
	 if($("."+class_name).is(":checked")){
			 return true;
	}
	return false;
}


$(document).ready(function(){

	
	
	/* $(".state-tab").live("change",function(){
		state_id = $(this).val();
		if(typeof state_id === "undefined" || state_id == "") return;
		$.ajax({
			type:"POST",
			url:generateUrl("info","cities",[state_id],"/"),
			success:function(json){
					json =$.parseJSON(json);
					$(".city-tab").html(json.options);
			}
		});
	});	
	
	$(".city-tab").live("change",function(){
		city_id = $(this).val();
		if(typeof city_id === "undefined" || city_id == "") return;
		$.ajax({
			type:"POST",
			url:generateUrl("info","regions",[city_id],"/"),
			success:function(json){
					json =$.parseJSON(json);
					$(".region-tab").html(json.options);
			}
		});
		
	});	 */
	
	
	
	
	
	$(".closeMsg").click(function(){
		$(".system_message").remove();
	});
	
	
	$(document).click(function(){
		$(".system_message").remove();
	});
	
	/* function for left Navs */     
        $(".iconnavtoggle").click(function(){
            $(this).toggleClass("active");
            $(".sectionLeft").animate({width: 'toggle'});
        });
     /* function for flat Tabs */     
		$(".m_togglelink").click(function(){
			$(this).toggleClass("active");
			$('.tabflats').slideToggle("600");
			
		});  
	 /* function for  Keyword Search */     
        $(".linkcollapse").click(function(){
            $(this).toggleClass("active");
            $('.collapsewrap').slideToggle("600");
            
        }); 
	
});

function addComment(record_id,record_type){
	$.facebox({ajax:generateUrl("comments","comment",[record_id,record_type])});	
}
 
function updateComment(v){
	$('#frm_mbs_id_frmComment').ajaxSubmit({ 
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
				$('#frm_mbs_id_frmComment')[0].reset();
				$.facebox.close();
			}
		}
	});  
	 
}
	
function jsonErrorMessage(msg){
$.mbsmessage(msg);	
$('#mbsmessage').removeClass("alert alert_info");
$('#mbsmessage').removeClass("alert alert_success");
$('#mbsmessage').addClass("alert alert_danger");
}

function jsonSuccessMessage(msg){
$.mbsmessage(msg);	
$('#mbsmessage').removeClass("alert alert_info");
$('#mbsmessage').removeClass("alert alert_danger");
$('#mbsmessage').addClass("alert alert_success");
}

function jsonNotifyMessage(msg){
$.mbsmessage(msg);	
$('#mbsmessage').removeClass("alert alert_danger");
$('#mbsmessage').removeClass("alert alert_success");
$('#mbsmessage').addClass("alert alert_info");
}

function getComments(record_id,record_type){
	$.facebox({ajax:generateUrl("comments","comments",[record_id,record_type])});	
}

function moveToTop(){
	$("html, body").animate({ scrollTop: 0 }, "slow");
}

function closeForm(){
	$("#form-tab").html("");
}

function popupView(url){
	$.facebox({ajax:url});	
}




function closePopup(src){
	submitImageForm();
	$.facebox.close();
}



function getRegions(city_id,region_id){
	if(typeof city_id === "undefined" || city_id == "") return;
	$.ajax({
		type:"POST",
		url:fcom.makeUrl("info","regions",[city_id],"/"),
		success:function(json){
				json =$.parseJSON(json);
				$("#"+region_id).html(json.options);
		}
	});
}