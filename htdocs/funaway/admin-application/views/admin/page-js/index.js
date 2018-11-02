$(document).ready(function(){
	$( document ).ajaxStart(function() {
		 jsonNotifyMessage("loading....")
	});
	listing();
})



listing = function(){
	moveToTop();
	jQuery.ajax({
		type:"POST",
		url:fcom.makeUrl("admin","lists"),
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#listing").html(json.msg);
				jsonSuccessMessage("List Updated.")
				loadSVGs("#listing");
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		}
	});
}

getForm = function(admin_id){
	if(typeof admin_id === undefined){
		admin_id = 0;
	}
	jQuery.ajax({
		type:"POST",
		url:fcom.makeUrl("admin","form"),
		data:{"admin_id":admin_id},
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
				jsonSuccessMessage("Add/Update Admin.")
				
			}else{
				jsonErrorMessage("something went wrong.")
			}
		}
	});
}
getForgotPasswordForm = function(admin_id){
	if(typeof admin_id === undefined){
		admin_id = 0;
	}
	jQuery.ajax({
		type:"POST",
		url:fcom.makeUrl("admin","forgotPasswordForm"),
		data:{"admin_id":admin_id},
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#form-tab").html(json.msg);
			}else{
				jsonErrorMessage(json.msg)
			}
		}
	});
}

searchForm = function(form){
	
	fcom.ajax(fcom.makeUrl('admin', 'lists'), fcom.frmData(form), function(json) {
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


clearSearch = function(){
		$('.search-input').val('');
		$('#pretend_search_form input').val('');
		listing();
		$('#clearSearch').hide();
	}
	

submitForm = function(v){
	$('#frm_fat_id_frmAdmin').ajaxSubmit({ 
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
				jsonSuccessMessage(json.msg)
				closeForm();
				listing();
			}else{
				jsonErrorMessage(json.msg);
			}
		}		
	}); 	
	return false;
}

var existUrl = {};
function loadSVGs(dv) {
	
	$('.svg', $(dv)).each(function() {
		var obj = $(this);
		var url = obj.attr('data-url');
		if(existUrl.hasOwnProperty(url)){
			if (existUrl[url] != '') {
				obj.html(existUrl[url]);
			}
			return;
		}
		existUrl[url] = '';
		$.ajax({
			cache:false,
			url: fcom.makeUrl('svg', 'index',[url]),
			success: function(t) {
				existUrl[url] = t;
				$('.svg[data-url="' + url + '"]', $(dv)).html(t);
			}
		})
	});
}

closeForm = function(){
	$('#form-tab').html('');
}


$(document).ready(function(){
loadSVGs(document.body);
	
});
