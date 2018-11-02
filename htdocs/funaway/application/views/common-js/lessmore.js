$(function(){
	readlessmore = function (elm, adjustheight, showChar, ellipsestext, moretext, lesstext){
		var adjustheight = adjustheight || 50;
		var elm = elm || '.more-less';
		var showChar = showChar || 150; 
		var ellipsestext = "&hellip;";
		var moreText = "Read more";
		var lessText = "Read less";
		
		$(elm).each(function(i,item) {
			if($(item).hasClass('fatmore')){
				return;
			}
			
			/* // var content = $('.more-block', $(item)).html();
			// alert($('.more-block', $(item)).height() + '(===)' + adjustheight);
			// if(content.length > showChar) { */
			if($('.more-block', $(item)).height() > adjustheight) {
				
				$(this).addClass('fatmore');
				$(".more-block", $(item)).css('height', adjustheight).css('overflow', 'hidden');

				$(item).append('<p class="continued">' + ellipsestext + '</p><a href="javascript:void(0);" onClick="showhide(this,' + adjustheight +',\'' + moreText + '\',\''+ lessText + '\'); return(false);" class="adjust text--primary">'+moreText+'</a>');
			}
		});
	};
	
	showhide = function (ths, adjustheight, moreText, lessText){
		if($(ths).hasClass('less')){
			$(ths).parents("div:first").find(".more-block").css('height', adjustheight).css('overflow', 'hidden');
			$(ths).parents("div:first").find("p.continued").css('display', 'block');
			$(ths).text(moreText).addClass('more').removeClass('less');
		}else{
			$(ths).parents("div:first").find(".more-block").css('height', 'auto').css('overflow', 'hidden');
			$(ths).parents("div:first").find("p.continued").css('display', 'none');
			$(ths).text(lessText).addClass('less').removeClass('more');
		}
		
		return false;
	}
});		
