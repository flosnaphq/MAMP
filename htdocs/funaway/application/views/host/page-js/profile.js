$(document).ready(function(){
	profileForm(1);
});
(function() {
	updateProfile = function(v,frm) {
		v.validate();
		if (!v.isValid()) return false;
		jsonStrictNotifyMessage();
		fcom.ajax(fcom.makeUrl('host', 'updateProfile'),fcom.frmData(frm), function(json) {
			json = $.parseJSON(json);
			if(json.status == '1'){
				jsonSuccessMessage(json.msg);
			}
			else{
				jsonErrorMessage(json.msg);
			}
		});
	};
	
	removeImage = function(v,frm) {
		confirmbox('Do You Want To Remove ?',function(outcome){
			if(outcome){
				jsonStrictNotifyMessage();
				fcom.ajax(fcom.makeUrl('host', 'removeImage'),{}, function(json) {
				json = $.parseJSON(json);
				if(json.status == '1'){
					$('#profile_photo').attr('src',json.src)
					jsonSuccessMessage(json.msg);
				}
				else{
					jsonErrorMessage(json.msg);
				}
			});
			}
		});
		
	};
	
	updatePassword = function(v,frm) {
		v.validate();
		if (!v.isValid()) return false;
		jsonStrictNotifyMessage();
		fcom.ajax(fcom.makeUrl('host', 'passwordSetup'),fcom.frmData(frm), function(json) {
			json = $.parseJSON(json);
			if(json.status == '1'){
				jsonSuccessMessage(json.msg);
				$(frm)[0].reset();
			}
			else{
				jsonErrorMessage(json.msg);
			}
		});
	};
	
	 bindImageUploader = function(){
		$('.modaal-ajax').modaal({
					type: 'ajax',
					after_open:function(){
						var settings = {
							  'aspectRatio': 1/1,
							  'url':fcom.makeUrl('croper','userProfileImage'),
							  'afterSaveCallback':function(){
								var $elem = $('.avatar').find('img');
								$('#profile_photo').attr('src',$('#profile_photo').attr('src')+"?"+new Date().getTime());
								$('.modaal-ajax').modaal('close');
								$elem.attr('src',$elem.attr('src')+"?"+new Date().getTime());
								
							  }
						  }
						  var croper =  new CropAvatar(settings);
					}
		});
		
	};
	
	profileForm = function(tab){
		if(typeof tab== undefined || tab==null){
			tab = 1
		}
		$('.list--vertical li a').removeClass('active');
		 
		$(".list--vertical li:nth-child("+tab+") a").addClass("active");
		jsonNotifyMessage('Loading...');
		fcom.ajax(fcom.makeUrl('host', 'step'),{'tab':tab}, function(json) {
			json = $.parseJSON(json); 
			if(json.status == '1'){
				jsonRemoveMessage();
				$('#form-wrapper').html(json.msg);
				$('.second-email').modaal();
				$('.phone-instruction').modaal();
				$('.introduce-yourself').modaal();
				if(tab == 2){
					bindImageUploader();
				}
				
			}
			else{
				jsonErrorMessage(json.msg);
			}
		});
	};
	

	sumbmitProfileImage = function(){ 
		jsonStrictNotifyMessage();
		$('#profile-img-form').ajaxSubmit({ 
		delegation: true,
		url:fcom.makeUrl('host','setupProfileImage'),
		
		success:function(json){
			json = $.parseJSON(json);
			if(json.status == "1"){
                                console.log(json);
				jsonSuccessMessage(json.msg);
				$('#profile_photo').attr('src',json.src)
				$('.inline').modaal('close');
			}
			else{
				
				jsonErrorMessage(json.msg);
				$('.inline').modaal('close');
			}
		}
	}); 
	
	}
	
	updateEmail = function(v,frm) {
		v.validate();
		if (!v.isValid()) return false;
		jsonStrictNotifyMessage();
		fcom.ajax(fcom.makeUrl('host', 'updateEmail'),fcom.frmData(frm), function(json) {
			json = $.parseJSON(json);
			if(json.status == '1'){
				jsonSuccessMessage(json.msg);
				$(frm)[0].reset();
			}
			else{
				jsonErrorMessage(json.msg);
			}
		});
	};
	
	showProfileLoader = function(){
		var loader = '<span>Loading...</span>';
		$('#form-wrapper').html(loader);
	}
})();