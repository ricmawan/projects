
(function($) {
	"use strict";

	/*================================
	Preloader
	==================================*/

	var preloader = $('#preloader');
	$(window).on('load', function() {
		if(localStorage.getItem("unlock") === null) { 
			window.location.replace("./index.html");
		}
		else {
			if(localStorage.getItem("unlock") === "7b6496cf6e9f66c087a3ad164aaef7ab") {
				setTimeout(function() {
					preloader.fadeOut('slow', function() { $(this).remove(); });
				}, 300);
			}
			else {
				window.location.replace("./index.html");
			}
		}
	});

	/*================================
	sidebar collapsing
	==================================*/
	if (window.innerWidth <= 1364) {
		$('.page-container').addClass('sbar_collapsed');
	}
	$('.nav-btn').on('click', function() {
		$('.page-container').toggleClass('sbar_collapsed');
		if ( $.fn.DataTable.isDataTable( table ) ) {
            setTimeout(function() {
            	table.columns.adjust().draw();
            }, 400);
        }
	});

	/*================================
	Start Footer resizer
	==================================*/
	var e = function() {
		setTimeout(function() {
			var e = (window.innerHeight > 0 ? window.innerHeight : this.screen.height) - 5;
			(e -= 62) < 1 && (e = 1), e > 62 && $(".main-content").css("min-height", e + "px");
		}, 0);
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
})(jQuery);