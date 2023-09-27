<?php
	include __DIR__ . "/DBConfig.php";
	include __DIR__ . "/GetSession.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>POS</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		
		
		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/font-awesome.css" rel="stylesheet" />
		<!--<link href="assets/css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" />-->
		<link href="assets/css/jquery-ui.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui.structure.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui.theme.css" rel="stylesheet" />
		<link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
		<link href="assets/css/custom.css" rel="stylesheet" />
		<link href="assets/css/bootstrap-float-label.min.css" rel="stylesheet" />
		<link href="assets/css/dataTables.bootstrap.css" rel="stylesheet" />
		<link href="assets/css/jquery.dataTables.css" rel="stylesheet" />
		<link href="assets/css/keyTable.bootstrap.css" rel="stylesheet" />
		<link href="assets/css/lobibox.css" rel="stylesheet" />
		<link href="assets/css/toggles.css" rel="stylesheet" />
		<link href="assets/css/toggles-modern.css" rel="stylesheet" />

		<link href="assets/css/menu.css" rel="stylesheet">
		
		<link rel="shortcut icon" href="./assets/img/logo.ico">
		<link rel="apple-touch-icon" sizes="57x57" href="./assets/img/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="./assets/img/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="./assets/img/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="./assets/img/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="./assets/img/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="./assets/img/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="./assets/img/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="./assets/img/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="./assets/img/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="./assets/img/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="./assets/img/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="./assets/img/favicon-16x16.png">
		<link rel="manifest" href="./assets/img/manifest.json">
		<meta name="msapplication-config" content="./assets/img/browserconfig.xml">
		<meta name="msapplication-TileImage" content="./assets/img/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">
	</head>
	<body>
		<div id="wrapper">
			<header style="height:30px;">
				<div class="container" style="display:inline-block; width: 70%;padding-right: 0" >
					<div class="navbar navbar-static-top">
						<div class="navigation">
							<nav>
								<ul class="nav topnav bold">
									<!--<li class='dropdown'>
										<a href='#' class='menu active-menu' link='./Home.php'><i class='fa fa-home fa-2'></i> Home</a>
									</li>-->
									<li class='dropdown'>
										<a href='#' id="menuSale" class='menu' link='Transaction/Sale/'><i class='fa fa-cart-plus fa-2'></i> Penjualan</a>
									</li>
									<li class='dropdown'>
										<a href='#' class='menu' link='Transaction/SaleReturn/'><i class='fa fa-undo fa-2'></i> Retur</a>
									</li>
									<li class='dropdown'>
										<a href='#' class='menu' link='Transaction/Booking/'><i class='fa fa-hourglass-start fa-2'></i> D.O</a>
									</li>
									<li class='dropdown'>
										<a href='#' class='menu' link='Transaction/Payment/'><i class='fa fa-dollar fa-2'></i> Pembayaran</a>
									</li>
									<li class='dropdown'>
										<a href='#' class='menu' link='Transaction/PickUp/'><i class='fa fa-download fa-2'></i> Pengambilan</a>
									</li>
								</ul>
							</nav>
						</div>
					</div>
				</div>
				<div class="container" style="display:inline-block;width: 28% !important;color:white;padding-right: 0" >
					<div class="navbar navbar-static-top" style="float:right;">
						<div class="navigation" style="margin-bottom: 7px;">
							<nav>
									Selamat Datang, <a href="#" style="color: white;font-size: 13px;" onclick="UpdatePassword();"><?php echo $_SESSION['NamaKasir']; ?>!</a>&nbsp;&nbsp;&nbsp;<a href="#" onclick="printDailyReport();"><img src="./assets/img/logout.png" width="15px" border="0" acronym title="Logout" /></a>
									<input type="hidden" id="hdnLogout" class="menu" link="./Logout.php" />
							</nav>
						</div>
					</div>
				</div>
			</header>
			<!--
			<nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0; height : 35px;">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#"></a>
				</div>
				<span id="Clock" style="color: white; padding: 5px 20px 0px 20px; float: left; font-size: 16px;"></span>
				<div style="color: white; padding: 5px 20px 0px 50px; float: right; font-size: 16px;"> 
					 Selamat Datang, <a href="#" style="color: white;font-size: 16px;" onclick="UpdatePassword();"><?php echo $_SESSION['Nama']; ?>!</a> &nbsp;&nbsp;&nbsp;<a href="#" class="menu" link="./Logout.php"><img src="./assets/img/logout.png" width="20px" border="0" acronym title="Logout" /></a>
				</div>
			</nav>   
			<!-- /. NAV TOP  -->
			<!--<nav class="navbar-default navbar-side" role="navigation">
				<div class="sidebar-collapse">
					<ul class="nav" id="main-menu">
						<li class="text-center" style="visibility:hidden;">
							<!--<img src="assets/img/find_user.png" class="user-image img-responsive"/>-->&nbsp;
						<!--</li>
						<li>
							<a href="#" onclick="Reload();"><i class="fa fa-refresh fa-3x"></i> Reload</a>
						</li>
						<li>
							<a class='menu active-menu' href='#' id='Menu1' link='./Home.php'><i class='fa fa-home fa-3x'></i> Home</a>
						</li>
						<li>
							<a class='menu' href='#' id='Menu1' link='./Home.php'><i class='fa fa-cart-plus fa-3x'></i> Penjualan</a>
						</li>
						<li>
							<a class='menu' href='#' id='Menu1' link='./Home.php'><i class='fa fa-home fa-3x'></i> Retur Penjualan</a>
						</li>
						<li>
							<a class='menu' href='#' id='Menu1' link='./Home.php'><i class='fa fa-home fa-3x'></i> Pemesanan</a>
						</li>
					</ul>
				</div>
			</nav>  
			<!-- /. NAV SIDE  -->
			
			<!--<div id="page-wrapper">
				
			</div>-->
			<div id="page-inner" style="overflow-x:hidden;overflow-y:hidden;">
				<img src="./assets/img/logo.png" style="width:40%;margin: 40px auto;display:block;"/>
			</div>
		</div>
		<div id="delete-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin ingin menghapusnya?</p>
		</div>
		<div id="save-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda yakin data yang diinput sudah benar?</p>
		</div>
		<div id="print-confirm" title="Konfirmasi" style="display: none;">
			<p><span class="ui-icon ui-icon-alert" style="float:left; margin:5px 12px 20px 0;"></span>Apakah anda ingin mencetak laporan harian?</p>
		</div>
		<div id="update-password" title="Ganti Password" style="display: none;">
			<form class="col-md-12" id="UpdatePasswordForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-5 labelColumn">
						Password Lama :
					</div>
					<div class="col-md-6">
						<input tabindex=1 id="txtCurrentPassword" name="txtCurrentPassword" type="password" class="form-control-custom" placeholder="Password Lama" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Password Baru :
					</div>
					<div class="col-md-6">
						<input tabindex=2 id="txtNewPassword" name="txtNewPassword" type="password" class="form-control-custom" placeholder="Password Baru" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-md-5 labelColumn">
						Konfirmasi Password :
					</div>
					<div class="col-md-6">
						<input tabindex=3 id="txtConfirmNewPassword" name="txtConfirmNewPassword" type="password" class="form-control-custom" placeholder="Konfirmasi Password" />
					</div>
				</div>
			</form>
		</div>
		<div id="divModal"></div>
		<div id="first-balance" title="Saldo Awal" style="display: none;">
			<form class="col-md-12" id="FirstBalanceForm" method="POST" action="" >
				<div class="row">
					<div class="col-md-5 labelColumn">
						Saldo Awal :
					</div>
					<div class="col-md-6">
						<input id="txtFirstBalance" tabindex=50; name="txtFirstBalance" type="text" class="form-control-custom text-right mousetrap" placeholder="Saldo Awal" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value);this.select();" onblur="convertRupiah(this.id, this.value);" autocomplete="off" onpaste="return false;" value="0.00" />
					</div>
				</div>
				<br />
			</form>
		</div>
		<!-- Sliding div starts here -->
		<!--<div id="slider" style="right:-300px;">
			<div id="sidebar" onclick="open_panel()"><img src="assets/img/btnShortcutInfo.png"></button></div>
			<div id="header">
				<ul>
					<li>Double Klik/Enter: Edit.</li>
					<li>INSERT: Tambah Data.</li>
					<li>DELETE: Delete.</li>
				</ul>
			</div>
		</div>-->
		<!-- Sliding div ends here -->
		<!--<script src="assets/js/jquery-1.10.2.js"></script>
		<script src="assets/js/jquery-ui-1.10.3.custom.js"></script>-->
		<script src="assets/js/jquery-1.12.4.js"></script>
		<script src="assets/js/bootstrap.js"></script>
		<script src="assets/js/jquery-ui.js"></script>
		<script src="assets/js/jquery.metisMenu.js"></script>
		<script src="assets/js/custom.js"></script>
		<script src="assets/js/notify.js"></script>
		<script src="assets/js/lobibox.js"></script>
		<script src="assets/js/global.js"></script>
		<script src="assets/js/jquery.dataTables.js"></script>
		<script src="assets/js/dataTables.bootstrap.js"></script>
		<script src="assets/js/dataTables.keyTable.js"></script>
		<script src="assets/js/toggles.js"></script>
		<script src="assets/js/mousetrap.min.js"></script>
		<script src="assets/js/jquery.simulate.js"></script>
		<div id="loading"></div>
		<iframe id='excelDownload' src='' style='display:none'></iframe>
		<script type="text/javascript">
			function firstBalance() {
				$("#first-balance").dialog({
					autoOpen: false,
					open: function() {
						$("#divModal").show();
						$(document).on('keydown', function(e) {
							if (e.keyCode == 39 && $("input:focus").length == 0) { //right arrow
								 $("#btnCancelFirstBalance").focus();
							}
							else if (e.keyCode == 37 && $("input:focus").length == 0) { //left arrow
								 $("#btnSaveFirstBalance").focus();
							}
						});
						setTimeout(function() {
							$("#txtFirstBalance").focus();
						}, 0);
						
					},
					
					close: function() {
						$(this).dialog("destroy");
						$("#divModal").hide();
					},
					resizable: false,
					height: 150,
					width: 400,
					modal: false,
					buttons: [
					{
						text: "Simpan",
						tabindex: 51,
						id: "btnSaveFirstBalance",
						click: function() {
							$.ajax({
								url: "./InsertFirstBalance.php",
								type: "POST",
								data: $("#FirstBalanceForm").serialize(),
								dataType: "json",
								success: function(data) {
									$("#loading").hide();
									if(data.FailedFlag == '0') {
										//$.notify(data.Message, "success");
										$("#first-balance").dialog("destroy");
										$("#txtFirstBalance").val("0.00");
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
									LogEvent(errorMessage, "Home.php (fnFirstBalance)");
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
						text: "Tutup",
						tabindex: 52,
						id: "btnCancelFirstBalance",
						click: function() {
							$(this).dialog("destroy");
							$("#divModal").hide();
							return false;
						}
					}]
				}).dialog("open");
			}

			$(document).ready(function() {
				var windowHeight = $( window ).height() - 45;
				$("#page-inner").css ({
					"min-height" : windowHeight,
					"max-height" : windowHeight
				});
				$(".panel-default").css ({
					"min-height" : windowHeight
				});
				$("head").append("<style> .panel-default { min-height : " + windowHeight + "px; } .panel-body { overflow-y:auto;min-height : " + (windowHeight - 30) + "px; max-height : " + (windowHeight - 30) + "px } </style>");
				$("#wrapper").css ({
					"width" : "calc(100% - 5px)"
				});
				$( window ).resize(function() {
					windowHeight = $( window ).height() - 55;
					$("#page-inner").css ({
						"min-height" : windowHeight,
						"max-height" : windowHeight
					});

					$(".panel-default").css ({
						"min-height" : windowHeight
					});
					$("#wrapper").css ({
						"width" : "calc(100% - 5px)"
					});
				});

				$.ajax({
					url: "./FirstBalance.php",
					type: "POST",
					data: { },
					dataType: "json",
					success: function(Data) {
						if(Data.FailedFlag == '0') {
							if(Data.IsFilled == 0) firstBalance();
						}
						else {
							var counter = 0;
							Lobibox.alert("error",
							{
								msg: "Gagal memuat data",
								width: 480,
								beforeClose: function() {
									if(counter == 0) {
										setTimeout(function() {
											//$("#txtItemCode").focus();
										}, 0);
										counter = 1;
									}
								}
							});
							return 0;
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$("#loading").hide();
						var errorMessage = "Error : (" + jqXHR.status + " " + errorThrown + ")";
						LogEvent(errorMessage, "/Home.php");
						Lobibox.alert("error",
						{
							msg: errorMessage,
							width: 480
						});
						return 0;
					}
				});

				$("#menuSale").click();
			});
		</script>
	</body>
</html>
