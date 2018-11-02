var fatLazyImages = function(){
	var _ = this;
	_.settings = {
		effect: "fadein",
		data_attr_name: "lazysrc",
		imgCls: "",
		container: document.body,
		dim_attr_name: "lazydim",
		loaderCls: 'lazyloader',
		width: 200,
		height: 200,
		defImage: null,
		elementsQueue: [],
	};
};

fatLazyImages.prototype = {
	
	_init: function(){
		var _ = this;
		_.initLazyLoad();
	},
	
	initLazyLoad: function(){
		var _ = this;
		
		var el = _.settings.imgCls;
		$(_.settings.container).find(el).each(function(item, i){
			h = $(this);
			_.settings.elementsQueue.push(h)
			if((null !== _.settings.defImage) && ("" != _.settings.defImage)){
				h.attr("src", _.settings.defImage);
				if(h.hasClass(_.settings.loaderCls)){
					h.removeClass(_.settings.loaderCls)
				}
			}else{
				/* h.attr("src", "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=");  */
				h.attr("src", "/images/ajax-loader.gif");
				
				if(!h.hasClass(_.settings.loaderCls)){
					h.addClass(_.settings.loaderCls);
				}
			}
		});
		
		setTimeout(function(){
			_.fatLazyLoadImg();
		}, 1000);
		
	},
	
	checkQueue: function (){
		var _ = this;
		if (!_.settings.elementsQueue.length) {
            return;
        }
		return true;
	},
	
	getImgHeightWidth: function(el){
		var _ = this;
		var dim = null;
		/* ret = [_.settings.width, _.settings.height]; */
		ret = [];
		h.data(_.settings.dim_attr_name) && (dim = h.data(_.settings.dim_attr_name).split("_"));
		if((null !== dim) && ("" != dim[0])){
			ret[0] = dim[0];
		}
		
		if((null !== dim) && ("" != dim[1])){
			ret[1] = dim[1];
		}
		return ret;
	},
	
	on_scroll: function(){
		var _ = this;
		if(!_.checkQueue()){
			return;
		}
		_.fatLazyLoadImg();
	},
	
	fatLazyLoadImg: function(){
		var _ = this;
		
		var el = _.settings.imgCls;
		
		var windowHeight = $(window).height(),
        gridTop = (windowHeight * .1),
        gridBottom = (windowHeight * .9) + 500;
		$(_.settings.container).find(el).each(function(item, i){
			h = $(this);
			var noAttr = h.attr('data-'+_.settings.data_attr_name);
			if(typeof noAttr != 'undefined' && noAttr != '' && noAttr != null) { 
				var thisTop = h.offset().top - $(window).scrollTop();
				
				/* Check if this element is in the viewport */
					// alert(thisTop +'>='+ gridTop +'&&' + (thisTop + h.height()) +'<='+ gridBottom);
				if (thisTop <= 0 || (thisTop >= gridTop && (thisTop + h.height()) <= gridBottom)) {
					h.attr("src", h.data(_.settings.data_attr_name));
					switch (_.settings.effect) {
						case "fadein":
							h.fadeIn('slow');
							break;
						case "none":
							h.show();
							break;
						default:
							h.show()
					}
					dim = _.getImgHeightWidth(h);
					h.attr("width", dim[0]);
					h.attr("height", dim[1]);
					if(h.hasClass(_.settings.loaderCls)){
						h.removeClass(_.settings.loaderCls)
					}
					h.removeAttr('data-'+_.settings.data_attr_name);
					_.settings.elementsQueue.splice(_.settings.elementsQueue.indexOf(h),1);
				} else {
					return;
				}
			}
			return;
		});
	}
	
};

(function() {
	$.fn.FatLazyLoad = function(options){
		lazyObj = new fatLazyImages();
		if (options) {
			$.extend(lazyObj.settings, options);
		}
		lazyObj._init();
	};
	$(window).scroll(function(){
		lazyObj.on_scroll();
	});
})();

$(document).ready(function(){
	$(document).FatLazyLoad({
		imgCls: '.lazyimg',
	});
});


/*

https://github.com/fasterize/lazyload/blob/master/lazyload.js

addEventListener('touchmove', processScrollMobile);

processScrollMobile = function(){
    for(var i = 0; i < images.length; i++) {
        loadImage(images[i], function() {
            images.splice(i,i);
        });
    };
}

$("#loadingDiv").show();
var nImages = $("#all-images").length;
var loadCounter = 0;
//binds onload event listner to images
$("#all-images img").on("load", function() {
    loadCounter++;
    if(nImages == loadCounter) {
        $(this).parent().show();
        $("#loadingDiv").hide();
    }
}).each(function() {

    // attempt to defeat cases where load event does not fire
    // on cached images
    if(this.complete) $(this).trigger("load");
});

*/