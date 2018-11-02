(function ($) {
    
    //alert($(window).width());
    
    nicescrollbar = function () {
        
        /*$('.scrollable--x').enscroll({
            showOnHover: true,
            verticalScrolling: false,
            horizontalScrolling: true,
            easingDuration: 100,
            minScrollbarLength: 50
        });
        $('.scrollable--y').enscroll({
            showOnHover: true,
            verticalScrolling: true,
            horizontalScrolling: false,
            easingDuration: 100,
            minScrollbarLength: 50
        });*/

    }
})(jQuery);

$(document).ready(function () {
    $('.js-tab').jsTab();
    $('.js-main-menu > ul').clone().appendTo('.js-mobile-menu');
    addOn();
    // $('.scrollable--x').enscroll({
    // showOnHover: true,
    // verticalScrolling: false,
    // horizontalScrolling: true,
    // easingDuration: 100,
    // minScrollbarLength: 50
    // });
    // $('.scrollable--y').enscroll({
    // showOnHover: true,
    // verticalScrolling: true,
    // horizontalScrolling: false,
    // easingDuration: 100,
    // minScrollbarLength: 50
    // });



});

/* Form Add On */
function addOn() {
    $('.field_add-on, .form-element__add-on').each(function () {
        var wAddOn = $(this).outerWidth();
        if ($(this).hasClass('add-on--left'))
            $(this).siblings('input').css({'paddingLeft': (wAddOn + 20)});


        if ($(this).hasClass('add-on--right'))
            $(this).siblings('input').css({'paddingRight': (wAddOn + 20)});

        if ($(this).siblings('label').length) {
            if ($(this).hasClass('add-on--left'))
                $(this).siblings('label').css({'left': (wAddOn + 20)});


            if ($(this).hasClass('add-on--right'))
                $(this).siblings('label').css({'right': (wAddOn + 20)});
        }

    });
}



$(document).ajaxComplete(function () {
    if ($(window).width() > 1279) {
        addOn();
    }
    $('.share-ajax').modaal({
        type: 'ajax',
    });
});



(function ($) {

    "use strict";

    $(function () {

        var $window = $(window),
                $body = $('body');

        /* Disable animations/transitions until the page has loaded. */
        $body.addClass('is--loading');

        $window.on('load', function () {
            window.setTimeout(function () {
                $body.removeClass('is--loading');
            }, 100);
        });

        /* Form Add On */
        $('.field_add-on').each(function () {
            var wAddOn = $(this).outerWidth();
            if ($(this).hasClass('add-on--left'))
                $(this).siblings('input').css({'paddingLeft': (wAddOn + 20)});

            if ($(this).hasClass('add-on--right'))
                $(this).siblings('input').css({'paddingRight': (wAddOn + 20)});
        });


        /* Blank Input */
        var _emptyTextBoxes = $('input').filter(function () {
            return this.value == "";
        });

        _emptyTextBoxes.each(function () {
            $(this).addClass('empty');
            $(this).change(function () {
                if ($(this).val() != "" || $(this).val() != null && $(this).hasClass('empty')) {
                    $(this).removeClass('empty');
                } else {
                    $(this).addClass('empty');
                }
            });
        });


        /* Scroll top */
        var _isTop = function () {
            if ($window.scrollTop() > 0)
                $body.removeClass('is--top').addClass('is--bottom');
            else
                $body.removeClass('is--bottom').addClass('is--top');
        };
        _isTop();
        $window.on('scroll', function () {
            _isTop();
        });

        /* Parallax */
        $('.js-img-parallax').each(function () {
            var img = $(this);
            var imgParent = $(this).parent();
            function parallaxImg() {
                var speed = img.data('speed');
                var imgY = imgParent.offset().top;
                var winY = $(this).scrollTop();
                var winH = $(this).height();
                var parentH = imgParent.innerHeight();


                // The next pixel to show on screen      
                var winBottom = winY + winH;

                // If block is shown on screen
                if (winBottom > imgY && winY < imgY + parentH) {
                    // Number of pixels shown after block appear
                    var imgBottom = ((winBottom - imgY) * speed);
                    // Max number of pixels until block disappear
                    var imgTop = winH + parentH;
                    // Porcentage between start showing until disappearing
                    var imgPercent = ((imgBottom / imgTop) * 100) + (50 - (speed * 50));
                }
                img.css({
                    top: imgPercent + '%',
                    transform: 'translate(-50%, -' + imgPercent + '%)'
                });
            }
            if ($(window).width() > 1279) {
                $(window).load(parallaxImg).scroll(parallaxImg);
            }
        });

        /* Tablet Menu */
        var $tabMenu = $('.js-main-menu');
        $tabMenu._closed = true;
        if ($window.width() < 1025 && $window.width() > 767 && $tabMenu._closed)
            $tabMenu.find('.sub-menu').each(function () {
                $(this).on('click', function () {
                    $tabMenu._closed = false;
                    $(this).children('ul').show(function () {
                        $(this).siblings('a').css({'z-index': 3000});

                        if (!$tabMenu._closed)
                            $("html").bind("click", function () {
                                $tabMenu.find('.sub-menu > ul').hide();
                                $tabMenu.find('.sub-menu > a').css({'z-index': 0});
                                $tabMenu._closed = true;
                                $("html").unbind("click");
                            });
                    });
                });
            });

        /* Menu */
        var $menu = $('.js-mobile-menu'), /* #MOBILE_MENU */
                $menuToggle = $('.js-menu-toggle, .js-menu-close'), /* #MENU_TOGGLE, #MENU_CLOSE */
                $overlay = $('.js-overlay'); /* #OVERLAY */
        $menu._opened = false;

        $menu._open = function () {

            if ($menu._opened) {

                $menu._opened = false;

                return false;
            }

            $menu._opened = true;

            return true;

        };

        $menu._show = function () {

            $body.addClass('is--menu-visible');
            $overlay.addClass('has--visible');

        };

        $menu._hide = function () {

            $body.removeClass('is--menu-visible');
            $overlay.removeClass('has--visible');

        };

        $menuToggle.on('click', function (event) {

            if ($menu._open())
                $menu._show();
            else
                $menu._hide();

            $(this).addClass('has--opened');

        });
        $overlay.on('click', function (event) {

            if (!$menu._open())
                $menu._hide();

            $menuToggle.removeClass('has--opened');
        });

        $menu.find('li').has('ul').addClass('has--menu');

        $menu.find('.has--menu > a').each(function () {
            $(this).on('click', function (event) {
                event.stopPropagation();
                event.preventDefault();
                event.stopImmediatePropagation();

                $('.has--menu > a').not(this).next().hide();
                $(this).next().show();
            });
        });

        // Sticky TODO
      
        if ($('.js-sticky').length) {
            var $sticky = $('.js-sticky');
            $sticky.each(function () {
                var _stickyConfig, _stickyOffset = $(this).data("sticky-offset"),
                        _stickyResponsive = $(this).data("sticky-responsive"),
                        _stickyParent = $(this).data("sticky-parent");

                if (typeof _stickyParent != 'undefined') {
                    _stickyConfig = {
                        offset_top: parseInt(_stickyOffset),
                        parent: _stickyParent,
                        sticky_class: "is--stuck",
                        recalc_every: 1
                    }
                } else {                   
                    _stickyConfig = {
                        offset_top: parseInt(_stickyOffset),
                        sticky_class: "is--stuck",
                        recalc_every: 1
                    }
                }
                
                $(this).stick_in_parent(_stickyConfig)
                    .on("sticky_kit:bottom", function(e) {
                        $(this).addClass("has--bottom");
                      })
                      .on("sticky_kit:stick", function(e) {
                        $(this).removeClass("has--bottom");
                      });

                if ($window.width() <= 1024 && !_stickyResponsive)
                    $(this).trigger("sticky_kit:detach");

                if ($window.width() < 767)
                    $(this).trigger("sticky_kit:detach");
            });
        }
        
        /* Main Carousel */
        if ($('.js-main-carousel').length) {
            var $mainCarousel = $('.js-main-carousel');
            $mainCarousel.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                arrows: false,
                dots: false,
                infinite: true,
                pauseOnHover: false,
                speed: 1000,
                fade: true,
                cssEase: 'linear'
            });
        }


        /* Common Carousel */
        var $carousel = $('.js-carousel');
        $carousel.each(function () {

            var _slidesToShow = $(this).data("slides"),
                    _slidesNext = $(this).data("next"),
                    _slidesPrev = $(this).data("prev"),
                    _slidesArrow = $(this).data("arrows"),
                    _slidesToShowArr = _slidesToShow.toString().split(','),
                    _slidesToShowD, _slidesToShowT, _slidesToShowM, _isSlidesArrow;

            if (_slidesToShowArr.length > 0) {
                _slidesToShowD = _slidesToShowArr[0];
            } else {
                _slidesToShowD = '3';
            }

            if (_slidesToShowArr.length > 1) {
                _slidesToShowT = _slidesToShowArr[1];
            } else {
                _slidesToShowT = '2';
            }

            if (_slidesToShowArr.length > 2) {
                _slidesToShowM = _slidesToShowArr[2];
            } else {
                _slidesToShowM = '1';
            }

            if (_slidesArrow != undefined) {
                _slidesArrow = parseInt(_slidesArrow);
            } else {
                _slidesArrow = true;
            }


            //slick common carousel init
            $(this).slick({
                slidesToShow: parseInt(_slidesToShowD),
                slidesToScroll: 1,
                arrows: _slidesArrow,
                dots: true,
                infinite: true,
                autoplay: true,
                pauseOnHover: true,
                responsive: [
                    {
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: parseInt(_slidesToShowT)
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: parseInt(_slidesToShowM)
                        }
                    }
                ]
            });
            //Slick NEXT PREV
            if (_slidesNext != undefined) {
                $(_slidesNext).on('click', function () {
                    $carousel.slick('slickNext');
                });
            }

            if (_slidesPrev != undefined) {
                $(_slidesPrev).on('click', function () {
                    $carousel.slick('slickPrev');
                });
            }
        });



        /* Smooth Scroll */
        $(function () {
			/*
            $('a[href*="#"]:not([href="#"])').click(function () {

                if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                    var target = $(this.hash),
                            targetOffset = $(this).data("offset");
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

                    if (target.length && $window.width() >= 768) {
                        $('html, body').animate({
                            scrollTop: target.offset().top - parseInt(targetOffset)
                        }, 1000);
                        return false;
                    }

                }
            });
			*/
        });

        /* Notification bar */
        /* Checks to see if it is the first visit on browser open */
        if (localStorage.getItem('firstVisit') !== 'true') {
            // Stores visit
            localStorage.setItem('firstVisit', 'true');
            $('.site-header-nofication').show();
            $body.addClass("has--notification");
        } else {
            $('.site-header-nofication').remove();
            $body.removeClass("has--notification");
        }

        $(".site-header-nofication .close-js").click(function () {
            $(this).parent()["hide"]();
            $body.removeClass("has--notification");
        });

    });




    /* Tab */
    $.jsTab = function (el, options) {

        var _base = this;
        _base.$el = $(el);
        _base.$nav = _base.$el.find("nav");

        _base.init = function () {

            _base.options = $.extend({}, $.jsTab.defaultOptions, options);

            /* Accessible hiding fix */
            $(".hide").css({
                "position": "relative",
                "top": 0,
                "left": 0,
                "display": "none"
            });

            _base.$nav.find("li > a").on("click", function () {



                /* $('.js-carousel').slick(); */
                /* Figure out current tab */
                var curEl = _base.$el.find("a.current").attr("href").substring(1),
                        /* Figure out new tab */
                        $newEl = $(this),
                        /* Figure out ID of new tab */
                        tabID = $newEl.attr("href").substring(1);

                if ((tabID != curEl) && (_base.$el.find(":animated").length == 0)) {

                    /* Fade out current tab */
                    _base.$el.find("#" + curEl).fadeOut(_base.options.speed, function () {

                        /* Fade in new tab on callback */
                        _base.$el.find("#" + tabID).fadeIn(_base.options.speed);

                        /* Remove highlighting - Add to just-clicked tab */
                        _base.$el.find("nav li a").removeClass("current");
                        $newEl.addClass("current");

                    });

                }

                return false;
            });

        };
        _base.init();
    };

    $.jsTab.defaultOptions = {
        "speed": 100
    };

    $.fn.jsTab = function (options) {
        return this.each(function () {
            (new $.jsTab(this, options));
        });
    };


    /* Modaal */
    if (typeof $('.js-video-modaal') == 'defined') {
        $('.js-video-modaal').modaal({type: 'video'});
    }




})($);

