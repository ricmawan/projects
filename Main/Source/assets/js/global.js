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
					LogEvent(errorMessage, "global.js (MenuClick)");
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
			$("#page-inner").html('<img src="./assets/img/logo.png" style="width:100%;"/>');
			$("#loading").hide();
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
				LogEvent(errorMessage, "global.js (Redirect)");
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
		$("#page-inner").html('<img src="./assets/img/logo.png" style="width:100%;"/>');
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
			error: function(jqXHR, textStatus, errorThrown) {
				$("#loading").hide();
				var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
				LogEvent(errorMessage, "global.js (Reload)");
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
		$("#page-inner").html('<img src="./assets/img/logo.png" style="width:100%;"/>');
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
				LogEvent(errorMessage, "global.js (Back)");
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

function UpdatePassword() {
	enterLikeTab();
	$("#update-password").dialog({
		autoOpen: false,
		open: function() {
			$("#divModal").show();
			$(document).on('keydown', function(e) {
				if (e.keyCode == 39 && $("input:focus").length == 0) { //right arrow
					 $("#btnCancelSavePassword").focus();
				}
				else if (e.keyCode == 37 && $("input:focus").length == 0) { //left arrow
					 $("#btnSavePassword").focus();
				}
			});
		},
		show: {
			effect: "fade",
			duration: 500
		},
		hide: {
			effect: "fade",
			duration: 500
		},
		close: function() {
			$(this).dialog("destroy");
			$("#divModal").hide();
		},
		resizable: false,
		height: 250,
		width: 450,
		modal: false,
		buttons: [
		{
			text: "Simpan",
			id: "btnSavePassword",
			tabindex: 4,
			click: function() {
				var PassValidate = 1;
				var FirstFocus = 0;
				if($("#txtCurrentPassword").val() == '') {
					$("#txtCurrentPassword").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#txtCurrentPassword").focus();
					PassValidate = 0;
					FirstFocus = 1;
				}
				if($("#txtNewPassword").val() == '') {
					$("#txtNewPassword").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#txtNewPassword").focus();
					PassValidate = 0;
					FirstFocus = 1;
				}
				if($("#txtConfirmNewPassword").val() == '') {
					$("#txtConfirmNewPassword").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#txtConfirmNewPassword").focus();
					PassValidate = 0;
					FirstFocus = 1;
				}
				
				if($("#txtConfirmNewPassword").val() != $("#txtNewPassword").val()) {
					$("#txtConfirmNewPassword").notify("Konfirmasi Password tidak cocok!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#txtConfirmNewPassword").focus();
					PassValidate = 0;
					FirstFocus = 1;
				}
				if(PassValidate == 0) return false;
				$("#loading").show();
				
				$.ajax({
					url: "./UpdatePassword.php",
					type: "POST",
					data: $("#UpdatePasswordForm").serialize(),
					dataType: "json",
					success: function(data) {
						$("#loading").hide();
						if(data.FailedFlag == '0') {
							//$.notify(data.Message, "success");
							$("#update-password").dialog("destroy");
							$("#divModal").hide();
							Lobibox.alert("success",
							{
								msg: data.Message,
								width: 480,
								delay: 2000
							});
						}
						else {
							$("#loading").hide();
							Lobibox.alert("warning",
							{
								msg: data.Message,
								width: 480,
								delay: false
							});
						}
						
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$("#loading").hide();
						var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
						LogEvent(errorMessage, "global.js (UpdatePassword)");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});
			}
		},
		{
			text: "Batal",
			id: "btnCancelSavePassword",
			click: function() {
				$(this).dialog("destroy");
				$("#divModal").hide();
				return false;
			}
		}]
	}).dialog("open");
}

function SingleDelete(url, DeleteID, callback) {
	$("#delete-confirm").dialog({
		autoOpen: false,
		open: function() {
			$(document).on('keydown', function(e) {
				if (e.keyCode == 39) { //right arrow
					$("#btnNoDelete").focus();
				}
				else if (e.keyCode == 37) { //left arrow
					 $("#btnYesDelete").focus();
				}
			});
		},
		close: function() {
			$(this).dialog("destroy");
			callback("Tidak");
		},
		show: {
			effect: "fade",
			duration: 500
		},
		hide: {
			effect: "fade",
			duration: 500
		},
		resizable: false,
		height: "auto",
		width: 400,
		modal: true,
		buttons: [
		{
			text: "Ya",
			id: "btnYesDelete",
			click: function() {
				$(this).dialog("destroy");
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
						var counter1 = 0;
						var counter2 = 0;
						if(berhasil!="") {
							Lobibox.alert("success",
							{
								msg: berhasil,
								width: 480,
								delay: 2000,
								beforeClose: function() {
									if(counter1 == 0) {
										if(gagal=="") callback("Ya");
										counter1 = 1;
									}
								}
							});
						}
						if(gagal!="") {
							Lobibox.alert("warning",
							{
								msg: gagal,
								width: 480,
								delay: false,
								beforeClose: function() {
									if(counter2 == 0) {
										callback("Ya");
										counter2 = 1;
									}
								}
							});
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$("#loading").hide();
						var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
						LogEvent(errorMessage, "global.js (SingleDelete)");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						callback("Error");
					}
				});
			}
		},
		{
			text: "Tidak",
			id: "btnNoDelete",
			click: function() {
				$(this).dialog("destroy");
				callback("Tidak");
			}
		}]
	}).dialog("open");
}

function DeleteData(url, callback) {
	var DeleteID = new Array();
	$("input:checkbox[name=select]:checked").each(function() {
		if($(this).val() != 'all') DeleteID.push($(this).val());
	});
	if(DeleteID.length > 0) {
		$("#delete-confirm").dialog({
			autoOpen: false,
			open: function() {
				$(document).on('keydown', function(e) {
					if (e.keyCode == 39) { //right arrow
						$("#btnNoDel").focus();
					}
					else if (e.keyCode == 37) { //left arrow
						 $("#btnYesDel").focus();
					}
				});
			},
			close: function() {
				$(this).dialog("destroy");
				callback("Tidak");
			},
			show: {
				effect: "fade",
				duration: 500
			},
			hide: {
				effect: "fade",
				duration: 500
			},
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: [
			{
				text: "Ya",
				id: "btnYesDel",
				click: function() {
					$(this).dialog("destroy");
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
							var counter1 = 0;
							var counter2 = 0;
							if(berhasil!="") {
								Lobibox.alert("success",
								{
									msg: berhasil,
									width: 480,
									delay: 2000,
									beforeClose: function() {
										if(counter1 == 0) {
											if(gagal=="") callback("Ya");
											counter1 = 1;
										}
									}
								});
							}
							if(gagal!="") {
								Lobibox.alert("warning",
								{
									msg: gagal,
									width: 480,
									delay: false,
									beforeClose: function() {
										if(counter2 == 0) {
											callback("Ya");
											counter2 = 1;
										}
									}
								});
							}
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$("#loading").hide();
							var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
							LogEvent(errorMessage, "global.js (DeleteData)");
							Lobibox.alert("error",
							{
								msg: errorMessage,
								width: 480
							});
							callback("Error");
						}
					});
				}
			},
			{
				text: "Tidak",
				id: "btnNoDel",
				click: function() {
					$(this).dialog("close");
					callback("Tidak");
				}
			}]
		}).dialog("open");
	}
}

function saveConfirm(callback) {
	var PassValidate = 1;
	var FirstFocus = 0;
	$(".form-control-custom").each(function() {
		if($(this).hasAttr('required')) {
			if($(this).val() == "") {
				PassValidate = 0;
				$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
				if(FirstFocus == 0) $(this).focus();
				FirstFocus = 1;
			}
		}
	});
	if(PassValidate == 1) {
		$("#save-confirm").dialog({
			autoOpen: false,
			open: function() {
				$(document).on('keydown', function(e) {
					if (e.keyCode == 39) { //right arrow
						 $("#btnNo").focus();
					}
					else if (e.keyCode == 37) { //left arrow
						 $("#btnYes").focus();
					}
				});
			},
			show: {
				effect: "fade",
				duration: 500
			},
			hide: {
				effect: "fade",
				duration: 500
			},
			close: function() {
				$(this).dialog("destroy");
				callback("Tidak");
			},
			resizable: false,
			height: "auto",
			width: 400,
			modal: true,
			buttons: [
			{
				text: "Ya",
				id: "btnYes",
				click: function() {
					$(this).dialog("destroy");
					callback("Ya");
				}
			},
			{
				text: "Tidak",
				id: "btnNo",
				click: function() {
					$(this).dialog("destroy");
					callback("Tidak");
				}
			}]
		}).dialog("open");
	}
	else {
		callback("Error")
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

var enterCounter = 0;
function enterLikeTab() {
	$("input").not($(":submit, :button")).keypress(function (evt) {
		if (evt.keyCode == 13) {
			evt.preventDefault();
			var next = $('[tabindex="'+(this.tabIndex+1)+'"]');
			var nextTabIndex = this.tabIndex+1;
			if(next.length) {
				if(next.attr("disabled") == "disabled") {
					$(document).find(":focusable").each(function() {
						console.log("element: " + parseInt($(this)[0].tabIndex));
						console.log("element: " + nextTabIndex);
						if(parseInt($(this)[0].tabIndex) > nextTabIndex) {
							$(this).focus();
							return false;
						}
					});
				}
				else next.focus();
			}
			else $('[tabindex="1"]').focus();
		}
	});
	
	$("select").keypress(function (evt) {
		if (evt.keyCode == 13) {
			evt.preventDefault();
			var next = $('[tabindex="'+(this.tabIndex+1)+'"]');
			if(next.length) next.focus();
			else $('[tabindex="1"]').focus();
		}
	});
	
	$("textarea").keypress(function (evt) {
		if (evt.keyCode == 13) {
			if(enterCounter == 0) {
				enterCounter = 1;
				setTimeout(function() { enterCounter = 0; }, 1000);
			}
			else {
				evt.preventDefault();
				var txtAreaValue = $(this).val();
				txtAreaValue = txtAreaValue.substr(0, txtAreaValue.length - 1);
				$(this).val(txtAreaValue);
				var next = $('[tabindex="'+(this.tabIndex+1)+'"]');
				if(next.length) next.focus();
				else $('[tabindex="1"]').focus();  
			}
		}
	});
	
	$(document).keypress(function (evt) {
		//evt.preventDefault();
		if (evt.keyCode == 13) {
			if($(":focus").length == 0) $('[tabindex="1"]').focus();
		}
	});
}

var counter = 0;
function keyFunction() {
	$(document).on("keydown", function (evt) {		
		//console.log(evt.keyCode);
		//$(this).off(evt);
		/*if (evt.keyCode == 46) { //delete button
			evt.preventDefault();
			if($(":focus").length == 0) $("#btnDelete").click();
		}
		else*/
		if (evt.keyCode == 45) { //insert button
			evt.preventDefault();
			if(counter == 0) {
				$("#btnAdd").click();
				counter = 1;
				setTimeout(function() { counter = 0; } , 1000);
			}
		}
		else if(evt.keyCode == 222 /* ' */ || evt.keyCode == 188 /* , */ /*|| evt.keyCode == 190 /* . */|| evt.keyCode == 191 /* / */ || evt.keyCode == 220 /* \ */ || evt.keyCode == 186 /* ; */) {
			evt.preventDefault();
			return false;
		}
		else if((evt.keyCode == 38 || evt.keyCode == 40 /* up & down */) && $("input:focus").length == 0 && $(".focus").length == 0 ) {
			evt.preventDefault();
			$("#grid-data").DataTable().cell( ':eq(0)' ).focus();
		}
		else {
			return true;
		}
		
		//setTimeout(function() { $(this).on(evt); }, 500);
	});
}

function LogEvent(Description, Source) {
	$.ajax({
		url: "./LogEvent.php",
		type: "POST",
		data: { Description : Description, Source : Source },
		dataType: "html"
	});
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
			tabindex = this.element.attr("tabindex");
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
				// Close if already visible
				if ( wasOpen ) {
				return;
				}
				input.select();
				// Pass empty string as value to search for, displaying all results
				input.autocomplete( "search", "" );
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
		var dialogIndex = $(".ui-dialog").css("z-index");
		var newIndex = parseInt(dialogIndex) + 1;
		$(this).autocomplete("widget").css({
            "width": ($(this).width() + "px !important"),
			"z-index":  newIndex
        });
    }
});