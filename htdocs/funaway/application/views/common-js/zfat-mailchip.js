function showMailChimpPopUp() {
	var mc_closed_cookie = $.cookie("MCPopupClosed");
	
	if(mc_closed_cookie == 'yes') {
		$.removeCookie('MCPopupClosed', { path: '/' });
		/*$.cookie("MCPopupClosed", 1, {
			expires : -10,
			path    : '/',
		});*/
	}
	
	var mc_subscribed_cookie = $.cookie("MCPopupSubscribed");
	
	if(mc_subscribed_cookie == 'yes') {
		$.removeCookie('MCPopupSubscribed', { path: '/' });
	}
	
	if($(".mc-closeModal").length > 0) {
		$( ".mc-closeModal" ).each(function() {
			$( this ).parent().parent().remove();
		});
	}
	if($("#PopupSignupForm_0").length > 0) {
		$('.mc-closeModal').parent().parent().remove();
	}
	window.dojoRequire(["mojo/signup-forms/Loader"], function(L) { L.start({"baseUrl":"mc.us18.list-manage.com","uuid":"7245ec463873eda467b72ae20","lid":"82c31f96f4","uniqueMethods":true}) });
}

$(document).ready(function(){
	$(document).on('click', '.mc-closeModal', function(){
		$('.mc-closeModal').parent().parent().remove();
	});
	
	$('#open-MailChimp-popup').on('click', function(event) {
		event.preventDefault();
	});
});