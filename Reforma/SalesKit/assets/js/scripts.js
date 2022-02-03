
(function($) {
    "use strict";

    /*================================
    Preloader
    ==================================*/

    var preloader = $('#preloader');
    $(window).on('load', function() {
        setTimeout(function() {
            preloader.fadeOut('slow', function() { $(this).remove(); });
        }, 300)
    });

    /*================================
    sidebar collapsing
    ==================================*/
    if (window.innerWidth <= 1364) {
        $('.page-container').addClass('sbar_collapsed');
    }
    $('.nav-btn').on('click', function() {
        $('.page-container').toggleClass('sbar_collapsed');
    });

    /*================================
    Start Footer resizer
    ==================================*/
    var e = function() {
        var e = (window.innerHeight > 0 ? window.innerHeight : this.screen.height) - 5;
        (e -= 88) < 1 && (e = 1), e > 88 && $(".main-content").css("min-height", e + "px")
    };
    $(window).ready(e), $(window).on("resize", e);
    /*================================
    sidebar menu
    ==================================*/
    $("#menu").metisMenu();

    /*================================
    slimscroll activation
    ==================================*/
    $('.menu-inner').slimScroll({
        height: 'auto'
    });

    /*================================
    stickey Header
    ==================================*/
    $(window).on('scroll', function() {
        var scroll = $(window).scrollTop(),
            mainHeader = $('#sticky-header'),
            mainHeaderHeight = mainHeader.innerHeight();

        // console.log(mainHeader.innerHeight());
        if (scroll > 1) {
            $("#sticky-header").addClass("sticky-menu");
        } else {
            $("#sticky-header").removeClass("sticky-menu");
        }
    });

   
    /*================================
    Slicknav mobile menu
    ==================================*/
    $('ul#nav_menu').slicknav({
        prependTo: "#mobile_menu"
    });

    var previousOrientation = window.orientation;
    var checkOrientation = function(){
        if(window.orientation !== previousOrientation){
            previousOrientation = window.orientation;
            // orientation changed, do your magic here
            $('.menu-inner').slimScroll({
                height: ($( window ).height() - 120)
            });
        }
        else {
            $('.menu-inner').slimScroll({
                height: 'auto'
            });
        }
    };

    window.addEventListener("resize", checkOrientation, false);
    window.addEventListener("orientationchange", checkOrientation, false);

    // (optional) Android doesn't always fire orientationChange on 180 degree turns
    setInterval(checkOrientation, 2000);
})(jQuery);