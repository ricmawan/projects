
/*=============================================================
    Authour URI: www.binarycart.com
    Version: 1.1
    License: MIT
    
    http://opensource.org/licenses/MIT

    100% To use For Personal And Commercial Use.
   
    ========================================================  */

(function ($) {
    "use strict";
    var mainApp = {

        main_fun: function () {
            /*====================================
            METIS MENU 
            ======================================*/
            $('#main-menu').metisMenu();

            /*====================================
              LOAD APPROPRIATE MENU BAR
           ======================================*/
            $(window).bind("load resize", function () {
                if ($(this).width() < 768) {
                    $('div.sidebar-collapse').addClass('collapse')
                } else {
                    $('div.sidebar-collapse').removeClass('collapse')
                }
            });
		}
	}
    // Initializing ///

    $(document).ready(function () {
        mainApp.main_fun();
        $('ul.nav li.dropdown').hover(function () {
            $(this).find('.dropdown-nav-menu').stop(true, true).delay(200).slideDown();
        }, function () {
            $(this).find('.dropdown-nav-menu').stop(true, true).delay(200).slideUp();
        });
    });

}(jQuery));