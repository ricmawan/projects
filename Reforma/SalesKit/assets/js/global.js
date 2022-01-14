$.fn.hasAttr = function(name) {  
	return this.attr(name) !== undefined;
};

var CurrentMenu = "./index.html";
var PrevMenu;

$(document).ready(function () {
	
	$(document).on("click", ".menu", function() {
		var MenuClicked = $(this).attr("link");
		PrevMenu = CurrentMenu;
		CurrentMenu = MenuClicked;
		if( $(this).is("a")) $(".menu").parent().removeClass("active");
		//$("#loading").show();
		$("#main-content-inner").html("");
		if(MenuClicked != "./Home.php") {
			$.ajax({
				url: MenuClicked,
				type: "POST",
				data: { },
				dataType: "html",
				success: function(data) {
					$("#main-content-inner").html(data);
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					$('.page-container').toggleClass('sbar_collapsed');
					//$("#loading").hide();
				},
				error: function(jqXHR, textStatus, errorThrown) {
					//$("#loading").hide();
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
		}
		$(this).parent().addClass("active");
	});
});

function Redirect(link) {
	var MenuClicked = link;
	PrevMenu = CurrentMenu;
	CurrentMenu = MenuClicked;
	//$("#loading").show();
	$("#main-content-inner").html("");
	if(MenuClicked != "./Home.php") {
		$.ajax({
			url: MenuClicked,
			type: "POST",
			data: { },
			dataType: "html",
			async : true,
			success: function(data) {
				$("#main-content-inner").html(data);
				$("html, body").animate({
					scrollTop: 0
				}, "slow");
				$('.page-container').toggleClass('sbar_collapsed');
				//$("#loading").hide();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				//$("#loading").hide();
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
	}
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
					width: 480
				});
				return 0;
			}
		});
	}
}