$(document).ready(function(){
    
      /* for sliding effect */  
    if($(window).width()>1000){
    $('#moveleft').click(function() {
        $('.panels').animate({
        'marginLeft' : "0" //moves left
        });
        
        $('.innerpanel').animate({
        'marginLeft' : "100%" //moves right
        });
    });
	}
    if($(window).width()>1000){
    $('#moveright').click(function() {
        $('.panels').animate({
        'marginLeft' : "50%" //moves right
        });
        
        $('.innerpanel').animate({
        'marginLeft' : "0" //moves right
        });
    });
     }
     
    /* for mobile view slide */  
     if($(window).width()<1000){
		$('.linkslide').click(function() {
            $(this).toggleClass("active");
			var el = $("body");
			if(el.hasClass('active-left')) el.removeClass("active-left");
			else el.addClass('active-left');
          
        }); 
    }
      /* for forms elements */         
      function floatLabel(inputType){
           $(inputType).each(function(){
           var $this = $(this);
           var text_value = $(this).val();

            // on focus add class "active" to label
            $this.focus(function(){

            $this.closest('.field_control').addClass("active");
            });

            // on blur check field and remove class if needed
            $this.blur(function(){
            if($this.val() === '' || $this.val() === 'blank'){
            $this.closest('.field_control').removeClass('active');
            }
            });

            // Check input values on postback and add class "active" if value exists
            if(text_value!=''){
            $this.closest('.field_control').addClass("active");
            }

            // Automatically remove floatLabel class from select input on load
              /* $('select').closest('.field_control').removeClass('active');*/
            });

            }
            // Add a class of "floatLabel" to the input field
            floatLabel(".web_form input[type='text'], .web_form input[type='password'], .web_form input[type='email'], .web_form select, .web_form textarea, .web_form input[type='file']"); 
     
     

     /* wave ripple effect */ 
        var parent, ink, d, x, y;
        $(".themebtn, .leftmenu > li > a, .actions > li > a, .leftlinks > li > a,.profilecover .profileinfo,.pagination li a, .circlebutton").click(function(e){
            parent = $(this);
            //create .ink element if it doesn't exist
            if(parent.find(".ink").length == 0)
                parent.prepend("<span class='ink'></span>");

            ink = parent.find(".ink");
            //incase of quick double clicks stop the previous animation
            ink.removeClass("animate");

            //set size of .ink
            if(!ink.height() && !ink.width())
            {
                //use parent's width or height whichever is larger for the diameter to make a circle which can cover the entire element.
                d = Math.max(parent.outerWidth(), parent.outerHeight());
                ink.css({height: d, width: d});
            }

            //get click coordinates
            //logic = click coordinates relative to page - parent's position relative to page - half of self height/width to make it controllable from the center;
            x = e.pageX - parent.offset().left - ink.width()/2;
            y = e.pageY - parent.offset().top - ink.height()/2;

            //set the position and add class .animate
            ink.css({top: y+'px', left: x+'px'}).addClass("animate");
        });
});

	function login(v){
		$('#frm_fat_id_frmLogin').ajaxSubmit({ 
			delegation: true,
			beforeSubmit:function(){
						v.validate();
						if (!v.isValid()){
							$(".login").addClass("error");
							return false;
						} 
					},
			success: function(json){
				json = $.parseJSON(json);
				if(json.status == "1"){
					jsonSuccessMessage(json.msg);
					location.reload();
				}else{
					jsonErrorMessage(json.msg);
				}
			}
		}); 
		
		$( document ).ajaxStart(function() {
			jsonNotifyMessage('Processing....');	
		}); 
	}


	function forgot(s){
			
		$('#frm_fat_id_frmForgotPassword').ajaxSubmit({ 
			delegation: true,
			beforeSubmit:function(){
						s.validate();
						if (!s.isValid()){
							$(".forgot").addClass("error");
							return false;
						} 
					},
			success: function(json){
				location.reload();
			} 
		}); 
	}	
forgotPassword = function(v, form){
	
	v.validate();
	if (!v.isValid()){
		return false;
	}
	
 	$.ajax({
	   type: "POST",
	   url: $(form).attr('action'),
	   data: $(form).serialize(),
	   success: function(json) {
			json = $.parseJSON(json);
			
			if("1" == json.status){
				  $(form).find("input[type=text], textarea").val("");
				jsonSuccessMessage(json.msg);
				
			}else{
				jsonErrorMessage(json.msg);
				refreshCaptcha();
			}
	   }
	 }); 
	
	/* $.ajax($(form).attr('action'), $(form).serialize(), function(json) {
			json = $.parseJSON(json);
			if("1" == json.status){
				jsonSuccessMessage(json.msg);
				
			}else{
				jsonErrorMessage(json.msg);
			}
		}); */
	return false;

	
}

function refreshCaptcha(){
	document.getElementById('image').src = '/admin/info/captcha?sid=' + Math.random(); return false
}