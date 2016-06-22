$.fn.hasAttr = function(name) {  
	return this.attr(name) !== undefined;
};
var CurrentMenu = "./Home.php";
var PrevMenu;
$(document).ready(function () {

	//Date picker option : .DatePickerGlobal, .DatePickerUntilNow, .DatePickerFromNow, .DatePickerMonthYearGlobal, .DatePickerMonthYearUntilNow, .DatePickerMonthYearFromNow
	$(document).on('focus',".DatePickerGlobal", function(){
		$(this).datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$(this).attr("readonly", "true");
		$(this).css({
			"background-color": "#FFF",
			"cursor": "text"
		});
	});
	
	$(document).on('focus',".DatePickerUntilNow", function(){
		$(this).datepicker({
			dateFormat: 'dd-mm-yy',
			maxDate : "+0D"
		});
		$(this).attr("readonly", "true");
		$(this).css({
			"background-color": "#FFF",
			"cursor": "text"
		});
	});
	
	$(document).on('focus',".DatePickerFromNow", function(){
		$(this).datepicker({
			dateFormat: 'dd-mm-yy',
			minDate : "+0D"
		});
		$(this).attr("readonly", "true");
		$(this).css({
			"background-color": "#FFF",
			"cursor": "text"
		});
	});
	
	$(document).on('focus',".DatePickerMonthYearGlobal", function(){
		$(this).datepicker({
			dateFormat: 'dd-mm-yy',
			changeMonth: true,
			changeYear: true
		});
		$(this).attr("readonly", "true");
		$(this).css({
			"background-color": "#FFF",
			"cursor": "text"
		});
	});
	
	$(document).on('focus',".DatePickerMonthYearUntilNow", function(){
		$(this).datepicker({ 
			dateFormat: 'dd-mm-yy',
			maxDate : "+0D",
			changeMonth: true,
			changeYear: true,
			yearRange: "-100:+0"
		});
		$(this).attr("readonly", "true");
		$(this).css({
			"background-color": "#FFF",
			"cursor": "text"
		});
	});
	
	$(document).on('focus',".DatePickerMonthYearFromNow", function(){
		$(this).datepicker({ 
			dateFormat: 'dd-mm-yy',
			minDate : "+0D",
			changeMonth: true,
			changeYear: true,
			yearRange: "0:+100"
		});
		$(this).attr("readonly", "true");
		$(this).css({
			"background-color": "#FFF",
			"cursor": "text"
		});
	});
	
	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			$('.scrollup').fadeIn();
		} else {
			$('.scrollup').fadeOut();
		}
	});
	
	if ($(window).scrollTop() <= 50) {
		$('.scrolldown').fadeIn();
	}
	
	$(window).scroll(function () {
		if ($(this).scrollTop() <= 50) {
			$('.scrolldown').fadeIn();
		} else {
			$('.scrolldown').fadeOut();
		}
	});

	$('.scrollup').click(function () {
		$("html, body").animate({
			scrollTop: 0
		}, "slow");
		return false;
	});
	
	$('.scrolldown').click(function () {
		$("html, body").animate({
			scrollTop: $(document).height()
		}, "slow");
		return false;
	});
	
	$(document).on("click", ".menu", function() {
		//alert($(this).attr("link"));
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
				error: function(data) {
					$("#loading").hide();
					$.notify("Koneksi gagal", "error");
				}
			});
		}
		else {
			/*$.ajax({
				url: "./Master/Notification/",
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
				error: function(data) {
					$("#loading").hide();
					$.notify("Koneksi gagal", "error");
				}
			});*/
			$("#loading").hide();
		}
		$(this).addClass("active-menu");
	});
});
function Redirect(link) {
	var MenuClicked = link;
	PrevMenu = CurrentMenu;
	CurrentMenu = MenuClicked;
	//if( $(this).is("a")) $(".menu").removeClass("active-menu");
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
				//alert(data);
				$("#loading").hide();
			},
			error: function(data) {
				$("#loading").hide();
				$.notify("Koneksi gagal", "error");
			}
		});
	}
	else {
		/*$.ajax({
			url: "./Master/Notification/",
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
			error: function(data) {
				$("#loading").hide();
				$.notify("Koneksi gagal", "error");
			}
		});*/
		$("#loading").hide();
	}
}
function Reload() {
	$("#loading").show();
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
			error: function(data) {
				$("#loading").hide();
				$.notify("Koneksi gagal", "error");
			}
		});
	}
	else {
		/*$.ajax({
			url: "./Master/Notification/",
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
			error: function(data) {
				$("#loading").hide();
				$.notify("Koneksi gagal", "error");
			}
		});*/
		$("#loading").hide();
	}
}

function isNumberKey(evt) {
	var e = evt || window.event;
	var charCode = e.which || e.keyCode;
	if (charCode > 31 && (charCode < 47 || charCode > 57))
		return false;
	if (e.shiftKey) return false;
	//if (charCode == 13) convertRupiah(id, value);
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
		
		//console.log(rupiah);
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
	//$("#" + id).blur();
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
	//$("#" + id).blur();
}


function clearFormat(id, angka) {
	if($("#" + id).val() == "0.00") $("#" + id).val(".00");
	else {
		var angka1 = angka.replace(/\,/g, "");
		$("#" + id).val(angka1);
	}
}

function SubmitForm(url) {
	$("#loading").show();
	var PassValidate = 1;
	var FirstFocus = 0;
	$(".form-control-custom").each(function() {
		if($(this).hasAttr('required')) {
			if($(this).val() == "") {
				PassValidate = 0;
				$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
				if(FirstFocus == 0) $(this).focus();
				FirstFocus = 1;
				$("html, body").animate({
					scrollTop: 0
				}, "slow");
			}
		}
	});
	if(PassValidate == 1) {
		$.ajax({
			url: url,
			type: "POST",
			data: $("#PostForm").serialize(),
			dataType: "json",
			success: function(data) {
				if(data.FailedFlag == '0') {
					$.notify(data.Message, "success");
					//window.location = PrevMenu;
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
							error: function(data) {
								$("#loading").hide();
								$.notify("Koneksi gagal", "error");
							}
						});
					}
					else $("#loading").hide();
				}
				else {
					$("#loading").hide();
					$.notify(data.Message, "error");					
				}
			},
			error: function(data) {
				$("#loading").hide();
				$.notify("Koneksi gagal", "error");
				//$("#error").html(data.responseText);
				//$.notify(data.ResponseText, "error");
			}
		});
	}
	else {
		$("#loading").hide();
	}
}

function DeleteData(url) {
	var DeleteID = new Array();
	$("input:checkbox[name=select]:checked").each(function() {
		if($(this).val() != 'all') DeleteID.push($(this).val());
	});
	if(DeleteID.length > 0) {
		var ask=confirm("Apakah anda yakin ingin menghapusnya?");
		if(ask==true) {
			$("#loading").show();
			$.ajax({
				url: url,
				type: "POST",
				data: { ID : DeleteID },
				dataType: "html",
				success: function(data) {
					$("#loading").hide();
					var datadelete = data.split("+");
					var berhasil = datadelete[0];
					var gagal = datadelete [1];
					if(berhasil!="") $.notify(berhasil, "success");
					if(gagal!="") $.notify(gagal, "error");
					Reload();
				},
				error: function(data) {
					$.notify("Koneksi gagal, Cek koneksi internet!", "error");
					$("#loading").hide();
				}
					
			});
		}
	}
}

//Clock
window.onload=GetClock;
tday  =new Array("Minggu","Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu");
tmonth=new Array("January","February","Maret","April","Mei","Juni","July","Agustus","September","Oktober","November","Desember");
function checkall() {
	var checkall = $("#check-all").is(':checked'); 
	if(checkall == true) {
		$(".delete").prop('checked', true);
		$(".delete").attr('checked', true);
	}
	else {
		$(".delete").prop('checked', false);
		$(".delete").removeAttr('checked');
	}
}
function GetClock(){
	d = new Date();
	nday   = d.getDay();
	nmonth = d.getMonth();
	ndate  = d.getDate();
	nyear = d.getYear();
	nhour  = d.getHours();
	nmin   = d.getMinutes();
	nsec   = d.getSeconds();
	if(nyear<1000) nyear=nyear+1900;
	if(nmin <= 9) {nmin = "0" +nmin;}
	if(nsec <= 9) {nsec = "0" +nsec;}
	$("#Clock").html(" " + tday[nday] + ", " + ndate + " " + tmonth[nmonth] + " " + nyear + " " + nhour + ":" + nmin + ":" + nsec);
	setTimeout("GetClock()", 1000);
}
//marquee title bar
var rev = "fwd";
function titlebar(val){
	var msg  = "Pusat Psikologi Terapan Unika Soegijapranata";
	var res = " ";
	var speed = 50;
	var pos = val;
	msg = "   | "+msg+" |";
	var le = msg.length;
	if(rev == "fwd"){ 
		if(pos < le){ 
			pos = pos+1; 
			scroll = msg.substr(0,pos); 
			document.title = scroll; 
			timer = window.setTimeout("titlebar("+pos+")",speed); 
		} else { 
			rev = "bwd"; 
			timer = window.setTimeout("titlebar("+pos+")",speed); 
		}
	} else { 
		if(pos > 0) {
			pos = pos-1; 
			var ale = le-pos; 
			scrol = msg.substr(ale,le); 
			document.title = scrol; 
			timer = window.setTimeout("titlebar("+pos+")",speed); 
		} else { 
			rev = "fwd"; 
			timer = window.setTimeout("titlebar("+pos+")",speed); 
		}
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
//titlebar(0);

//disable right click
/*$(document).on({
	"contextmenu": function(e) {
		//console.log("ctx menu button:", e.which); 
		// Stop the context menu
		e.preventDefault();
	}
});*/

//combobox autocomplete
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

		_createAutocomplete: function() {
			var selected = this.element.children( ":selected" ),
			value = selected.val() ? selected.text() : "",
			placeholder = this.element.attr("placeholder");
			wasOpen = false;
			this.input = $( "<input style='font-family: Open Sans, sans-serif; font-size: 12px;'>" )
			.appendTo( this.wrapper )
			.val( value )
			.attr( "title", "" )
			.attr("placeholder", placeholder)
			.addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
			.autocomplete({
				delay: 0,
				minLength: 0,
				source: $.proxy( this, "_source" ),
				select: function(event, ui) {
						//$("#txtCP").val($("#ddlKlien").attr("cp"));
					}
			})
			.mousedown(function() {
				wasOpen = input.autocomplete( "widget" ).is( ":visible" );
			})
			.click(function() {
				input.focus();
				// Close if already visible
				if ( wasOpen ) {
				return;
				}

				// Pass empty string as value to search for, displaying all results
				input.autocomplete( "search", "" );
			})
			.tooltip({
				tooltipClass: "ui-state-highlight"
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
			var input = this.input,
			wasOpen = false;
			this.caret = $ (" <b class='caret' acronym title='Tampilkan Semua' style='cursor: pointer;'></b> " )
			.appendTo( this.wrapper )
			.mousedown(function() {
				wasOpen = input.autocomplete( "widget" ).is( ":visible" );
			})
			.click(function() {
				input.focus();
				// Close if already visible
				if ( wasOpen ) {
				return;
				}

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
			.attr( "title", value + " Tidak Cocok Dengan Pilihan Yang Ada" )
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
		$(this).autocomplete("widget").css({
            "width": ($(this).width() + "px")
        });
    }
});