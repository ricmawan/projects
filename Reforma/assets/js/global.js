$.fn.hasAttr = function(name) {  
	return this.attr(name) !== undefined;
};

var CurrentMenu = "./Home.php";
var PrevMenu;

$(document).ready(function () {
	
	$(document).on("click", ".menu", function() {
		var MenuClicked = $(this).attr("link");
		PrevMenu = CurrentMenu;
		CurrentMenu = MenuClicked;
		if( $(this).is("a")) $(".menu").removeClass("active-menu");
		$("#loading").show();
		$("#page-inner").html("");
		if(MenuClicked != "./Home.php") {
			$.ajax({
				url: MenuClicked,
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
					LogEvent(errorMessage, "global.js (fnMenuClick)");
					Lobibox.alert("error",
					{
						msg: errorMessage,
						width: 320
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
						width: 320
					});
				}
			});
		}
		$(this).addClass("active-menu");
	});
});

function Redirect(link) {
	var MenuClicked = link;
	PrevMenu = CurrentMenu;
	CurrentMenu = MenuClicked;
	$("#loading").show();
	$("#page-inner").html("");
	if(MenuClicked != "./Home.php") {
		$.ajax({
			url: MenuClicked,
			type: "POST",
			data: { },
			dataType: "html",
			async : true,
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
				LogEvent(errorMessage, "global.js (fnRedirect)");
				Lobibox.alert("error",
				{
					msg: errorMessage,
					width: 320
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
					width: 320
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
		for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+',';
		
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

function convertWeight(id, angka){
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
		for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+',';
		
		if(angkarev.length > 3) {
			angka = rupiah.split('',rupiah.length-1).reverse().join('');
			if(flag == 1) $("#" + id).val(angka + "." + koma);
			else $("#" + id).val(angka + ".00");
		}
		else {
			if(flag == 1) $("#" + id).val(angka + "." + koma);
			else $("#" + id).val(angka + ".00");
		}
	}
	else if(angka == "" || angka == ".00") {
		$("#" + id).val("0.00");
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
		for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+',';
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

function returnWeight(angka) {	
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
		for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+',';
		if(angkarev.length > 3) {
			angka = rupiah.split('',rupiah.length-1).reverse().join('');
			if(flag == 1) angka = angka + "." + koma;
			else angka = angka + ".00";
		}
		else {
			if(flag == 1) angka = angka + "." + koma;
			else angka = angka + ".00";
		}
	}
	else if(angka == "" || angka == ".00") {
		angka = "0.00";
	}
	return angka;
}

function clearFormat(id, angka) {
	if($("#" + id).val() == "0.00") $("#" + id).val(".00");
	else {
		var angka1 = angka.replace(/\,/g, "");
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
					width: 320
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
					width: 320
				});
			}
		});
	}
}

function minmax(value, min, max) 
{
	if(parseInt(value) < 0 || isNaN(value)) 
		return 0; 
	else if(parseInt(value) > max) 
		return max; 
	else return value;
}

function chkAll() {
	if($("#select_all").prop("checked") == true) {
		$("input:checkbox[name=select]").each(function() {
			$(this).prop("checked", true);
			$(this).attr("checked", true);
		});
	}
	else {
		$("input:checkbox[name=select]").each(function() {
			$(this).prop("checked", false);
			$(this).attr("checked", false);
		});
	}
		
}

//combobox autocomplete
var idBeforeEsc = "";
(function( $ ) {
	$.widget( "custom.combobox", {
		_create: function() {
			this.wrapper = $( "<span>" )
			.addClass( "custom-combobox" )
			.insertAfter( this.element );
			
			this.element.hide();
			this._createAutocomplete();
			//this._createShowAllButton();
		},
		_createShowAllButton: function() {
			var input = this.input,
			wasOpen = false;

			$( "<a>" )
			.attr( "tabIndex", -1 )
			.attr( "title", "Show All Items" )
			.tooltip()
			.appendTo( this.wrapper )
			.button({
				icons: {
					primary: "ui-icon-triangle-1-s"
				},
				text: false
			})
			.removeClass( "ui-corner-all" )
			.addClass( "custom-combobox-toggle ui-corner-right" )
			.on( "mousedown", function() {
				wasOpen = input.autocomplete( "widget" ).is( ":visible" );
			})
			.on( "click", function() {
				input.trigger( "focus" );

				// Close if already visible
				if ( wasOpen ) {
				return;
				}

				// Pass empty string as value to search for, displaying all results
				input.autocomplete( "search", "" );
			});
		},

		_createAutocomplete: function() {
			var selected = this.element.children( ":selected" ),
			value = selected.val() ? selected.text() : "",
			placeholder = this.element.attr("placeholder");
			tabindex = this.element.attr("tabindex");
			var dialogIndex = $(".ui-dialog").css("z-index");
			var newIndex = parseInt(dialogIndex) + 1;
			var id = this.element.attr("id");
			wasOpen = false;
			this.input = $( "<input style='font-family: Open Sans, sans-serif; font-size: 12px;'>" )
			.appendTo( this.wrapper )
			.val( value )
			.attr( "title", "" )
			.attr("placeholder", placeholder)
			.attr("tabindex", tabindex)
			
			.addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
			.autocomplete({
				delay: 0,
				minLength: 0,
				source: $.proxy( this, "_source" )
			})
			.mousedown(function() {
				wasOpen = input.autocomplete( "widget" ).is( ":visible" );
			})
			.click(function() {
				input.focus();
				dialogIndex = $(".ui-dialog").css("z-index");
				newIndex = parseInt(dialogIndex) + 1;
				$(".ui-autocomplete").css({
					"z-index":  newIndex
				});
				// Close if already visible
				if ( wasOpen ) {
				return;
				}
				input.select();
				// Pass empty string as value to search for, displaying all results
				input.autocomplete( "search", "" );
			})
			.keydown(function(evt) {
				if(evt.keyCode == 38 || evt.keyCode == 40) {
					// Pass empty string as value to search for, displaying all results
					//input.autocomplete( "search", "" );
					if($("#" + id).val() != "") {
						//input.val("");
						input.trigger( "focus" );

						// Close if already visible
						if ( wasOpen ) {
						return;
						}

						// Pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
						idBeforeEsc = $("#" + id).val();
						$("#" + id).val("");
					}
				}
				else if(evt.keyCode == 27) {
					$("#" + id).val(idBeforeEsc);
				}
			})
			.tooltip({
				tooltipClass: "ui-state-highlight"
			})
			.focus(function() {
				// Close if already visible
				if ( wasOpen ) {
				return;
				}
				input.select();
				// Pass empty string as value to search for, displaying all results
				input.autocomplete( "search", "" );
			});

			this._on( this.input, {
				autocompleteselect: function( event, ui ) {
					ui.item.option.selected = true;
					this._trigger( "select", event, {
						item: ui.item.option
					});
				},
				autocompletechange: "_removeIfInvalid"
			});
			var input = this.input;
			//wasOpen = false;
			this.caret = $ (" <b class='caret' acronym title='Tampilkan Semua' style='cursor: pointer;'></b> " )
			.appendTo( this.wrapper )
			.mousedown(function() {
				wasOpen = input.autocomplete( "widget" ).is( ":visible" );
			})
			.click(function() {
				input.focus();
				dialogIndex = $(".ui-dialog").css("z-index");
				newIndex = parseInt(dialogIndex) + 1;
				$(".ui-autocomplete").css({
					"z-index":  newIndex
				});
				// Close if already visible
				if ( wasOpen ) {
				return;
				}
				input.select();
				// Pass empty string as value to search for, displaying all results
				input.autocomplete( "search", "" );
			});
		},
		_source: function( request, response ) {
			var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
			response( this.element.children( "option" ).map(function() {
				var text = $( this ).text();
				if ( this.value && ( !request.term || matcher.test(text) ) )
				return {
					label: text,
					value: text,
					option: this
				};
			}) );
		},

		_removeIfInvalid: function( event, ui ) {

			// Selected an item, nothing to do
			if ( ui.item ) {
				return;
			}

			// Search for a match (case-insensitive)
			var value = this.input.val(),
			valueLowerCase = value.toLowerCase(),
			valid = false;
			this.element.children( "option" ).each(function() {
				if ( $( this ).text().toLowerCase() === valueLowerCase ) {
					this.selected = valid = true;
					return false;
				}
			});

			// Found a match, nothing to do
			if ( valid ) {
				return;
			}

			// Remove invalid value
			this.input
			.val( "" )
			//.attr( "title", value + " Tidak Cocok Dengan Pilihan Yang Ada" )
			.notify("Tidak Cocok Dengan Pilihan Yang Ada!", { position:"bottom left", className:"warn", autoHideDelay: 2000 })
			this.element.val( "" );
			/*.tooltip( "open" );
			
			this._delay(function() {
				this.input.tooltip( "close" ).attr( "title", "" );
			}, 2500 );
			this.input.autocomplete( "instance" ).term = "";*/
		},

		_destroy: function() {
			this.wrapper.remove();
			this.element.show();
		}
	});
})( jQuery );

$.extend($.ui.autocomplete.prototype.options, {
	open: function(event, ui) {
		var dialogIndex = $(".ui-dialog").css("z-index");
		var windowIndex = $(".lobibox-backdrop").css("z-index");
		if(typeof windowIndex != 'undefined' && parseInt(windowIndex) > parseInt(dialogIndex)) {
			dialogIndex = windowIndex;
		}
		var newIndex = parseInt(dialogIndex) + 1;
		$(this).autocomplete("widget").css({
            "width": ($(this).width() + "px !important"),
			"z-index":  newIndex
        });
    }
});