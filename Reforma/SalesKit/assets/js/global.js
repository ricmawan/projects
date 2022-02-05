$.fn.hasAttr = function(name) {  
	return this.attr(name) !== undefined;
};

var GLOBAL_STATE_COUNTER = 0;
var historyJs = window.History;
var pdfView = 0;
var xDown = null;                                                        
var yDown = null;
var table;
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

$(document).ready(function () {
	window.onscroll = function() {scrollFunction()};
	//document.addEventListener('touchstart', handleTouchStart, false);        
	//document.addEventListener('touchmove', handleTouchMove, false);
	window.addEventListener("resize", checkOrientation, false);
	window.addEventListener("orientationchange", checkOrientation, false);

	// (optional) Android doesn't always fire orientationChange on 180 degree turns
	setInterval(checkOrientation, 2000);

	$('.scrollup').click(function () {
		$("html").animate({
			scrollTop: 0
		}, "slow");
		return false;
	});

	// Bind the adapter to listen 'statechange' event.
    historyJs.Adapter.bind(window, "statechange", function() {
 
        // Get state object.
        var state = historyJs.getState();
 		if(typeof state.data.counter == 'undefined') {
			Redirect("./Dashboard.html");
			return false;
		}
        // Check whether back button is pressed.
        if (state.data.counter < GLOBAL_STATE_COUNTER) {
			// Reload the content.
			Redirect(state.data.url);
        	//historyJs.back();
        }
    });

    $(document).on("click", ".pdfMenu", function() {
		var MenuClicked = $(this).attr("link");
		//if( $(this).is("a")) $(".menu").parent().removeClass("active");
		$("#loading").show();
		$("#main-content-inner").html("");
		//if(MenuClicked != "./Home.php") {
			$.ajax({
				url: "./pdfjs-dist/web/viewer.html",
				type: "GET",
				data: { },
				dataType: "html",
				success: function(data) {
					if(pdfView == 0) {
						$(".page-title-area").css("cssText", "display: none !important;");
						$(".header-area").css("cssText", "display: none !important;");
						$(".footer-area").css("cssText", "display: none !important;");
						pdfView = 1;
					}
					$("#main-content-inner").html(data);
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					//$('.page-container').toggleClass('sbar_collapsed');
					//$("#breadcrumbsContainer").html($("#breadcrumbsContent").html());
					$("#loading").hide();
					//window.history.pushState({href: MenuClicked}, '', MenuClicked);
					historyJs.pushState(
		               {
		                   counter: GLOBAL_STATE_COUNTER,
		                   url: MenuClicked
		               },
		               'R-Force', // It should be filled with document title, but just ignore it for now.
		               "?file=" + MenuClicked
		            );
		            // Increment global state counter.
            		GLOBAL_STATE_COUNTER++;
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$("#loading").hide();
					var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
					//LogEvent(errorMessage, "global.js (fnMenuClick)");
					//Lobibox.alert("error",
					//{
					//	msg: errorMessage,
					//	width: 480
					//});
					return 0;
				}
			});
		//}
		//$(this).parent().addClass("active");
	});
	
	$(document).on("click", ".menu", function() {
		var MenuClicked = $(this).attr("link");
		if( $(this).is("a")) $(".menu").parent().removeClass("active");
		$("#loading").show();
		if ( $.fn.DataTable.isDataTable( table ) ) {
            table.fixedHeader.disable();
        }
		$("#main-content-inner").html("");
		//if(MenuClicked != "./Home.php") {
			$.ajax({
				url: MenuClicked,
				type: "POST",
				data: { },
				dataType: "html",
				success: function(data) {
					if(pdfView == 1) {
						$(".page-title-area").css("cssText", "");
						$(".header-area").css("cssText", "");
						$(".footer-area").css("cssText", "");
						$(".fileInput").remove();
						pdfView = 0;
					}
					$("#main-content-inner").html(data);
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					$('.page-container').addClass('sbar_collapsed');
					$("#breadcrumbsContainer").html($("#breadcrumbsContent").html());
					$("#loading").hide();
					//window.history.pushState({href: MenuClicked}, '', MenuClicked);
					historyJs.pushState(
		               {
		                   counter: GLOBAL_STATE_COUNTER,
		                   url: MenuClicked
		               },
		               'R-Force', // It should be filled with document title, but just ignore it for now.
		               "?state=" + GLOBAL_STATE_COUNTER
		            );
		            // Increment global state counter.
            		GLOBAL_STATE_COUNTER++;
				},
				error: function(jqXHR, textStatus, errorThrown) {
					$("#loading").hide();
					var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
					//LogEvent(errorMessage, "global.js (fnMenuClick)");
					//Lobibox.alert("error",
					//{
					//	msg: errorMessage,
					//	width: 480
					//});
					return 0;
				}
			});
		//}
		$(this).parent().addClass("active");
	});

	$(document).on("click", ".zoom", function() {
        $("#img-modal").show();
        $("#img-content").attr("src", $(this).attr("src"));
        $("#img-caption").html($(this).attr("alt"));
    });

    $(document).on("click", ".close, #img-modal", function() {
        $("#img-modal").hide();
    });    
});

/*function getTouches(evt) {
  return evt.touches ||             // browser API
         evt.originalEvent.touches; // jQuery
}                                                     
                                                                         
function handleTouchStart(evt) {
    const firstTouch = getTouches(evt)[0];                                      
    xDown = firstTouch.clientX;                                      
    yDown = firstTouch.clientY;                                      
};                                                
                                                                         
function handleTouchMove(evt) {
    if ( ! xDown || ! yDown ) {
        return;
    }

    var xUp = evt.touches[0].clientX;                                    
    var yUp = evt.touches[0].clientY;

    var xDiff = xDown - xUp;
    var yDiff = yDown - yUp;
                                                                         
    if ( Math.abs( xDiff ) > Math.abs( yDiff ) ) {/*most significant
        if ( xDiff > 0 ) {
            /* right swipe 
            //$('.page-container').toggleClass('sbar_collapsed');
            $('.page-container').addClass('sbar_collapsed');
        } else {
            /* left swipe 
            $('.page-container').removeClass('sbar_collapsed');
        }                       
    } else {
        if ( yDiff > 0 ) {
            /* down swipe/ 
        } else { 
            /* up swipe 
        }                                                                 
    }
    /* reset values 
    xDown = null;
    yDown = null;                                             
};*/

function scrollFunction() {
	if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
		$('.scrollup').fadeIn();
	} else {
		$('.scrollup').fadeOut();
	}
}

function Redirect(link) {
	var MenuClicked = link;
	$("#loading").show();
	if ( $.fn.DataTable.isDataTable( table ) ) {
        table.fixedHeader.disable();
    }
	$("#main-content-inner").html("");
	//if(MenuClicked != "./Home.php") {
		$.ajax({
			url: MenuClicked,
			type: "POST",
			data: { },
			dataType: "html",
			async : true,
			success: function(data) {
				if(pdfView == 1) {
					$(".page-title-area").css("cssText", "");
					$(".header-area").css("cssText", "");
					$(".footer-area").css("cssText", "");
					$(".fileInput").remove();
					pdfView = 0;
				}
				$("#main-content-inner").html(data);
				$("html, body").animate({
					scrollTop: 0
				}, "slow");
				//$('.page-container').toggleClass('sbar_collapsed');
				$("#breadcrumbsContainer").html($("#breadcrumbsContent").html());
				$("#loading").hide();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$("#loading").hide();
				var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
				//LogEvent(errorMessage, "global.js (fnRedirect)");
				//Lobibox.alert("error",
				//{
					//msg: errorMessage,
					//width: 480
				//});
				return 0;
			}
		});
	//}
}

function Reload() {
	//$("#loading").show();
	$("#page-inner").html("");
	if(CurrentMenu != "./Home.php") {
		$.ajax({
			url: CurrentMenu,
			type: "POST",
			data: { },
			dataType: "html",
			success: function(data) {
				$("#page-inner").html(data);
				$("html, body").animate({
					scrollTop: 0
				}, "slow");
				$("#loading").hide();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$("#loading").hide();
				var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
				LogEvent(errorMessage, "global.js (fnReload)");
				Lobibox.alert("error",
				{
					msg: errorMessage,
					width: 480
				});
				return 0;
			}
		});
	}
	else {
		$("#loading").show();
		$.ajax({
			url: "./Notification/",
			type: "POST",
			data: { },
			dataType: "html",
			success: function(data) {
				$("#loading").hide();
				$("#page-inner").html(data);
			},
			error: function(data) {
				$("#loading").hide();
				var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
				LogEvent(errorMessage, "/Home.php");
				Lobibox.alert("error",
				{
					msg: errorMessage,
					width: 480
				});
			}
		});
	}
}

function isNumberKey(evt) {
	var e = evt || window.event;
	var charCode = e.which || e.keyCode;
	if (charCode > 31 && (charCode < 47 || charCode > 57))
		return false;
	if (e.shiftKey) return false;
	return true;
}

function isNumberKey(evt, id, value) {
	$("#" + id).removeAttr("onpaste");
	$("#" + id).attr("onpaste", "return false;");
	var e = evt || window.event;
	var charCode = e.which || e.keyCode;
	if(charCode == 46 && value.indexOf(".") > 0) return false;
	else if(charCode == 46 && value.indexOf(".") < 0) return true;
	if (charCode > 31 && (charCode < 47 || charCode > 57))
		return false;
	if (e.shiftKey) return false;
	if (charCode == 13) $("#" + id).blur();
	return true;
}

function isEnterKey(evt, fn) {
	var e = evt || window.event;
	e.stopImmediatePropagation();
	var charCode = e.which || e.keyCode;
	if (charCode == 13) window[fn]();
}

function PercentNumber(evt, id, value) {
	$("#" + id).removeAttr("onpaste");
	$("#" + id).attr("onpaste", "return false;");
	var e = evt || window.event;
	var charCode = e.which || e.keyCode;
	if(charCode == 46 && value.indexOf(".") > 0) return false;
	else if(charCode == 46 && value.indexOf(".") < 0) return true;
	if (charCode > 31 && (charCode < 47 || charCode > 57))
		return false;
	if (e.shiftKey) return false;
	return true;
}

function convertRupiah(id, angka){
	if(angka.indexOf(",") < 0 && angka != "" && angka != ".00") {
		var flag = 0;
		if(angka.indexOf(".") > 0) {
			var temp = angka.split(".");
			angka = temp[0];
			var koma = temp[1];
			flag = 1;
		}
		angka = angka.replace(/\,/g, "");		
		var rupiah = '';
		var angkarev = angka.toString().split('').reverse().join('');
		for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
		
		if(angkarev.length > 3) {
			angka = rupiah.split('',rupiah.length-1).reverse().join('');
			if(flag == 1) $("#" + id).val(angka);
			else $("#" + id).val(angka);
		}
		else {
			if(flag == 1) $("#" + id).val(angka);
			else $("#" + id).val(angka);
		}
	}
	else if(angka == "" || angka == ".00") {
		$("#" + id).val("0");
	}
}

function returnRupiah(angka) {	
	if(angka.indexOf(",") < 0 && angka != "" && angka != ".00") {
		var flag = 0;
		if(angka.indexOf(".") > 0) {
			var temp = angka.split(".");
			angka = temp[0];
			var koma = temp[1];
			flag = 1;
		}
		angka = angka.replace(/\,/g, "");
		var rupiah = '';
		var angkarev = angka.toString().split('').reverse().join('');
		for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
		if(angkarev.length > 3) {
			angka = rupiah.split('',rupiah.length-1).reverse().join('');
			if(flag == 1) angka = angka;
			else angka = angka;
		}
		else {
			if(flag == 1) angka = angka;
			else angka = angka;
		}
	}
	else if(angka == "" || angka == ".00") {
		angka = "0";
	}
	return angka;
}

function clearFormat(id, angka) {
	if($("#" + id).val() == "0.00") $("#" + id).val(".00");
	else {
		var angka1 = angka.replace(/\./g, "");
		$("#" + id).val(angka1);
	}
}

function Back() {
	$("#loading").show();
	$("#page-inner").html("");
	if(PrevMenu != "./Home.php") {
		CurrentMenu = PrevMenu;
		$.ajax({
			url: PrevMenu,
			type: "POST",
			data: { },
			dataType: "html",
			success: function(data) {
				$("#page-inner").html(data);
				$("html, body").animate({
					scrollTop: 0
				}, "slow");
				$("#loading").hide();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				$("#loading").hide();
				var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
				LogEvent(errorMessage, "global.js (fnBack)");
				Lobibox.alert("error",
				{
					msg: errorMessage,
					width: 480
				});
				return 0;
			}
		});
	}
}