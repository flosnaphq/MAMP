	$(function() {
		if($('.js-faq').length){
			var $jsFaq = $('.js-faq');
				$jsFaq.find('.toggle').on('click', function(){
					if($(this).hasClass('has--close')){
						$jsFaq.find('.answer').slideUp();
						$jsFaq.find('.toggle').removeClass('has--close');
						return;
					}
					$jsFaq.find('.answer').slideUp();
					$jsFaq.find('.toggle').removeClass('has--close');
					
					$(this).addClass('has--close');
					$(this).parent().find('.answer').slideDown();
					
				});
				$jsFaq.find('.question').on('click', function(){
					if($(this).parent().children('.toggle').hasClass('has--close')){
						$jsFaq.find('.answer').slideUp();
						$jsFaq.find('.toggle').removeClass('has--close');
						return;
					}
					$jsFaq.find('.answer').slideUp();
					$jsFaq.find('.toggle').removeClass('has--close');
					
					$(this).parent().children('.toggle').addClass('has--close');
					$(this).parent().find('.answer').slideDown();
					
				});
		}
	});