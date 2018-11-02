$(window).load(function(){
	listing();
});

listing = function(page){
	jsonNotifyMessage('Loading...');
	if(typeof page === 'undefined'){
		page = 1;
	}
	var confirm_status = $('#confirm_status').val();
	var status = $('#status').val();
	$.ajax({
		url:fcom.makeUrl("hostactivity","listing"),
		data: {"page":page,'status':status, 'confirm_status':confirm_status},
		type: "post",
		success:function(json){
			jsonRemoveMessage();
			json = $.parseJSON(json);
			if(1 == json.status){
				$(".activity-list").html(json.msg);
				moveTo(".activity-list");
				$('.modaal-ajax').modaal({
						type: 'ajax'
				});
			}else{
				$(".activity-list").html(json.msg);
			}
		}
		
	});
}

/*//////////////////// twitter ////////*/
(function() {
  if (window.__twitterIntentHandler) return;
  var intentRegex = /twitter\.com\/intent\/(\w+)/,
      windowOptions = 'scrollbars=yes,resizable=yes,toolbar=no,location=yes',
      width = 550,
      height = 420,
      winHeight = screen.height,
      winWidth = screen.width;
 
  function handleIntent(e) {
    e = e || window.event;
    var target = e.target || e.srcElement,
        m, left, top;
 
    while (target && target.nodeName.toLowerCase() !== 'a') {
      target = target.parentNode;
    }
 
    if (target && target.nodeName.toLowerCase() === 'a' && target.href) {
      m = target.href.match(intentRegex);
      if (m) {
        left = Math.round((winWidth / 2) - (width / 2));
        top = 0;
 
        if (winHeight > height) {
          top = Math.round((winHeight / 2) - (height / 2));
        }
 
        window.open(target.href, 'intent', windowOptions + ',width=' + width +
                                           ',height=' + height + ',left=' + left + ',top=' + top);
        e.returnValue = false;
        e.preventDefault && e.preventDefault();
      }
    }
  }
 
  if (document.addEventListener) {
    document.addEventListener('click', handleIntent, false);
  } else if (document.attachEvent) {
    document.attachEvent('onclick', handleIntent);
  }
  window.__twitterIntentHandler = true;
}());

///////////////////////// pintrest /////////////////////////////////


function pinit(url,media,desc) {
    window.open("//www.pinterest.com/pin/create/button/"+
    "?url="+url+
    "&media="+media+
    "&description="+desc,"_blank","width=500,height=500");
    return false;
}  

var twitterWindow;
function twitterShare(url){
	twitterWindow = window.open(url,"_blank","width=500,height=500");
    return false;
} 

function twitterwindowclose(){
	twitterWindow.close();
}

