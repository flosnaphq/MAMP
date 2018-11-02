(function($){
	$.mbsmessage=function(data, autoclose, cls){
		$.mbsmessage.loading();
		$.mbsmessage.fillMbsmessage(data, cls);
		if(autoclose) $.mbsmessage.startTimer();
	};
	$.extend($.mbsmessage, {
		settings:{
			closeimage:siteConstants.webroot + 'images/close.gif',
			leftimage:siteConstants.webroot + 'images/left.png',
			rightimage:siteConstants.webroot + 'images/right.png',
			mbshtml: '\
				<div id="mbsmessage"> \
				 \
				<div class="content">Content</div> \
				<a class="close"></a>\
				</div> \
				'
			
		},
		loading: function(){
			initialize();
			$('#mbsmessage').show();
		},
		fillMbsmessage:function(data, cls){
			if(cls) $('#mbsmessage .content').addClass(cls);
			$('#mbsmessage .content').html(data);
			$('#mbsmessage').fadeIn();
			//$('#mbsmessage').css({top:10, left:10});
			if(isPopupView){
				isPopupView = false;
				jsonRemoveMessage();
			}
		},
		close:function(){
			
			$(document).trigger('close.mbsmessage');
		},
		startTimer:function(){
			if($.mbsmessage.timer) clearTimeout($.mbsmessage.timer);
			$.mbsmessage.timeInterval=12;
			$.mbsmessage.timer=setTimeout('$.mbsmessage.checkTimer()', 3000);
		},
		checkTimer:function(){
			if(!$.mbsmessage.timer) return;
			if(!$.mbsmessage.timeInterval) $.mbsmessage.timeInterval=3;
			$.mbsmessage.timeInterval-=3;
			if($.mbsmessage.timeInterval<=0){
				$(document).trigger('close.mbsmessage');
				clearTimeout($.mbsmessage.timer);
			}
			else{
				$.mbsmessage.timer=setTimeout('$.mbsmessage.checkTimer()', 3000);
			}
		}
	});
	
	$.fn.mbsmessage=function(settings){
		initialize(settings);
	};
	
	
	
	function initialize(settings){
		if($.mbsmessage.timer) clearTimeout($.mbsmessage.timer);
		if($.mbsmessage.settings.initialized) return true;
		$.mbsmessage.settings.initialized=true;
		$(document).trigger('initialize.mbsmessage');
		if(settings) $.extend($.mbsmessage.settings, settings);
		$('body').append($.mbsmessage.settings.mbshtml);
		var preload=[new Image(), new Image(), new Image()];
		preload[0].src=$.mbsmessage.settings.closeimage;
		preload[1].src=$.mbsmessage.settings.leftimage;
		preload[2].src=$.mbsmessage.settings.rightimage;
		$('#mbsmessage .left').html('<img src="' + $.mbsmessage.settings.leftimage + '" />');
		$('#mbsmessage .right').html('<img src="' + $.mbsmessage.settings.rightimage + '" />');
		$('#mbsmessage .close').click($.mbsmessage.close);
		$('#mbsmessage .close').attr({src:$.mbsmessage.settings.closeimage});
		
	}
	
	$(document).bind('close.mbsmessage', function() {
		if($.mbsmessage.timer) clearTimeout($.mbsmessage.timer);
	    $('#mbsmessage').fadeOut(function() {
	      $('#mbsmessage .content').removeClass().addClass('content');
	    });
	  });
	
})(jQuery);